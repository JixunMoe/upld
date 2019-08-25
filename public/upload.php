<?php

require('config.php');
require('common.php');

validate_csrf();

$url = empty($_POST['url']) ? null : $_POST['url'];

// see if anonymous uploads has been disabled, and check if the user is logged in
if (ANON_UPLOADS === false && !isset($_SESSION['user']))
{
	exit_message('Anonymous uploads have been disabled, please register or log in to upload');
}

// both image and url submitted. wtf, let's get the hell out of here!
if (!empty($_FILES['image']) && !is_null($url))
{
	exit_message('Please only choose one image to upload.');
}

// neither submitted - inform user and exit
if (empty($_FILES['image']) && is_null($url))
{
	exit_message('Please choose either an image on your computer to upload or a remote image to download.');
}

$allowed_ext = [
	'png',
	'jpg',
	'gif'
];

require('db.php');

function process_upload($index, $url) {
	global $allowed_ext;
	global $db_queries;
	global $db;

	// user must have submitted either an image or URL
	// check which one and make sure it's valid
	// check for an uploaded image first
	if (!is_null($index))
	{
		// user wants to upload via browser
		// set variables - will check after
		$size = $_FILES['image']['size'][$index];
		$ext = pathinfo($_FILES['image']['name'][$index], PATHINFO_EXTENSION);
	}

	elseif (!is_null($url))
	{
		// user wants to download a remote image
		// make sure URL is valid and set variables - will check after
		// is remote downloading enabled in conf.php?
		if (ALLOW_REMOTE !== true)
		{
			// remote downloading is disabled - error and exit
			exit_message('Remote downloading is not enabled on this installation');
		}

		// allowed URL schemes
		$allowed_schemes = [
			'http',
			'https'
		];

		// check if URL is valid and http/https only
		if (!filter_var($url, FILTER_VALIDATE_URL) || (!in_array(parse_url($url, PHP_URL_SCHEME), $allowed_schemes)))
		{
			// not a valid URL
			exit_message('Sorry, this URL ' . $url . ' is invalid');
		}

		// if whitelisting is enabled, make sure it's an allowed domain
		if ((URL_WHITELIST === true) && (!in_array(parse_url($url, PHP_URL_HOST), $allowed_urls)))
		{
			exit_message('Sorry, downloads from this domain have not been allowed by the administrator');
		}

		// looks good so far, download the image and make sure it's valid
		$size = get_headers($url, 1)['Content-Length'];
		$arr = explode('.',$url);
		$ext = end($arr);
	}

	// OK, everything checks out so far
	// check size/ext
	if ($size > ALLOWED_SIZE)
	{
		// file is too big
		exit_message('Sorry, this file is too big');
	}

	// size is OK, make sure EXT is allowed
	if (!in_array($ext, $allowed_ext))
	{
		// ext not allowed
		exit_message('Sorry, this extension is not allowed.');
	}

	// size and ext are fine
	// let's set $image to either $_FILES['image'] or $url a[$index]nd check if they're valid
	if (!is_null($index))
	{
		$image = $_FILES['image']['tmp_name'][$index];
		if (!$image) {
			// image too large.
			return array(null, null);
		}

		if (!getimagesize($image))
		{
			exit_message('Sorry, this does not appear to be a valid image: ' . $_FILES['image']['name'][$index]);
		}
	}

	elseif (!is_null($url))
	{
		$image = file_get_contents($url, NULL, NULL, NULL, $size);

		if (!imagecreatefromstring($image))
		{
			exit_message('Sorry, this does not appear to be a valid image');
		}
	}

	// everything looks good so far! images are valid, size and ext check out
	// generate an ID, move files and insert into DB

	// generate ID (and make sure it doesn't exist)
	// prepare query
	$exists = mysqli_prepare($db, 'SELECT EXISTS(SELECT 1 FROM `images` WHERE `id` = ?)');

	// create ID and check if it exists in the DB
	do
	{
		// create ID
		$id = '';
		$chars = 'ACDEFHJKLMNPQRTUVWXYZabcdefghijkmnopqrstuvwxyz23479';
		for ($i = 0; $i < IMAGE_ID_LEN; ++$i)
		{
			$id .= $chars[mt_rand(0, 50)];
		}
		// $id is now set to a randomly generated ID

		// query DB to see if ID exists
		mysqli_stmt_bind_param($exists, "s", $id);
		mysqli_stmt_execute($exists);
		++$db_queries;
		mysqli_stmt_bind_result($exists, $result);
		mysqli_stmt_fetch($exists);
		mysqli_stmt_close($exists);
	}
	while ($result === 1);

	// write image (this is different depending on whether it's an upload or remote download)
	if (!is_null($index))
	{
		$image_path = 'images/' . $id . '.' . $ext;

		// write image
		move_uploaded_file($image, $image_path);
	}
	else if (!is_null($url))
	{
		// write image
		file_put_contents('images/' . $id . '.' . $ext, $image);
	}

	// create thumbnail (only bother if user is logged in)
	if (isset($_SESSION['user']))
	{
		if (!is_null($index))
		{
			// set source for thumb
			switch ($ext)
			{
				case 'jpg':
					$thumb = imagecreatefromjpeg($image_path);
				break;

				case 'png':
					$thumb = imagecreatefrompng($image_path);
				break;

				case 'gif':
					$thumb = imagecreatefromgif($image_path);
				break;
			}
		}
		else if (!is_null($url))
		{
			// set source for thumb
			$thumb = imagecreatefromstring($image);
		}

		$width = imagesx($thumb);
		$height = imagesy($thumb);

		if ($width > 200 || $height > 200)
		{
			if ($width > $height)
			{
				$new_width = 200;
				// if image height is below 300, don't bother resizing
				$new_height = floor($height * ($new_width / $width));
			}
			else
			{
				$new_height = 200;
				// if image width is below 300, don't bother resizing
				$new_width = floor($width * ($new_height / $height));
			}
		}
		else
		{
			$new_height = $height;
			$new_width = $width;
		}

		$new_thumb = imagecreatetruecolor($new_width, $new_height);

		switch ($ext)
		{
			case 'png':
				imagefill($new_thumb, 0, 0, imagecolorallocate($new_thumb, 255, 255, 255));
				imagealphablending($new_thumb, TRUE);
			break;

			case 'gif':
				$new_thumb = imagecolorallocate($thumb, 0, 0, 0);
				imagecolortransparent($thumb, $new_thumb);
			break;
		}

		imagecopyresized($new_thumb, $thumb, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		imagedestroy($thumb);

		imagejpeg($new_thumb, 'thumbs/' . $id . '.jpg', 30);
		imagedestroy($new_thumb);
	}

	// check if user is logged in or not and write info to DB
	if (!isset($_SESSION['user']))
	{
		$query = mysqli_prepare($db, 'INSERT INTO `images` (`id`, `ext`, `ip`) VALUES (?, ?, ?)');
		mysqli_stmt_bind_param($query, 'sss', $id, $ext, $ip);
	}
	else
	{
		$query = mysqli_prepare($db, 'INSERT INTO `images` (`id`, `ext`, `user`, `ip`) VALUES (?, ?, ?, ?)');
		mysqli_stmt_bind_param($query, 'ssis', $id, $ext, $user, $ip);
	}

	// set data for query
	$user = $_SESSION['user'];
	$ip = BEHIND_CF ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];

	// insert data
	mysqli_stmt_execute($query);
	++$db_queries;
	mysqli_stmt_close($query);

	return array($id, $ext);
}

$size = empty($_FILES['image']['name'][0]) ? 0 : count($_FILES['image']['name']);
$multiple = $size > 0;

$results = array();

function do_upload($index, $url) {
	global $results;

	list($id, $ext) = process_upload($index, $url);
	$results[] = array(
		'thumbnailUrl' => CACHE_URL . "/${id}.jpg",
		'name' => "${id}.${ext}",
		'url' => IMAGE_URL . "/${id}.${ext}",
		'deleteType' => "DELETE",
		'type' => "image/jpeg",
		'deleteUrl' => SITE_URL . '/delete.php?id=' . $id . '&csrf=' . get_csrf(),
		'size' => 1,
	);
	
	return $id;
}

if ($size > 0) {
	// File upload
	for($i = 0; $i < $size; $i++) {
		$id = do_upload($i, null);
	}
} else {
	// remote upload
	$urls = explode("\n", $url);
	
	foreach ($urls as $u) {
		$id = do_upload(null, trim($u));
	}
}

// close connection
mysqli_close($db);

if (!empty($_POST['ajax']))
{
	header('Content-Type: application/json');
	echo json_encode(array('files' => $results));
}
else if ($multiple)
{
	// TODO: Print $results data.
	header('location: ' . SITE_URL . '/account.php');
}
else
{
	header('location: ' . VIEW_URL . $id);
}
