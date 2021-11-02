# User Importer from CSV
Simple CSV user import to local DB in PHP using Symfony console

Boilerplate code and added functionality built from the Symfony Console component. https://symfony.com/doc/current/console

## Usage

- First, you will need to create a local DB instance with a database called 'myDB'
- Install all dependancies by using the terminal from your project directory 'composer install'
- Run from terminal with "php user_upload.php"

## Commands/switches

'php user_upload.php' - To call script

'php user_upload.php import -h' - Will give help on this package.

import - Command to give acces to switches below

create_table - To create the table locally on your DB instance

-u 'your_db_name'

-p 'your_db_password'

-d 'your database host location' *Note: 'h' is taken by default as help for command line switches. '-d' has been used instead.

--file=./users.csv

## An example command to enter credentials to DB, supply the file path, create the table, then display a dry run of import data to screen.

"php user_upload.php import -u myUsername -p myPassword -d localhost --file=./users.csv create_table dry_run"

## An example command to enter credentials to DB, supply the file path, then import data and present on screen.

"php user_upload.php import -u myUsername -p myPassword -d localhost --file=./users.csv create_table"

## Features

- Validation of all email addresses
- Removing whitespace and odd characters from names
- First letter of each name/surname will be capitalised
- Will not allow duplicate emails

## Known issues
- Requires refactoring and a more OO approach
- No tests
- Email validation will not allow 'open.edu.au' as a domain
- Requires better help docs and user experience

