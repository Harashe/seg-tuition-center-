<?php

// Allow CORS (Optional, but important for local testing)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Include the database configuration and user handling functions
include_once '../config/database.php';  // Adjust the path to where the database.php is located
include_once 'user.php';  // Assuming user.php handles signup and login logic

// Check for the request method
$method = $_SERVER['REQUEST_METHOD'];

// Check for actions
if ($method == 'POST') {
    if (isset($_GET['action'])) {
        $action = $_GET['action'];

        // Route to the corresponding function based on the 'action' parameter
        if ($action == 'signup') {
            signup($db);  // Pass the $db connection to the signup function
        } elseif ($action == 'login') {
            login($db);  // Pass the $db connection to the login function
        } else {
            echo json_encode(["message" => "Invalid action"]);
        }
    } else {
        echo json_encode(["message" => "No action specified"]);
    }
} else {
    echo json_encode(["message" => "Invalid request method"]);
}

?>
