<?php
error_reporting(0);
set_time_limit(100);

$registryWeb = empty($_ENV['REGISTRY_WEB']) ? 'localhost:5000' : $_ENV['REGISTRY_WEB'];
$registryAPI = empty($_ENV['REGISTRY_API']) ? 'http://localhost:5000/v2' : $_ENV['REGISTRY_API'];
$showImageSize = empty($_ENV['SHOW_IMAGE_SIZE']) ? 0 : $_ENV['SHOW_IMAGE_SIZE'];

$headerStr;
$headerKey;
function httpGet($url, $headKey = '', $headerOnly = false) {
    global $registryAPI, $headerKey;
    $headerKey = $headKey;
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $registryAPI.$url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_POST, false);
    curl_setopt($curl, CURLINFO_HEADER_OUT, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    if (!empty($headerKey)) {
        curl_setopt($curl, CURLOPT_HEADERFUNCTION, "curlHeaderCallback");
    }
    if ($headerOnly) {
        curl_setopt($curl, CURLOPT_NOBODY, true);
    }
    $res = curl_exec($curl);
    $errorno = curl_errno($curl);
    curl_close($curl);
    if ($errorno) {
        return array('error' => true, 'msg' => $errorno);
    }
    return json_decode($res, true);
}
function curlHeaderCallback($curl, $strHeader) {
    global $headerKey, $headerStr;
    if (preg_match("/^$headerKey/i", $strHeader)) {
        $headerStr = $strHeader;
    }
    return strlen($strHeader);
}
function getBlobSize($image, $digest) {
    global $registryAPI, $headerStr;
    $url = "/$image/blobs/$digest";
    httpGet($url, 'Content-Length', true);
    return 0 + substr($headerStr, 15);
}
function getTagInfo($image, $tag) {
    global $registryAPI, $showImageSize;
    $url = "/$image/manifests/$tag";
    $json = httpGet($url);
    if (isset($json['errors'])) {
        return array(
            'Layeys' => 0,
            'Size' => 0,
            'Created' => ''
        );
    }
    $tagSize = 0;
    if (!$showImageSize) {
        $tagSize = '-';
    } else {
        foreach ($json['fsLayers'] as $value) {
            $tagSize += getBlobSize($image, $value['blobSum']);
        }
    }
    $infoV1 = json_decode($json['history'][0]['v1Compatibility'], true);
    $lastTime = date('Y-m-d H:i:s', strtotime(explode('.', $infoV1['created'])[0]));
    return array(
        'Layeys' => count($json['fsLayers']),
        'Size' => $tagSize,
        'Created' => $lastTime
    );
}
function getRepInfo($image) {
    global $registryAPI, $showImageSize;
    $url = "/$image/tags/list";
    $json = httpGet($url);
    $totalSize = 0;
    $totalTags = 0;
    if (!empty($json['tags'])) {
        if (!$showImageSize) {
            $totalTags = count($json['tags']);
            $totalSize = '-';
        } else {
            foreach ($json['tags'] as $tag) {
                $totalSize += getTagInfo($image, $tag)['Size'];
                $totalTags += 1;
            }
        }
    }
    return array(
        'Tags' => $totalTags,
        'Size' => empty($totalSize) ? '-' : $totalSize
    );
}
function size($byte) {
    if (!is_numeric($byte)) {
        return $byte;
    } else if($byte < 1024) {
        $unit = "B";
    } else if($byte < 1048576) {
        $byte = round_dp($byte/1024, 2);
        $unit = "KB";
    } else if ($byte < 1073741824) {
        $byte = round_dp($byte/1048576, 2);
        $unit = "MB";
    } else {
        $byte = round_dp($byte/1073741824, 2);
        $unit = "GB";
    }
    $byte .= $unit;
    return $byte;
}

function round_dp($num , $dp) {
    $sh = pow(10 , $dp);
    return (round($num*$sh)/$sh);
}
