<?php
// Enable error reporting for debugging (disable this in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection variables
$host = 'localhost';
$dbname = 'SegStudentsManagementDB';
$user = 'postgres'; // Use your actual PostgreSQL username
$password = 'Ssempt2002@GMDB'; // Use your actual PostgreSQL password

// Establishing connection with PostgreSQL
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieving the form data
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];  // New field
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $program_id = $_POST['program'];

    // Basic input validation
    if (empty($fname) || empty($lname) || empty($email) || empty($phone) || empty($password) || empty($program_id)) {
        echo "All fields are required!";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format!";
        exit();
    }

    // Check if the email already exists
    $checkEmailSql = "SELECT * FROM students WHERE email = :email";
    $checkEmailStmt = $pdo->prepare($checkEmailSql);
    $checkEmailStmt->bindParam(':email', $email);
    $checkEmailStmt->execute();

    if ($checkEmailStmt->rowCount() > 0) {
        echo 'User already exists. <a href="login.html">Click here to sign in.</a>';
        exit();
    }

    // SQL query to insert the data into the students table
    $sql = "INSERT INTO students (first_name, last_name, email, phone_number, payment_status, password, program_id, subscription_status, date_enrolled)
            VALUES (:fname, :lname, :email, :phone, 'unpaid', :password, :program_id, 'false', NOW())";

    // Prepare statement
    $stmt = $pdo->prepare($sql);

    // Bind parameters
    $stmt->bindParam(':fname', $fname);
    $stmt->bindParam(':lname', $lname);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':program_id', $program_id);

    // Execute query
    try {
        $stmt->execute();
        // Show success message with a link to the login page
        echo 'Signup successful! <a href="login.html">Click here to log in.</a>';
        exit();
    } catch (PDOException $e) {
        echo "Error: Unable to sign up. " . $e->getMessage();
    }
}
?>

