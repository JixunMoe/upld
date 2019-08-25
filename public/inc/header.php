<?php

if (!defined('IN_SCRIPT'))
{
	header('location: ../index.php');
	exit;
}

?>

<!DOCTYPE html>
<html>
<head>
	<title><?= SITE_NAME ?></title>
	<link href="css/upload.css" rel="stylesheet" type="text/css" />
</head>

<body class="no-js">

	<div id="header">

		<ul id="navbar">

			<!-- need to use a dirty hack here to avoid whitespace in between navbar buttons  -->

			<li><a href="index.php">upload</a></li><?php

if (isset($_SESSION['user']))
{

?><li><a href="account.php">my account</a></li><li><a href="logout.php?csrf=<?= get_csrf() ?>">logout</a></li>

<?php

}
else
{

?><li><a href="login.php">login</a></li><li><a href="register.php">register</a></li><?php

}

?>

		</ul>

		<div id="logo"><?= SITE_NAME ?></div>

	</div>

	<div id="main">
