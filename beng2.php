<?php
session_start(); // Start a session

// Database connection variables
$host = 'dpg-cs4ebi5svqrc738abegg-a';
$dbname = 'segstudentsmanagementdb';
$user = 'postgres_user'; 
$password = '2oS4P4ZjFuMCz6hFeTxVhsm17cYsX53W';  

// Establishing connection with PostgreSQL
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.html?error=You must log in first!");
    exit();
}

// Retrieve the user's information
$email = $_SESSION['user_email'];

// SQL query to retrieve the user's payment status
$sql = "SELECT subscription_status, payment_status FROM students WHERE email = :email";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':email', $email);
$stmt->execute();

if ($stmt->rowCount() === 1) {
    // Fetch the user data
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check payment status
    $paymentStatus = trim(strtolower($user['payment_status']));
    
    if ($paymentStatus !== 'paid') {
        // User has not paid; show message and prevent access to materials
        $fullname = htmlspecialchars($_SESSION['user_name']);
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Access Denied</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f8f9fa;
                    color: #343a40;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }
                .container {
                    text-align: center;
                    padding: 20px;
                    border-radius: 8px;
                    background-color: #ffffff;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    width: 90%;
                    max-width: 500px;
                }
                h1 {
                    color: #dc3545;
                }
                p {
                    font-size: 1.2em;
                    margin: 20px 0;
                }
                .contact {
                    font-weight: bold;
                    color: #28a745;
                }
                a {
                    display: inline-block;
                    margin-top: 20px;
                    padding: 10px 20px;
                    color: white;
                    background-color: #007bff;
                    border-radius: 5px;
                    text-decoration: none;
                }
                a:hover {
                    background-color: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h1>Access Denied, ' . $fullname . '!</h1>
                <p>Your payment status is <strong>unpaid</strong>. Please make your full payment to access course materials.</p>
                <p>For assistance, contact your tutor at <span class="contact">0974353800</span>.</p>
                <a href="dashboard.php">Return to Dashboard</a>
		<a href="payment.php">Go to payment page</a>
            </div>
        </body>
        </html>';
        exit();
    } else {
        // User has paid; allow access to materials
        echo '<!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>BENG_2 (NQ) Courses</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                }
                nav {
                    background-color: #333;
                    color: white;
                    padding: 10px;
                    text-align: center;
                }
                nav h1 {
                    margin: 0;
                }
                section {
                    margin: 20px;
                    padding: 20px;
                    background-color: white;
                    text-align: center;
                }
                details {
                    margin: 10px 0;
                    width: 300px;
                    margin-left: auto;
                    margin-right: auto;
                }
                summary {
                    font-weight: bold;
                    cursor: pointer;
                    background-color: #28A745;
                    color: white;
                    padding: 10px;
                    border-radius: 5px;
                }
                summary:hover {
                    background-color: #218838;
                }
                ul {
                    list-style-type: none;
                    padding-left: 0;
                    margin-top: 10px;
                }
                ul li {
                    margin: 5px 0;
                }
                ul li a {
                    color: #333;
                    text-decoration: none;
                }
                ul li a:hover {
                    text-decoration: underline;
                }
            </style>
        </head>
        <body>

            <!-- Navigation Bar -->
            <nav>
                <h1>BENG_2(NQ) Courses</h1>
                <a href="beng2_dashboard.php" style="color: white; text-decoration: underline;">Back to Dashboard</a>
            </nav>

            <section>
                <h2>Select a Course</h2>

                <!-- Mathematics (Ma210) -->
                <details>
                    <summary aria-label="Mathematics course materials">Mathematics (Ma210)</summary>
                    <details>
                        <summary>Lecture Notes</summary>
                        <ul>
                            <li><a href="materials/beng2/MA210/LECTURE 1.pdf" download>Lecture 1 (PDF)</a></li>
                            <li><a href="materials/beng2/MA210/LECTURE 2.pdf" download>Lecture 2 (PDF)</a></li>
                        </ul>
                    </details>
                    <details>
                        <summary>Lecture Videos</summary>
                        <ul>
                            <li><a href="https://www.youtube.com/watch?v=video1" target="_blank">Lecture Video 1</a></li>
                            <li><a href="https://www.youtube.com/watch?v=video2" target="_blank">Lecture Video 2</a></li>
                        </ul>
                    </details>
                </details>

                <!-- Electricity and Electronics -->
                <details>
                    <summary aria-label="Electricity and Electronics course materials">Electricity and Electronics</summary>
                    <details>
                        <summary>Lecture Notes</summary>
                        <ul>
                            <li><a href="materials/beng2/electricity_lecture_1.pdf" download>Lecture 1 (PDF)</a></li>
                            <li><a href="materials/beng2/electricity_lecture_2.pdf" download>Lecture 2 (PDF)</a></li>
                        </ul>
                    </details>
                </details>

                <!-- Fixing Statics and Dynamics Section -->
                <details>
                    <summary aria-label="Statics and Dynamics course materials">Statics and Dynamics</summary>
                    <details>
                        <summary>Lecture Notes</summary>
                        <ul>
                            <li><a href="materials/beng2/statics_dynamics_lecture_1.pdf" download>Lecture 1 (PDF)</a></li>
                        </ul>
                    </details>
                    <details>
                        <summary>Lecture Videos</summary>
                        <ul>
                            <li><a href="https://www.youtube.com/watch?v=statics_dynamics_video_1" target="_blank">Lecture Video 1</a></li>
                            <li><a href="https://www.youtube.com/watch?v=statics_dynamics_video_2" target="_blank">Lecture Video 2</a></li>
                        </ul>
                    </details>
                </details>

            </section>

        </body>
        </html>';
        exit();
    }
} else {
    // User not found in the database
    header("Location: login.html?error=User not found.");
    exit();
}
?>
