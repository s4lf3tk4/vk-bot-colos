<?php

    $dbhost = 'localhost';
    $dbuser = 'root';
    $dbpass = '';
    $dbport = 3306;
    $dbname = 'vk-bot';
    
    $conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname, $dbport);

    if ($conn->connect_error) {
        throw new \Exception("Ошибка подключения к БД: " . $conn->connect_error);
    }
    return $conn;

?>