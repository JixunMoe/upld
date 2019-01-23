<?php

if (!defined('IN_SCRIPT'))
{
	header('location: ../index.php');
	exit;
}

?>

<div class="box">

	<p class="title">Register</p>

	<p>You can register an account which will allow you to keep track of your uploaded images</p>

	<form name="register" method="POST" action="register.php">
		<input name="email" type="text" placeholder="email..." />
		<input name="password" type="password" placeholder="password... (8 characters minimum)" />
		<?php input_csrf(); ?>
		<input name="submit" type="submit" value="Register" />
	</form>

</div>
