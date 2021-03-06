<?php
require_once('vendor/autoload.php');

$uri = $_SERVER['REQUEST_URI'];
$image = [];
// extract image data and options from uri
preg_match('/(.*)\/(\w+)\.(jpg|png)(.*)/', $uri, $image);

// get absolute path of image to display
$filePath = $_SERVER["DOCUMENT_ROOT"] . $image[1];

$simple = new \Hiperbola\SimpleImage\SimpleImage($filePath, $image[2] . '.' . $image[3], trim($image[4], '/'));
$simple->dumpImage();