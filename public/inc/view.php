<?php

if (!defined('IN_SCRIPT'))
{
	header('location: ../index.php');
	exit;
}

?>

<div id="sidebar">

	<ul id="links" class="box sidebar-box">
		<li>preview link (email &amp; chat)</li>
		<li><input type="text" value="<?= VIEW_URL . $id ?>" readonly /></li>
		<li>direct link (websites &amp; backgrounds)</li>
		<li><input type="text" value="<?= IMAGES_URL . $id . '.' . $ext ?>" readonly /></li>
		<li>markdown code (websites)</li>
		<li><input type="text" size="25" value="![Image](<?= IMAGES_URL . $id . '.' . $ext ?>)" readonly /></li>
		<li>html code (websites)</li>
		<li><input type="text" size="25" value="<img src=&#34;<?= IMAGES_URL . $id . '.' . $ext ?>&#34; alt=&#34;<?= $id ?>&#34; />" readonly /></li>
		<li>bb code (forums)</li>
		<li><input type="text" size="25" value="[img]<?= IMAGES_URL . $id . '.' . $ext ?>[/img]" readonly /></li>
		<li>linked bb code (forums)</li>
		<li><input type="text" size="25" value="[url=<?= VIEW_URL . $id ?>][img]<?= IMAGES_URL . $id . '.' . $ext ?>[/img][/url]" readonly /></li>
	</ul>

	<ul id="info" class="box sidebar-box">
		<li>image ID: <?= $id ?></li>
		<li>image dimensions: <?= $dimensions[0] . 'x' . $dimensions[1] ?></li>
		<li>image size: <?= ($size > 1024 ? round(($size / 1024), 1) . 'MB' : round($size, 1) . 'KB' ) ?></li>
		<li>image type: <?= $ext ?></li>

<?php

if (is_admin())
{

?>

		<li>upload time: <?= $time ?></li>
		<li>uploader IP: <?= $ip ?></li>

<?php

}

?>

	</ul>

	<ul id="report">

<?php

if (is_admin() || (isset($_SESSION['user']) && ($_SESSION['user'] === $user)))
{

?>

		<li><a class="delete" href="delete.php?id=<?= $id ?>&csrf=<?= get_csrf() ?>">DELETE this image</a></li>

<?php

}

else
{

?>

		<li><a href="report.php?id=<?= $id ?>&csrf=<?= get_csrf() ?>">report this image</a></li>

<?php

}

if (is_admin())
{

?>

		<li><a id="ban" href="ban.php?id=<?= $user ?>&csrf=<?= get_csrf() ?>">BAN user and DELETE ALL IMAGES</a></li>

<?php

}

?>

	</ul>

</div>

<div id="image" class="box">
	<img src="<?= IMAGES_URL . $id . '.' . $ext ?>" />
</div>

