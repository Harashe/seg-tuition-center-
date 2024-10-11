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

// Retrieve student's payment status
$email = $_SESSION['user_email'] ?? null;
if ($email) {
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
        // Update the student's payment status and subscription status in the database
        $sqlUpdate = "UPDATE students SET payment_status = 'paid', subscription_status = 'true' WHERE email = :email";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->bindParam(':email', $email);

        try {
            $stmtUpdate->execute();
            echo "Payment successful! Your payment and subscription statuses have been updated.";
            echo '<a href="smns.php">Please access your course materials now.</a>';
        } catch (PDOException $e) {
            echo "Error updating payment status: " . $e->getMessage();
        }
    } else {
        echo "Please complete all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your custom styles here -->
</head>
<body>
    <div class="payment-container">
        <h1>Payment Page</h1>

        <?php if ($paymentStatus === 'paid') : ?>
            <p>Your payment status is: <strong>Paid</strong>.</p>
            <p>No outstanding balance. Thank you!</p>
            <a href="login.html">Please sign in now.</a>
        <?php else : ?>
            <p>Your payment status is: <strong><?php echo ucfirst($paymentStatus); ?></strong>.</p>

            <form action="payment.php" method="POST">
                <h3>Select Mobile Money Payment Method:</h3>
                <label>
                    <input type="radio" name="payment_method" value="airtel" required>
                    Airtel Money (0974353800)
                </label>
                <br>
                <label>
                    <input type="radio" name="payment_method" value="mtn" required>
                    MTN Money (0769750580)
                </label>
                <br><br>

                <label for="amount_paid">Amount Paid (ZMW):</label>
                <input type="text" id="amount_paid" name="amount_paid" required>
                <br><br>

                <button type="submit">Confirm Payment</button>
            </form>
        <?php endif; ?>

    </div>
</body>
</html>
