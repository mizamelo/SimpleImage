<?php
require_once 'SimpleImage.php';

$simple = new SimpleImage('../img/Test.png');
$simple->rotate90();
$simple->resize(500, 500);
$simple->cloneToGIF()();
$simple->merge('../img/merge.jpg', 10, 25);
$simple->crop(300, 300, 20, 40);
$simple->setFont('../font/arial.ttf');
$simple->setFontSize(15);
$simple->setFontColor('#FF0000');
$simple->write('Test', 45, 45);
$simple->save();
