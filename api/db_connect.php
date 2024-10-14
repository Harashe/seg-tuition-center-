<?php

require __DIR__ . '/vendor/autoload.php'; // Load Composer's autoloader

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__); // Load the .env file from the current directory
$dotenv->load(); // Load environment variables

// Access environment variables
$host = $_ENV['DB_HOST'];
$port = $_ENV['DB_PORT'];
$dbname = $_ENV['DB_DATABASE'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];

// Create the PostgreSQL connection string
$conn_str = "host=$host port=$port dbname=$dbname user=$username password=$password";

// Establish a connection to PostgreSQL
$conn = pg_connect($conn_str);

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}
echo "Connected successfully";

?>
