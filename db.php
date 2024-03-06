<?php

    $db = 'progettosettimana16';
    $config = [
        'mysql_host' => 'localhost',
        'mysql_user' => 'root',
        'mysql_password' => ''
    ];

    $mysqli = new mysqli(
        $config['mysql_host'],
        $config['mysql_user'],
        $config['mysql_password']);

    if($mysqli->connect_error) { die($mysqli->connect_error); } 

    // Creo il mio DB
    $sql = 'CREATE DATABASE IF NOT EXISTS ' . $db;
    if(!$mysqli->query($sql)) { die($mysqli->connect_error); }

    // Seleziono il DB
    $sql = 'USE ' . $db;
    $mysqli->query($sql);

    // Creo la tabella
    $sql = 'CREATE TABLE IF NOT EXISTS users ( 
        id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
        firstname VARCHAR(255) NOT NULL, 
        lastname VARCHAR(255) NOT NULL, 
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL, 
        isadmin BOOLEAN NOT NULL DEFAULT 0
    )';
    if(!$mysqli->query($sql)) { die($mysqli->connect_error); }

    // Leggo dati da una tabella
    $sql = 'SELECT * FROM users;';
    $res = $mysqli->query($sql); // return un mysqli result
    if($res->num_rows === 0) { 
        $password = password_hash('admin' , PASSWORD_DEFAULT);
        // Inserisco dati in una tabella
        $sql = 'INSERT INTO users (firstname, lastname, email, password, isadmin) 
            VALUES ("Pippo", "Franco", "chic0@mail.com", "'.$password.'", 1);';
        if(!$mysqli->query($sql)) { echo($mysqli->connect_error); }
        else { echo 'Record aggiunto con successo!!!';}
    }
