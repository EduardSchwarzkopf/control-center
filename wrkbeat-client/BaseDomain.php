<?php
$host = $_SERVER['HTTP_HOST'];
preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
return $matches[0];
