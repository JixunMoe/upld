<?php

if (!defined('IN_SCRIPT'))
{
	header('location: ../index.php');
	exit;
}

?>

	</div>

	<div id="footer">
		<a href="faq.php">FAQ</a>|<a href="tc.php">Terms &amp; Conditions</a>|<a href="contact.php">Contact</a>
		<!-- page generated in <?= round((microtime(true) - $start), 5) ?> seconds with <?= $db_queries ?> DB quer<?= ($db_queries === 1 ? 'y' : 'ies') ?> -->
	</div>

	<script src="js/jquery.min.js"></script>	
	<script src="js/upload.js"></script>

</body>
</html>