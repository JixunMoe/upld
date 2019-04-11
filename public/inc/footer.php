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

<?php if (defined('INC_UPLOAD_SCRIPT')) { ?>
	<script src="js/jquery.min.js"></script>
	<script src="js/jquery.ui.widget.js"></script>
	<script src="js/load-image.all.min.js"></script>
	<script src="js/jquery.fileupload.js"></script>
	<script src="js/jquery.fileupload-process.js"></script>
	<script src="js/jquery.fileupload-image.js"></script>
	<script src="js/jquery.fileupload-validate.js"></script>
	<script>
		var max_upload = <?= ALLOWED_SIZE ?>;
	</script>
	<script src="js/upload.js"></script>
<?php } ?>

<script>
$(function () {
	$('body').toggleClass('no-js js');
});
</script>

</body>
</html>