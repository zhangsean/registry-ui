<?php
include_once 'common.php';

$image = empty($_GET['image']) ? '' : $_GET['image'];
$tag = empty($_GET['tag']) ? '' : $_GET['tag'];
if (empty($image) || empty($tag)) {
	echo "Image name and tag must be set.";
	return;
}
$url = "/$image/manifests/$tag";
$json = httpGet($url);
if (isset($json['errors'])) {
	echo $json['errors'][0]['message'];
	return;
}
echo "<h1>Tiny Registry UI</h1>";
echo "<b>Image</b><br/>$registryWeb/".$json['name'].":$tag";
echo "<h3>Layers</h3><hr/>";
echo "<table border=0 cellspacing=0 cellpadding=0>";
echo "<tr><th width=50>Layer</th><th width=150>Image ID</th><th width=100>Size</th><th width=200>Created</th><th width=100>Author</th><th width=1000>Cmd</th></tr>";
foreach ($json['history'] as $key => $info) {
	echo "<tr><td>$key</td>";
	if (isset($info['v1Compatibility'])) {
		$infoV1 = json_decode($info['v1Compatibility'], true);
		echo "<td><a href='blobs.php?image=$image&tag=$tag&digest=".$json['fsLayers'][$key]['blobSum']."'>".substr($infoV1['id'], 0, 12)."</a></td>";
		echo "<td>".size(getBlobSize($image, $json['fsLayers'][$key]['blobSum']))."</td>";
		echo "<td>".date('Y-m-d H:i:s', strtotime(explode('.', $infoV1['created'])[0]))."</td>";
		echo isset($infoV1['author']) ? "<td>".$infoV1['author']."</td>" : "<td>&nbsp;</td>";
		echo "<td>";
		foreach ($infoV1['container_config']['Cmd'] as $cmd) {
			echo "$cmd<br/>";
		}
		echo "</td>";
	}
}
echo "</table>";
?>