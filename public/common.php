<?php

session_start();

$start = microtime();

if (empty($_SESSION['csrf'])) {
	$_SESSION['csrf'] = bin2hex(random_bytes(16));
}


$db_queries = 0;

define('MAIN_SITE_URL', trim(SITE_URL, '/') . '/');

define('MAIN_SCRIPT_PATH', (SCRIPT_PATH ? trim(SCRIPT_PATH, '/') . '/' : ''));

define('VIEW_PATH', (FRIENDLY_URLS ? '' : 'view.php?id='));

define('VIEW_URL', 'http://' . MAIN_SITE_URL . MAIN_SCRIPT_PATH . VIEW_PATH);

define('IMAGES_URL', 'http://' . (FRIENDLY_URLS ? 'i.' : '') . MAIN_SITE_URL . (FRIENDLY_URLS ? '' : MAIN_SCRIPT_PATH . 'images/'));

define('IN_SCRIPT', true);

function exit_message($message)
{
	require('inc/header.php');
	require('inc/message.php');
	require('inc/footer.php');
	exit;
}

function get_csrf() {
	return $_SESSION['csrf'];
}

function validate_csrf() {
	if (!empty($_POST['csrf']) && $_SESSION['csrf'] === $_POST['csrf']) {
		// ok: csrf token validated in POST body
		return;
	}
	if (!empty($_GET['csrf']) && $_SESSION['csrf'] === $_GET['csrf']) {
		// ok: csrf token validated in GET query
		return;
	}

	exit_message('Oops: Your request does not contain a valid CSRF token.');
	exit;
}

function input_csrf() {
	?>
	<input type="hidden" name="csrf" value="<?= get_csrf() ?>" >
	<?php
}
