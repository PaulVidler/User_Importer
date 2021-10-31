#! /usr/bin/env php

<?php

use Symfony\Component\Console\Application;

require 'vendor/autoload.php';

$app = new Application('User Importer', '1.0');

$servername = "localhost";
$username = "fleetest";
$password = "050505";


$createTable = 'CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name varchar(255) NOT NULL,
    surname varchar(255) NOT NULL,
    email varchar(255) NOT NULL UNIQUE,
    created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
)';

try
{
    $pdo = new PDO("mysql:host=$servername;dbname=myDB", $username, $password);
    $pdo->query($createTable);
    // set PDO attributes
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // set return type to associative array
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // sets error mode to throw exceptions

}
catch (Exception $exception)
{
    echo 'Could not connect to the database. Check you connection details are correct';
    exit(1);
}


