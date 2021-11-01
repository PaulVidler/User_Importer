<?php

// Steps
// - Install dependancies: 'composer install'
// - Make the file executable: "cdmod +x ./user_upload"
// - Run "php user_upload.php" 

$filename = './users.csv';
$data = [];

// open the file
$f = fopen($filename, 'r');

if ($f === false) {
	die('Cannot open the file ' . $filename);
}

// read each line in CSV file at a time
while (($row = fgetcsv($f)) !== false) {
	$data[] = $row;
}

foreach($data as $row){
    echo $row[0];
    echo $row[1];
    echo $row[2];
}

// close the file
fclose($f);