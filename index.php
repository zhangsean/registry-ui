<?php
include_once 'common.php';

$url = "/_catalog";
$query = array();
if (!empty($_GET['last'])) {
	$query[] = "last=". $_GET['last'];
}
if (!empty($_GET['n'])) {
	$query[] = "n=". $_GET['n'];
}
if (!empty($query)) {
	$url .= '?' . implode($query, '&');
}
$json = httpGet($url, 'Link');
$link = substr($headerStr, strpos($headerStr, '?') + 1, strpos($headerStr, '>') - strpos($headerStr, '?') - 1);
if (empty($json['repositories'])) {
	echo "Empty repository";
	return;
}
echo "<h1>Registry Web</h1>";
echo "<b>Registry</b><br/>".$registryWeb."</h4>";
echo "<h2>Images</h2><hr/>";
if (!empty($link)) {
	echo "<a href='?$link'>Next Page</a><br/><br/>";
}
echo "<table>";
echo "<tr><th width=500>Repository</th><th width=100>Tags</th><th width=100>Size</th></tr>";
foreach ($json['repositories'] as $img) {
	echo "<tr><td><a href='tags.php?image=$img'>".$img."</a></td>";
	$repInfo = getRepInfo($img);
	echo "<td>".$repInfo['Tags']."</td>";
	echo "<td>".size($repInfo['Size'])."</td>";
	echo "</tr>";
}
echo "</table>";
?>