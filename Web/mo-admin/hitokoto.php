<?php
$url = 'http://api.hitokoto.us/rand?encode=js&charset=utf-8';
$contents = file_get_contents($url);
header('Content-Type: text/javascript;charset=utf-8');
$contents = explode('"', $contents);
$contents = 'function hitokoto(){$(".hitokoto").append("'. $contents[1]. '");}';
echo $contents;
