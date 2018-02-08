<?php
include_once 'common.php';
include_once 'config.php';

$image = $_GET['image'];
if (empty($image)) {
	echo "Image name must be set.";
	return;
}
$url = "/$image/tags/list";
$json = httpGet($url);
if (isset($json['errors'])) {
	echo $json['errors'][0]['message'];
	return;
}
echo "<h1>Registry Web</h1>";
echo "<b>Repository</b><br/>$registryWeb/".$json['name'];
echo "<h3>Tags</h3><hr/>";
echo "<table border=0 cellspacing=0 cellpadding=0>";
echo "<tr><th width=100>Tag</th><th width=80>Layers</th><th width=100>Size</th><th width=100>Created</th></tr>";
foreach ($json['tags'] as $tag) {
	echo "<tr><td><a href='manifests.php?image=$image&tag=$tag'>".$tag."</a></td>";
	$tagInfo = getTagInfo($image, $tag);
	echo "<td>".$tagInfo['Layeys']."</td>";
	echo "<td>".size($tagInfo['Size'])."</td>";
	echo "<td>".$tagInfo['Created']."</td>";
	echo "</tr>";
}
echo "</table>";
?>