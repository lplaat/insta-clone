<?php

class Database {

    public static function connect() {
        # Connected to database
        try {
            $env = parse_ini_file('.env');
            
            $host = $env['DATABASE_HOST'];
            $port = $env['DATABASE_PORT'];
            $databaseName = $env['DATABASE_NAME'];

            $conn = new PDO("mysql:host=$host;port=$port;dbname=$databaseName", $env['DATABASE_USER'], $env['DATABASE_PASSWORD']);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            return $conn;
        } catch(PDOException $e) {
            echo "Failed connecting to the database! Error:" . $e->getMessage();
            exit;
        }
    }
}
