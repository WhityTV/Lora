<?php

require_once __DIR__ . '/../lan/lan.php';
require_once __DIR__ . '/../inc/header.php';

$help = new Lan();

mihiway_handle_syslang_post($help);

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

?>

<html>
	<head>
		<script src="../theme.js"></script>
		<link rel="stylesheet" href="../theme.css">
	</head>
	<body>
		<?php mihiway_render_header($help, ['rootPrefix' => '../', 'showMenu' => true, 'showLogo' => false, 'showAccount' => true]); ?>
		<h1>Hilfe</h1>
		<p>Tut mir leid dir ist nicht mehr zu helfen. Bitte wende dich an einen Arzt oder Apotheker. Nur wenn es wirklich nicht anders geht - unten findest du Kontaktdaten vom Entwickler/Support.</p>
		<?php mihiway_render_footer($help, ['rootPrefix' => '../']); ?>
	</body>
</html>