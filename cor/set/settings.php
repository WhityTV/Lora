<?php

require_once __DIR__ . '/../lan/lan.php';
require_once __DIR__ . '/../inc/header.php';

$settings = new Lan();

mihiway_handle_syslang_post($settings);

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
		<script src="../theme.js"></script>
		<link rel="stylesheet" href="../theme.css">
	</head>
	<body>
		<?php mihiway_render_header($settings, ['rootPrefix' => '../', 'showMenu' => true, 'showLogo' => false, 'showAccount' => true]); ?>
		<h1>Einstellungen</h1>
		<?php mihiway_render_footer($settings, ['rootPrefix' => '../']); ?>
	</body>
</html>