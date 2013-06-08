<?php
include_once('/usr/share/nginx/trial/index.php');

$file = '/usr/share/nginx/trial/test.txt';
// Open the file to get existing content
$current = file_get_contents($file);
// Append a new person to the file
$current .= "No way \n";
// Write the contents back to the file
file_put_contents($file, $current);