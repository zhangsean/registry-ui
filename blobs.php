<?php
include_once 'common.php';

$image = empty($_GET['image']) ? '' : $_GET['image'];
$tag = empty($_GET['tag']) ? '' : $_GET['tag'];
$digest = empty($_GET['digest']) ? '' : $_GET['digest'];
if (empty($image) || empty($digest)) {
	echo "Image name and digest must be set.";
	return;
}
echo "<h1>Docker Registry UI</h1>";
echo "<b>Image</b><br/><a href='/'>$registryWeb</a>/<a href='/tags.php?image=$image'>$image</a>:<a href='/manifests.php?image=$image&tag=$tag'>$tag</a>";
echo "<br/><b>Layer</b><br/>$digest";
echo "<h3>Image Blob</h3><hr/>";
echo "<table border=0 cellspacing=0 cellpadding=0>";
echo "Blob Size: ".size(getBlobSize($image, $digest));
?>
