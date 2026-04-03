<?php

class Functions {
    private $mysqli;
    private $DB_HOST = '127.0.0.1';
    private $DB_USER = 'root';
    private $DB_PASS = '';
    private $DB_NAME = 'lora';


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
}