<?php
session_start(); // Start a session

// Database connection variables
$host = 'dpg-cs4ebi5svqrc738abegg-a';
$dbname = 'segstudentsmanagementdb';
$user = 'postgres_user'; 
$password = '2oS4P4ZjFuMCz6hFeTxVhsm17cYsX53W'; 

// Establish connection with PostgreSQL
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Basic input validation
    if (empty($email) || empty($password)) {
        header("Location: login.html?error=Both fields are required!");
        exit();
    }

    // Implement rate limiting (3 failed attempts max)
    if (isset($_SESSION['failed_attempts']) && $_SESSION['failed_attempts'] >= 3) {
        header("Location: login.html?error=Too many failed login attempts. Please try again later.");
        exit();
    }

    // SQL query to retrieve the user by email
    $sql = "SELECT * FROM students WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    // Check if the user exists
    if ($stmt->rowCount() === 1) {
        // Fetch student data
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify the password
        if (password_verify($password, $student['password'])) {
            // Regenerate session ID for security
            session_regenerate_id(true);

            // Set session variables
            $_SESSION['user_email'] = $student['email'];
            $_SESSION['user_name'] = htmlspecialchars($student['first_name']);

            // Check the program ID and redirect accordingly
            switch ($student['program_id']) {
                case 1:
                    header('Location: smns_dashboard.php'); // Redirect to SMNS dashboard
                    break;
                case 2:
                    header('Location: beng2_dashboard.php'); // Redirect to BENG2 dashboard
                    break;
                default:
                    echo "Invalid program ID.";
                    exit();
            }
            exit();
        } else {
            // Invalid password, increment failed attempts
            $_SESSION['failed_attempts'] = ($_SESSION['failed_attempts'] ?? 0) + 1;
            header("Location: login.html?error=Invalid password.");
            exit();
        }
    } else {
        // User not found
        header("Location: login.html?error=User not found. Please sign up.");
        exit();
    }
}
?>

