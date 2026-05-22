<?php

require_once __DIR__ . '/../lan/lan.php';

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

if (!isset($_SESSION['userId'])) {
	header('Location: ../log/login.php');
	exit;
}
?>

<h1> Last Articles </h1>