<?php

// Load environment variables using Composer's autoload (Dotenv package)
require '../vendor/autoload.php';  // Ensure this points to the correct path for autoload.php

use Dotenv\Dotenv;

// Initialize the environment variable loader
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');  // Adjust the path to match your directory structure
$dotenv->load();

// Retrieve the database connection details from .env
$host = $_ENV['DB_HOST'];
$db_name = $_ENV['DB_NAME'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];
$port = $_ENV['DB_PORT'];

// Create a database connection using PDO (PHP Data Objects)
try {
    // Establish a connection to the PostgreSQL database
    $db = new PDO("pgsql:host=$host;port=$port;dbname=$db_name", $username, $password);
    // Set PDO error mode to exception to handle potential errors properly
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // (Optional) You can print a success message for testing the connection
    // echo "Connected to the database successfully!";
    
} catch (PDOException $exception) {
    // If there's an error in the connection, display the message
    echo "Connection error: " . $exception->getMessage();
}

?>
