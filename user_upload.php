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

        if ($createTable != null){
            if(isset($servername, $username, $password)){
                createTable($servername, $username, $password);
            }
        }

        if (!$dryRun != false){
            if(isset($servername, $username, $password)){
                createTable($servername, $username, $password);
                try {
                    $result = InsertUsers($output, $filePath, $servername, $username, $password);
                    echo $result;
                } catch (Exception $e) {
                    echo "There has been an error. Exception caught: " . $e->getMessage() . "\n";
                }
            }
        }

        if ($dryRun != false And isset($filePath)){
            showDryRun($output, $filePath);
        }

    });

$app->run();

function createTable($servername, $username, $password){
    
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
    $data = ReadCSVFile($output, $filePath);

    ShowTable($output, $data);
    
}

function ReadCSVFile(OutputInterface $output, $filePath)
{
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
            $count++;
            if(CheckEmail($row[2]) == '' ){
                $newArr = [CleanSpecialNameStringChars($row[0]), CleanSpecialNameStringChars($row[1]), "** This email does not pass validation **"];
            } else{
                $newArr = [CleanSpecialNameStringChars($row[0]), CleanSpecialNameStringChars($row[1]), CheckEmail($row[2])];
            }
            
            array_push($data, $newArr);
        }

    }

    // close the file
    fclose($f);

    return $data;
}

function ShowTable(OutputInterface $output, $data){
    $table = new Table($output);

    $table->setHeaders(['Name', 'Surname', 'Email'])
            ->setRows($data)
            ->render();
}

// clean up odd chars and whitespace from Name strings - Requires feedback on code review for edge cases
function CleanSpecialNameStringChars($str)
{
    $res = preg_replace('/[0-9\@\.\;\" "\!]+/', '', $str);
    return trim(ucfirst(strtolower($res)));
}

// Check email - Requires feedback on code review for edge cases, currently does not allow "open.edu.au" domain
function CheckEmail($email){
    return filter_var(strtolower($email), FILTER_VALIDATE_EMAIL);
}

function InsertUsers(OutputInterface $output, $filePath, $servername, $username, $password){

    // check email is not duplicated
    $data = [];
    $data = ReadCSVFile($output, $filePath);

    $pdo = new PDO("mysql:host=$servername;dbname=myDB", $username, $password);

    $test = $pdo->query("SELECT email FROM users")->fetchAll();

    if(is_array($data)){
        $count = 0;
        foreach ($data as $row) {

            if($row[2] == "** This email does not pass validation **"){
                // echo $row[2]. "\n";
                echo "User: " .$row[0]. ' '. $row[1]. " email did not pass validation or is already in the DB. Please check this email and try again\n";
            } else {
                $date = date('Y-m-d H:i:s');
                $sql = "INSERT INTO users (name, surname, email, created) VALUES (?,?,?,?)";
                $stmt= $pdo->prepare($sql);
                $stmt->execute([$row[0], $row[1], $row[2], $date]);
                $count++;
            }
        }
    }
    echo "Results are shown below. All records with an unvalidated email \nhave been skipped and are not in the database\n";
    showDryRun($output, $filePath);
}


