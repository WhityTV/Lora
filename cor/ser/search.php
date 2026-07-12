<?php

require_once __DIR__ . '/../lan/lan.php';
require_once __DIR__ . '/../inc/header.php';

$search = new Lan();

mihiway_handle_syslang_post($search);
?>
<!DOCTYPE html>
<html>
	<head>
		<script src="../theme.js"></script>
		<link rel="stylesheet" href="../theme.css">
	</head>
	<body>
		<?php mihiway_render_header($search, ['rootPrefix' => '../', 'showMenu' => true, 'showLogo' => false, 'showAccount' => true]); ?>
		<h1> Search Results </h1>
		<?php mihiway_render_footer($search, ['rootPrefix' => '../']); ?>
	</body>
</html>