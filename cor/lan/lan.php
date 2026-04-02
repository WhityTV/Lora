<?php

require_once __DIR__ . '/../fct/fct.php';

if (session_status() !== PHP_SESSION_ACTIVE) session_start();

class Lan extends Functions {
    public function __construct() {
        parent::__construct();
    }

    public function getSysLan() {
        return $_SESSION['syslan'] ?? 'DE';
    }

    public function setSysLan($syslan) {
        $_SESSION['syslan'] = $syslan;
    }

    public function getLan($code) {
        $syslan = $this->getSysLan();
        if ($syslan == 'DE') {
            $sql = "SELECT val_de FROM lan_variables WHERE code = '{$code}';";
            $lan_val_de = $this->qry($sql);
            $row = mysqli_fetch_assoc($lan_val_de);
            return $row['val_de'];
        } elseif ($syslan == 'EN') {
            $sql = "SELECT val_en FROM lan_variables WHERE code = '{$code}';";
            $lan_val_en = $this->qry($sql);
            $row = mysqli_fetch_assoc($lan_val_en);
            return $row['val_en'];
        }
    }

    // ToDo: Implement setLan function to update or add language values in the database
    public function setLan($lan, $lan_val_de, $lan_val_en) {
       
    }
}
