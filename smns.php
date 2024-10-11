<?php
session_start(); // Start a session

// Database connection variables
$host = 'localhost';
$dbname = 'SegStudentsManagementDB';
$user = 'postgres'; 
$password = 'Ssempt2002@GMDB'; 

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
                <a href="smns_dashboard.php">Return to Dashboard</a>
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
            <title>SMNS(NQ) Courses</title>
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
                <h1>SMNS(NQ) Courses</h1>
                <a href="smns_dashboard.php" style="color: white; text-decoration: underline;">Back to Dashboard</a>
            </nav>

            <!-- Course List -->
            <section>
                <h2>Select a Course</h2>

                <!-- Mathematics 110 -->
                <details>
                    <summary>Mathematics 110 (MA110)</summary>
                    <details>
                        <summary>Lecture Notes</summary>
                        <ul>
                            <li><a href="materials/smns/MA110/LECTURE 1-set-theory.pdf" download>Lecture 1 (PDF)</a></li>
                            <li><a href="materials/smns/MA110/LECTURE 2.pdf" download>Lecture 2 (PDF)</a></li>
                            <li><a href="materials/smns/MA110/LECTURE 3.pdf" download>Lecture 3 (PDF)</a></li>
                            <li><a href="materials/smns/MA110/LECTURE 4.pdf" download>Lecture 4 (PDF)</a></li>
                        </ul>
                    </details>
                    <details>
                        <summary>Lecture Videos</summary>
                        <ul>
                            <li><a href="https://www.youtube.com/watch?v=video1" target="_blank">Lecture Video 1</a></li>
                            <li><a href="https://www.youtube.com/watch?v=video2" target="_blank">Lecture Video 2</a></li>
                            <li><a href="https://www.youtube.com/watch?v=video3" target="_blank">Lecture Video 3</a></li>
                            <li><a href="https://www.youtube.com/watch?v=video4" target="_blank">Lecture Video 4</a></li>
                        </ul>
                    </details>
                    <details>
                        <summary>Past Papers</summary>
                        <ul>
                            <li><a href="materials/smns/mathematics_past_paper_1.pdf" download>Past Paper 1 (PDF)</a></li>
                            <li><a href="materials/smns/mathematics_past_paper_2.pdf" download>Past Paper 2 (PDF)</a></li>
                        </ul>
                    </details>
                    <details>
                        <summary>Tutorial Sheets</summary>
                        <ul>
                            <li><a href="materials/smns/mathematics_tutorial_1.pdf" download>Tutorial 1 (PDF)</a></li>
                            <li><a href="materials/smns/mathematics_tutorial_2.pdf" download>Tutorial 2 (PDF)</a></li>
                        </ul>
                    </details>
                </details>

                <!-- Physics 110 -->
                <details>
                    <summary>Physics 110 (PH110)</summary>
                    <!-- Add similar sections for Lecture Notes, Lecture Videos, Past Papers, and Tutorial Sheets -->
                </details>

                <!-- Chemistry 110 -->
                <details>
                    <summary>Chemistry 110 (CH110)</summary>
                    <!-- Add similar sections for Lecture Notes, Lecture Videos, Past Papers, and Tutorial Sheets -->
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
