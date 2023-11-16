<?php
if (!isset($_GET['width']) || !isset($_GET['height']) || !isset($_GET['fieldWidth'])) {
    die('Please provide width, height, and fieldWidth parameters via GET.');
}

$width = intval($_GET['width']);
$height = intval($_GET['height']);
$fieldWidth = intval($_GET['fieldWidth']);

$image = imagecreatetruecolor($width, $height);

$color1 = imagecolorallocate($image, 255, 255, 255); // white
$color2 = imagecolorallocate($image, 0, 0, 0);       // black

$alternateColor = true;

for ($y = 0; $y < $height; $y += $fieldWidth) {

    for ($x = 0; $x < $width; $x += $fieldWidth) {

        $currentColor = $alternateColor ? $color1 : $color2;

        imagefilledrectangle($image, $x, $y, $x + $fieldWidth, $y + $fieldWidth, $currentColor);

        $alternateColor = !$alternateColor;
    }

    $alternateColor = !$alternateColor;
}

header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
