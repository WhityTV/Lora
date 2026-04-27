<?php

class Functions {
    private $mysqli;
    private $DB_HOST = '127.0.0.1';
    private $DB_USER = 'root';
    private $DB_PASS = '';
    private $DB_NAME = 'mihiway';


    public function __construct() {
        mysqli_report(MYSQLI_REPORT_OFF);
        $this->mysqli = @new mysqli($this->DB_HOST, $this->DB_USER, $this->DB_PASS, $this->DB_NAME);
        if ($this->mysqli->connect_errno) {
            error_log('Connection failed: ' . $this->mysqli->connect_error);
            exit;
        }
        $this->mysqli->set_charset("utf8mb4");
    }

    
    public function qry(string $sql) {
        $res = $this->mysqli->query($sql);
        if ($res === false) {
            error_log('SQL Error: ' . $this->mysqli->error . ' | SQL: ' . $sql);
        }
        return $res;
    }

    public function esc(string $value): string {
        return $this->mysqli->real_escape_string($value);
    }

    private function normalizePasswordForBlacklist(string $password): string {
        return mb_strtolower(trim($password), 'UTF-8');
    }

    private function getCommonBlackListedPasswords(): array {
        return [
            'passwordpassword',
            'passwortpasswort',
            '123456789012345',
            '123451234512345',
            'qwertyuiopasdfgh',
            'asdfghjklqwertyu',
            'iloveyouiloveyou',
            'letmeinletmein1',
            'adminadminadmin',
            'welcomewelcome1',
            'willkommen123456',
            'changemechangeme',
            'supersecure12345',
            'p0sw0rt1234567!',
            'password1234567!',
            'qwerty1234567890',
            'zaq12wsx34edc56r',
            '1q2w3e4r5t6y7u8i',
            'abcdefg123456789',
            'monkeymonkey1234',
            'dragondragon1234',
            'footballfootball',
            'sunshine12345678',
            'halloichbin12345',
            'passwort12345678',
        ];
    }

    private function isInHibpPwnedPasswords(string $password): bool {
        static $cache = [];

        $sha1 = strtoupper(sha1($password));
        if (isset($cache[$sha1])) {
            return $cache[$sha1];
        }

        $prefix = substr($sha1, 0, 5);
        $suffix = substr($sha1, 5);
        $url = 'https://api.pwnedpasswords.com/range/' . $prefix;

        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: mihiway-password-check\r\nAdd-Padding: true\r\n",
                'timeout' => 4,
            ],
        ]);

        $response = @file_get_contents($url, false, $context);
        if ($response === false) {
            $cache[$sha1] = false;
            return false;
        }

        foreach (explode("\n", $response) as $line) {
            $parts = explode(':', trim($line));
            if (count($parts) < 2) {
                continue;
            }

            if (strtoupper($parts[0]) === $suffix) {
                $cache[$sha1] = true;
                return true;
            }
        }

        $cache[$sha1] = false;
        return false;
    }

    public function isCommonOrPwnedBlackListedPassword(string $password): bool {
        $normalized = $this->normalizePasswordForBlacklist($password);
        if ($normalized === '') {
            return false;
        }

        if (in_array($normalized, $this->getCommonBlackListedPasswords(), true)) {
            return true;
        }

        return $this->isInHibpPwnedPasswords($password);
    }

    public function containsPersonalInfoInPassword(string $password, array $contextWords = []): bool {
        if (trim($password) === '') {
            return false;
        }

        foreach ($contextWords as $word) {
            $wordTrimmed = trim((string) $word);
            if ($wordTrimmed !== '' && mb_stripos($password, $wordTrimmed, 0, 'UTF-8') !== false) {
                return true;
            }
        }

        return false;
    }

    public function hasWeakRepetitionPattern(string $password): bool {
        $trimmed = trim($password);
        if ($trimmed === '') {
            return false;
        }

        $length = mb_strlen($trimmed, 'UTF-8');
        if ($length < 15) {
            return false;
        }

        $chars = preg_split('//u', $trimmed, -1, PREG_SPLIT_NO_EMPTY);
        if (!is_array($chars) || count($chars) === 0) {
            return false;
        }

        $frequencies = array_count_values($chars);
        $maxCount = max($frequencies);
        $dominantShare = $maxCount / $length;

        if ($dominantShare >= 0.70) {
            return true;
        }

        if (count($frequencies) <= 2) {
            return true;
        }

        return false;
    }
}