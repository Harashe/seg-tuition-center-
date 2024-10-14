<?php
session_start(); // Start a session

// Load environment variables from .env file
require_once __DIR__ . '/vendor/autoload.php'; // Adjust the path if necessary

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Get database connection variables from the environment
$host = getenv('DB_HOST');
$dbname = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASS');

// Establish connection with PostgreSQL using PDO
try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}

// Check if the user is logged in and retrieve their email from the session
$email = $_SESSION['user_email'] ?? null;
if ($email) {
    // Retrieve payment status from the database
    $sql = "SELECT payment_status FROM students WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {
        $paymentStatus = strtolower(trim($student['payment_status']));
    } else {
        echo "Student data not found.";
        exit();
    }
} else {
    header("Location: login.html?error=Please log in to proceed with payment.");
    exit();
}

// Handle form submission for payment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedMethod = $_POST['payment_method'] ?? null;
    $amountPaid = $_POST['amount_paid'] ?? null;

    if ($selectedMethod && $amountPaid) {
        // Airtel Money and MTN Money API integration
        // Replace with actual API URLs and API keys from environment variables
        $airtelApiUrl = "https://api.airtel.com/collections/request";
        $airtelApiKey = getenv('AIRTEL_API_KEY');

        $mtnApiUrl = "https://api.mtn.com/payment/transaction";
        $mtnApiKey = getenv('MTN_API_KEY');

        // Prepare data to send to the API
        $apiUrl = ($selectedMethod === 'airtel') ? $airtelApiUrl : $mtnApiUrl;
        $apiKey = ($selectedMethod === 'airtel') ? $airtelApiKey : $mtnApiKey;

        $paymentData = [
            'amount' => $amountPaid,
            'phone_number' => $_POST['phone_number'] ?? null, // Make sure the phone number is dynamic
            'currency' => 'ZMW',
            'external_id' => uniqid(), // Generate a unique ID for the transaction
            'payer_message' => 'Payment for course materials',
            'payee_note' => 'Subscription Payment'
        ];

        // Make the API request
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Payment error: ' . curl_error($ch);
        } else {
            $result = json_decode($response, true);
            if (isset($result['status']) && $result['status'] === 'success') {
                // Update payment status in the database
                $sqlUpdate = "UPDATE students SET payment_status = 'paid', subscription_status = 'true' WHERE email = :email";
                $stmtUpdate = $pdo->prepare($sqlUpdate);
                $stmtUpdate->bindParam(':email', $email);
                $stmtUpdate->execute();

                echo "Payment successful! Your payment and subscription statuses have been updated.";
                echo '<a href="smns.php">Please access your course materials now.</a>';
            } else {
                echo 'Payment failed. Please try again.';
            }
        }
        curl_close($ch);
    } else {
        echo "Please complete all fields.";
    }
}
?>
