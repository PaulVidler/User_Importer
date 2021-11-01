<!-- Create a PHP script that is executed form the command line. The script should:
    • Output the numbers from 1 to 100
    • Where the number is divisible by three (3) output the word “foo”
    • Where the number is divisible by five (5) output the word “bar”
    • Where the number is divisible by three (3) and (5) output the word “foobar”
    • Only be a single PHP file
-->
<?php

for ($x =1; $x <= 100; $x++){
    
    if($x % 15 == 0){
        echo $x. ": foobar\n";
        continue;
    } 

    else if($x % 3 == 0){
        echo $x. ": foo\n";
    }

    else if($x % 5 == 0){
        echo $x. ": bar\n";
    }
    
    else {
        echo $x. ":\n";
    }
}