#! /usr/bin/env php

<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\Table;

require 'vendor/autoload.php';

$app = new Application('User Importer', '1.0');

// $servername = "localhost";
// $username = "fleetest";
// $password = "050505";


$servername = "";
$username = "";
$password = "";





// try
// {
//     $pdo = new PDO("mysql:host=$servername;dbname=myDB", $username, $password);
//     $pdo->query($createTable);
//     // set PDO attributes
//     $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // set return type to associative array
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // sets error mode to throw exceptions

// }
// catch (Exception $exception)
// {
//     echo 'Could not connect to the database. Check you connection details are correct';
//     exit(1);
// }


$app->register('import')
    ->setDescription('Import users from a csv file')
    ->addOption('dbUsername', 'u', InputOption::VALUE_REQUIRED, 'Set the username for your local DB instance')
    ->addOption('dbPassword', 'p', InputOption::VALUE_REQUIRED, 'Set the password for your local DB instance')
    ->addOption('dbHost', 'd', InputOption::VALUE_REQUIRED, 'Set the server name for your local DB instance')
    ->addOption('file', 'f', InputOption::VALUE_REQUIRED, 'Set the relative path/name of the file you wish to use (ie: "./users.csv"')
    ->addArgument('dry_run', InputArgument::OPTIONAL, 'Display a dry run of data and present into a table on screen', false)
    ->addArgument('create_table', InputArgument::OPTIONAL, 'Create a "users" table on your local DB instance', false)
    ->setCode(function(InputInterface $input, OutputInterface $output)
    {

        $servername = $input->getOption('dbHost');
        $username = $input->getOption('dbUsername');
        $password = $input->getOption('dbPassword');
        $filePath = $input->getOption('file');
        $createTable = $input->getArgument('create_table');
        $dryRun = $input->getArgument('dry_run');

        echo $servername. "\n";
        echo $username. "\n";
        echo $password. "\n";
        echo $filePath. "\n";
        echo $createTable. "\n";
        echo gettype($createTable). "\n";
        echo "dryRun\n";
        echo $dryRun . "\n";
        echo gettype($dryRun). "\n";

        if ($createTable != null){
            if(isset($servername, $username, $password)){
                createTable($servername, $username, $password);
            }
        }

        if ($dryRun != false And isset($filePath)){
            showDryRun($output, $filePath);
        }

    });

$app->run();

function createTable($servername, $username, $password){
    
    echo "From createtable\n";
    $createTable = 'CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name varchar(255) NOT NULL,
        surname varchar(255) NOT NULL,
        email varchar(255) NOT NULL UNIQUE,
        created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    )';
    
    $pdo = new PDO("mysql:host=$servername;dbname=myDB", $username, $password);
    $pdo->query($createTable);
}

function showDryRun(OutputInterface $output, $filePath){
    $data = [];

    // open the file
    $f = fopen($filePath, 'r');

    if ($f === false) {
        die('Cannot open the file, check your path is correct ' . $filePath);
    }

    $count = 1;
    // read each line in CSV file at a time
    while (($row = fgetcsv($f)) !== false) {
        // skip header
        if ($count === 1){
            $count++;
            continue;
        } 
        else {
            // $data[] = $row;
            // $count++;
            if(CheckEmail($row[2]) == '' ){
                $newArr = [CleanSpecialNameStringChars($row[0]), CleanSpecialNameStringChars($row[1]), "** Email did not pass validation **"];
            } else{
                $newArr = [CleanSpecialNameStringChars($row[0]), CleanSpecialNameStringChars($row[1]), CheckEmail($row[2])];
            }
            
            array_push($data, $newArr);
        }

    }

    // close the file
    fclose($f);

    $table = new Table($output);

    $table->setHeaders(['Name', 'Surname', 'Email'])
            ->setRows($data)
            ->render();
}

function InsertToDB(OutputInterface $output, $filePath){
    $data = [];

    // open the file
    $f = fopen($filePath, 'r');

    if ($f === false) {
        die('Cannot open the file, check your path is correct ' . $filePath);
    }

    $count = 1;
    // read each line in CSV file at a time
    while (($row = fgetcsv($f)) !== false) {
        // skip header
        if ($count === 1){
            $count++;
            continue;
        } 
        else {
            // $data[] = $row;
            // $count++;
            if(CheckEmail($row[2]) == '' ){
                $newArr = [CleanSpecialNameStringChars($row[0]), CleanSpecialNameStringChars($row[1]), "** Email did not pass validation **"];
            } else{
                $newArr = [CleanSpecialNameStringChars($row[0]), CleanSpecialNameStringChars($row[1]), CheckEmail($row[2])];
            }
            
            array_push($data, $newArr);
        }

    }

    // close the file
    fclose($f);

    
}


// clean up odd chars and whitespace from Name strings - Requires feedback on code review for edge cases
function CleanSpecialNameStringChars($str)
{
    $res = preg_replace('/[0-9\@\.\;\" "\!]+/', '', $str);
    return trim(ucfirst(strtolower($res)));
}

// Check email - Requires feedback on code review for edge cases
function CheckEmail($email){
    return filter_var(strtolower($email), FILTER_VALIDATE_EMAIL);
}

function InsertUsers(){

    // try/catch
    // check email is not duplicated

    // return "success";
}


