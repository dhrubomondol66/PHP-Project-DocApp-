<?php
session_start();
include('db.php'); // Include your database connection script

$conn = $mysqli; // Use $mysqli directly from db.php

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if the user is not logged in
    exit();
}

// Get user details from the user table
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Get payment history for the user
$payment_query = "SELECT * FROM payments WHERE user_id = ?";
$payment_stmt = $conn->prepare($payment_query);
$payment_stmt->bind_param('i', $user_id);
$payment_stmt->execute();
$payment_result = $payment_stmt->get_result();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="styles.css"> <!-- Include your CSS file -->
</head>
<body>
    <style>
        /* Reset default browser styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
    color: #333;
    padding: 20px;
}

.profile-container {
    max-width: 900px;
    margin: 30px auto;
    background-color: #ffffff;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    padding: 40px;
    transition: all 0.3s ease;
}

.profile-container:hover {
    transform: scale(1.02);
}

h1 {
    text-align: center;
    font-size: 3.5em; /* Larger font size for the title */
    color: #2c3e50;
    margin-bottom: 30px;
    text-transform: uppercase; /* Make the title all caps for emphasis */
    font-weight: 700; /* Make the font bold */
    letter-spacing: 2px; /* Add spacing between letters for a more striking look */
    text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); /* Add a subtle shadow for depth */
}

h2 {
    color: #34495e;
    font-size: 2em; /* Make the subheading slightly larger */
    margin-bottom: 15px;
    text-align: center;
    font-weight: 600;
    letter-spacing: 1px;
}

.user-details, .payment-history {
    margin-top: 25px;
}

.user-details p {
    font-size: 1.2em;
    color: #7f8c8d;
    line-height: 1.6;
}

.user-details p strong {
    color: #2c3e50;
}

.payment-history table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    overflow: hidden;
}

.payment-history table th,
.payment-history table td {
    padding: 12px 18px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.payment-history table th {
    background-color: #3498db;
    color: #fff;
    font-weight: bold;
}

.payment-history table td {
    background-color: #ffffff;
    color: #7f8c8d;
}

.payment-history table tr:hover {
    background-color: #ecf0f1;
    transition: background-color 0.3s;
}

.payment-history table td:last-child {
    text-align: right;
}

.edit-profile {
    display: inline-block;
    padding: 12px 25px;
    margin-top: 20px;
    background-color: #2ecc71;
    color: white;
    text-decoration: none;
    font-size: 1.2em;
    border-radius: 8px;
    transition: background-color 0.3s;
}

.edit-profile:hover {
    background-color: #27ae60;
}

.edit-profile:focus {
    outline: none;
}

@media (max-width: 768px) {
    .profile-container {
        padding: 25px;
    }

    h1 {
        font-size: 2.8em;
    }

    h2 {
        font-size: 1.7em;
    }

    .user-details p {
        font-size: 1em;
    }

    .edit-profile {
        font-size: 1em;
        padding: 10px 20px;
    }
}

@media (max-width: 480px) {
    .profile-container {
        padding: 20px;
    }

    h1 {
        font-size: 2.2em;
    }

    h2 {
        font-size: 1.5em;
    }

    .user-details p {
        font-size: 0.9em;
    }

    .edit-profile {
        font-size: 0.9em;
        padding: 8px 18px;
    }
}

    </style>
    <div class="profile-container">
        <h1>Welcome, <?php echo htmlspecialchars($user_data['username']); ?></h1>
        
        <div class="user-details">
            <h2>User Details:</h2>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($user_data['username']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?></p>
            <p><strong>Account Created:</strong> <?php echo date('Y-m-d', strtotime($user_data['created_at'])); ?></p>
        </div>
        
        <div class="payment-history">
            <h2>Payment History:</h2>
            <?php if ($payment_result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = $payment_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $payment['payment_id']; ?></td>
                                <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                                <td><?php echo date('Y-m-d H:i:s', strtotime($payment['payment_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No payment history available.</p>
            <?php endif; ?>
        </div>
        
        <a href="updated_user_profile.php" class="edit-profile">Edit Profile</a><br>
        <a href="index2.php" class="edit-profile">Back</a>
    </div>
</body>
</html>
