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

<html>
	<head>
		<link rel="stylesheet" href="../theme.css">
		<script src="../theme.js"></script>
	</head>
	<body>
		<h1>Einstellungen</h1>
	</body>
</html>