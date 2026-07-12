<?php

require_once __DIR__ . '/../../lan/lan.php';
require_once __DIR__ . '/../../inc/header.php';

$summary = new Lan();
mihiway_handle_syslang_post($summary);
?>
<!DOCTYPE html>
<html>
	<head>
		<script src="../../theme.js"></script>
		<link rel="stylesheet" href="../../theme.css">
	</head>
	<body>
		<?php mihiway_render_header($summary, ['rootPrefix' => '../../', 'showMenu' => true, 'showLogo' => false, 'showAccount' => true]); ?>
		<h1>Bestellübersicht</h1>
		<?php mihiway_render_footer($summary, ['rootPrefix' => '../../']); ?>
	</body>
</html>