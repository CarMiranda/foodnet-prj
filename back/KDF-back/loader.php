<?php

    // Load global configuration
    include_once('config/globals.php');
    #include_once(dirname(__FILE__).'/core/_loader.php');
    include_once('config/database.php');

    // Setting constants for databases
    define('USERS__DB_HOST', 'localhost');
    define('USERS__DB_NAME', $users__db_name);
    define('USERS__DB_USER', $users__db_user);
    define('USERS__DB_PASS', $users__db_pass);
    define('USERS__TABLE', $users__table);
    define('USERS__TOKEN_FIELD', $users__token_field);

    define('APP__DB_HOST', 'localhost');
    define('APP__DB_NAME', $app__db_name);
    define('APP__DB_USER', $app__db_user);
    define('APP__DB_PASS', $app__db_pass);

    // Database connections configuration
    try {
        DB::config('mysql:host=' . USERS__DB_HOST . ';dbname=' . USERS__DB_NAME, USERS__DB_USER, USERS__DB_PASS, 'users');
        DB::config('mysql:host=' . APP__DB_HOST . ';dbname=' . APP__DB_NAME, APP__DB_USER, APP__DB_PASS, 'app');
    } catch(Exception $e) {
        die('Error in MySQL configuration.');
    }    
?>