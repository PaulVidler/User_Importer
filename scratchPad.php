<?php

// Steps
// - Create local mysql DB instance named myDB
// - Install dependancies: 'composer install'
// - Make the file executable: "cdmod +x ./user_upload"
// - Run "php user_upload.php" 
// - -h command needs to be updated as it;'s the help in any regular conesole app - updated to 'd'

// - For foobar.php - from terminal: "php foorbar.php"

$filename = './users.csv';
$data = [];

// open the file
$f = fopen($filename, 'r');

if ($f === false) {
	die('Cannot open the file, check your path is correct ' . $filename);
}

// read each line in CSV file at a time
while (($row = fgetcsv($f)) !== false) {
	$data[] = $row;
}

foreach($data as $row){
    echo CleanSpecialNameStringChars($row[0]) . ', '. CleanSpecialNameStringChars($row[1]) . ', Legit email: '. $row[2] . ' : '. CheckEmail($row[2]) ."\n";
}

// close the file
fclose($f);

function CleanSpecialNameStringChars($str)
{
    $res = preg_replace('/[0-9\@\.\;\" "\!]+/', '', $str);
    return ucfirst(strtolower($res));
}

function CheckEmail($email){
    echo filter_var($email, FILTER_VALIDATE_EMAIL). "\n";
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}