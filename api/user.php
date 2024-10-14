<?php

// Function to handle user signup
function signup($db) {
    // Get the posted data (e.g., from Postman or front-end)
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->username) && !empty($data->password)) {
        // Insert the new user into the database
        $query = "INSERT INTO users (username, password) VALUES (:username, :password)";

        $stmt = $db->prepare($query);

        // Bind the parameters
        $stmt->bindParam(':username', $data->username);
        $stmt->bindParam(':password', password_hash($data->password, PASSWORD_DEFAULT)); // Hash the password

        if ($stmt->execute()) {
            echo json_encode(["message" => "User registered successfully"]);
        } else {
            echo json_encode(["message" => "Failed to register user"]);
        }
    } else {
        echo json_encode(["message" => "Incomplete data"]);
    }
}

// Function to handle user login
function login($db) {
    // Get the posted data
    $data = json_decode(file_get_contents("php://input"));

    if (!empty($data->username) && !empty($data->password)) {
        // Retrieve the user from the database
        $query = "SELECT * FROM users WHERE username = :username";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':username', $data->username);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the user exists and the password is correct
        if ($user && password_verify($data->password, $user['password'])) {
            echo json_encode(["message" => "Login successful", "user" => $user['username']]);
        } else {
            echo json_encode(["message" => "Invalid username or password"]);
        }
    } else {
        echo json_encode(["message" => "Incomplete data"]);
    }
}

?>
