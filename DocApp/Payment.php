<?php
session_start();
include('db.php'); // Include the database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: Doc-profile(user).php');
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

if (isset($_GET['doctor_id'])) {
    $doctor_id = $_GET['doctor_id'];
} else {
    $doctor_id = ''; // Default to an empty string if not available
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = trim($_POST['paymentMethod']);
    $amount = trim($_POST['amount']);
    $doctor_id = trim($_POST['doctor_id']);

    if (empty($payment_method) || empty($amount) || empty($doctor_id)) {
        echo "All fields are required.";
    } else {
        // Validate doctor ID and availability
        $check_doctor_query = "SELECT id FROM doctors WHERE id = ?";
        $check_doctor_stmt = $mysqli->prepare($check_doctor_query);

        if (!$check_doctor_stmt) {
            die("SQL Error: " . $mysqli->error); // Handle query preparation failure
        }

        $check_doctor_stmt->bind_param('i', $doctor_id);
        $check_doctor_stmt->execute();
        $check_doctor_stmt->bind_result($doctor_id_result);
        $doctor_found = $check_doctor_stmt->fetch();
        $check_doctor_stmt->close(); // Close the statement to prevent "commands out of sync"

        if ($doctor_found) {
            // Insert payment into the database
            $insert_query = "INSERT INTO payments (user_id, doctor_id, amount, payment_method, payment_date) 
                 VALUES (?, ?, ?, ?, NOW())";
            $insert_stmt = $mysqli->prepare($insert_query);
        
            if (!$insert_stmt) {
                die("SQL Error: " . $mysqli->error); // Handle query preparation failure
            }
        
            $insert_stmt->bind_param('iiis', $user_id, $doctor_id, $amount, $payment_method);
        
            if ($insert_stmt->execute()) {
                // Redirect to receipt download
                header("Location: Download.php?doctor_id=$doctor_id&user_id=$user_id");
                exit();
            } else {
                echo "Error processing payment: " . $insert_stmt->error;
            }
        
            $insert_stmt->close(); // Close the insert statement
        } else {
            echo "Invalid doctor ID.";
        }
        
    }
}


// Fetch payment history for the user
$payment_history_query = "
    SELECT p.payment_id, p.amount, p.payment_method, p.payment_date, d.username AS doctor_name
    FROM payments p
    JOIN doctors d ON p.doctor_id = d.id
    WHERE p.user_id = ?";
$history_stmt = $mysqli->prepare($payment_history_query);
$history_stmt->bind_param('i', $user_id);
$history_stmt->execute();
$payment_history_result = $history_stmt->get_result();

// Fetch total payments for each doctor
$total_payments_query = "
    SELECT d.username AS doctor_name, SUM(p.amount) AS total_payments
    FROM payments p
    JOIN doctors d ON p.doctor_id = d.id
    GROUP BY p.doctor_id";
$total_stmt = $mysqli->prepare($total_payments_query);
$total_stmt->execute();
$total_payments_result = $total_stmt->get_result();

?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment and Download Receipt</title>
    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #add8e6; /* Light Blue Background Color */
    transition: background-color 0.3s ease; /* Smooth transition for background color */
}

body:hover {
    background-color: #7ec8d6; /* Darker Blue when hovered */
}

/* Header */
header {
    background-color: #3498db;
    color: #fff;
    text-align: center;
    padding: 20px;
    font-size: 2rem; /* Larger header text */
    font-weight: bold; /* Make header text bold */
    text-transform: uppercase; /* Uppercase text for the header */
    letter-spacing: 2px; /* Improve readability with spaced-out letters */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow under the header */
    transition: background-color 0.3s ease; /* Transition for header background */
}

header:hover {
    background-color: #2980b9; /* Darker header background color */
}

/* Main Content Section */
section {
    max-width: 600px;
    margin: 20px auto;
    background-color: #fff;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Subtle shadow for content section */
    border-radius: 8px; /* Rounded corners */
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out; /* Smooth transition for hover effects */
}

section:hover {
    transform: translateY(-5px); /* Lift effect when hovered */
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2); /* Stronger shadow effect on hover */
}

/* Button Styling */
button {
    display: block;
    width: 100%;
    padding: 15px;
    margin: 15px 0;
    border: none;
    background-color: #3498db;
    color: #fff;
    cursor: pointer;
    font-size: 1.1rem; /* Slightly larger text for buttons */
    border-radius: 5px; /* Rounded corners for buttons */
    transition: background-color 0.3s, transform 0.3s; /* Smooth transitions */
}

button:hover {
    background-color: #2980b9; /* Darker blue on hover */
    transform: translateY(-3px); /* Lift effect on hover */
}

/* Payment Methods Section */
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #add8e6; /* Light Blue Background Color */
    transition: background-color 0.3s ease; /* Smooth transition for background color */
}

body:hover {
    background-color: #7ec8d6; /* Darker Blue when hovered */
}

/* Header */
header {
    background-color: #3498db;
    color: #fff;
    text-align: center;
    padding: 20px;
    font-size: 2rem; /* Larger header text */
    font-weight: bold; /* Make header text bold */
    text-transform: uppercase; /* Uppercase text for the header */
    letter-spacing: 2px; /* Improve readability with spaced-out letters */
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Subtle shadow under the header */
    transition: background-color 0.3s ease; /* Transition for header background */
}

header:hover {
    background-color: #2980b9; /* Darker header background color */
}

/* Main Content Section */
section {
    max-width: 600px;
    margin: 20px auto;
    background-color: #fff;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); /* Subtle shadow for content section */
    border-radius: 8px; /* Rounded corners */
    transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out; /* Smooth transition for hover effects */
}

section:hover {
    transform: translateY(-5px); /* Lift effect when hovered */
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2); /* Stronger shadow effect on hover */
}

/* Button Styling */
button {
    display: block;
    width: 100%;
    padding: 15px;
    margin: 15px 0;
    border: none;
    background-color: #3498db;
    color: #fff;
    cursor: pointer;
    font-size: 1.1rem; /* Slightly larger text for buttons */
    border-radius: 5px; /* Rounded corners for buttons */
    transition: background-color 0.3s, transform 0.3s; /* Smooth transitions */
}

button:hover {
    background-color: #2980b9; /* Darker blue on hover */
    transform: translateY(-3px); /* Lift effect on hover */
}

/* Payment Methods Section */
.payment-methods {
    margin-top: 20px;
    font-size: 1.2rem; /* Slightly larger text */
    color: #333; /* Darker text for contrast */
}

.payment-method {
    display: flex;
    align-items: center;
    margin-bottom: 15px;
    font-size: 1rem;
    transition: transform 0.3s ease-in-out; /* Smooth transition for payment method */
}

.payment-method:hover {
    transform: translateX(5px); /* Slight movement when hovered */
}

.payment-method label {
    margin-left: 10px;
    font-size: 1rem; /* Ensure readability */
    color: #555; /* Slightly darker text for labels */
}

.payment-method input[type="radio"] {
    width: 20px;
    height: 20px;
    margin-right: 10px;
    transition: transform 0.3s ease-in-out; /* Radio button hover effect */
}

.payment-method input[type="radio"]:hover {
    transform: scale(1.1); /* Slight scale-up effect when hovered */
}


footer {
    text-align: center;
    margin-top: 20px;
    padding: 10px;
    background-color: #3498db;
    color: #fff;
    font-size: 1rem;
}


table {
    border-collapse: collapse;
    width: 100%;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 10px;
    text-align: center;
}

th {
    background-color: #f4f4f4;
    font-weight: bold;
}

tbody tr:nth-child(even) {
    background-color: #f9f9f9;
}

tbody tr:hover {
    background-color: #f1f1f1;
}

        .receipt-download {
            display: none;
            justify-content: space-between;
            align-items: center;
        }

        .receipt-download a {
            text-decoration: none;
            color: #fff;
            background-color: #27ae60;
            padding: 10px;
            border-radius: 5px;
        }

        input[type="radio"] {
            display: none;
        }

        input[type="radio"] + label {
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            position: relative;
            padding-left: 25px;
        }

        input[type="radio"] + label:before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: #000;
            margin-right: 10px;
        }

        input[type="radio"]:checked + label:before {
            background-color: #3498db;
        }

        .payment-amount {
            margin-top: 20px;
            display: flex;
            align-items: center;
        }

        .payment-amount label {
            margin-right: 10px;
        }

        .payment-amount input {
            flex: 1;
            padding: 8px;
        }
    </style>
</head>

<body>

    <header>
        <h2>Payment and Download Receipt</h2>
    </header>
    <!-- Display success or error message -->
    <?php if (isset($success_message)): ?>
        <div style="color: green;"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div style="color: red;"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <form method="POST" action="Payment.php"> <!-- Ensure the form submits to Payment.php -->
    <section>
        <h4>Select a payment method:</h4>
        <div class="payment-methods">
            <div class="payment-method">
                <input type="radio" id="bkash" name="paymentMethod" value="bKash" required>
                <label for="bkash">bKash</label>
            </div>

            <div class="payment-method">
                <input type="radio" id="nagad" name="paymentMethod" value="Nagad">
                <label for="nagad">Nagad</label>
            </div>

            <div class="payment-method">
                <input type="radio" id="visa" name="paymentMethod" value="Visa">
                <label for="visa">Visa</label>
            </div>

            <div class="payment-method">
                <input type="radio" id="mastercard" name="paymentMethod" value="MasterCard">
                <label for="mastercard">MasterCard</label>
            </div>

            <div class="payment-method">
                <input type="radio" id="rocket" name="paymentMethod" value="Rocket">
                <label for="rocket">Rocket</label>
            </div>
        </div>

        <div class="payment-amount">
            <label for="amount">Enter Payment (TK):</label>
            <input type="text" id="amount" name="amount" required>
        </div>

        <?php if (!empty($doctor_id)): ?>
            <input type="hidden" name="doctor_id" value="<?php echo htmlspecialchars($doctor_id); ?>">
        <?php else: ?>
        <p>Error: Doctor ID is missing. Please select a doctor first.</p>
        <?php endif; ?>


        <button type="submit" id="payment-btn">Make Payment</button>
    </section>
</form>

<section>
    <h4>Payment History</h4>
    <table>
        <thead>
            <tr>
                <th>Payment ID</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Date</th>
                <th>Doctor</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($payment_history_result->num_rows > 0) {
                while ($payment = $payment_history_result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$payment['payment_id']}</td>
                        <td>{$payment['amount']}</td>
                        <td>{$payment['payment_method']}</td>
                        <td>{$payment['payment_date']}</td>
                        <td>{$payment['doctor_name']}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No payment history found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</section>
<section>
    <h4>Total Payments by Doctor</h4>
    <table>
        <thead>
            <tr>
                <th>Doctor</th>
                <th>Total Payments</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($total_payments_result->num_rows > 0) {
                while ($total = $total_payments_result->fetch_assoc()) {
                    echo "<tr>
                        <td>{$total['doctor_name']}</td>
                        <td>{$total['total_payments']}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No payment data found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</section>


    <script>
        const paymentBtn = document.getElementById('payment-btn');
        const receiptSection = document.getElementById('receipt-section');

        paymentBtn.addEventListener('click', () => {
            const selectedPaymentMethod = document.querySelector('input[name="paymentMethod"]:checked');
            const paymentAmount = document.getElementById('amount').value.trim();

            if (selectedPaymentMethod && paymentAmount !== '') {
        alert(`Payment Successful! You chose ${selectedPaymentMethod.value} as the payment method. Amount: ${paymentAmount} TK`);

                receiptSection.style.display = 'flex';
                // Trigger the file download (if needed)
                setTimeout(() => {
                    const doctorId = '<?php echo urlencode($doctor_id); ?>';
                    const downloadLink = document.createElement('a');
                    downloadLink.href = `Download.php?doctor_id=${doctorId}`;
                    downloadLink.download = '';
                    downloadLink.click();
                }, 1000);
                window.location.href = 'Download.php?doctor_id=<?php echo urlencode($doctor['id']); ?>';
            } else {
                alert('Please select a payment method and enter the payment amount before making the payment.');
            }
        });
    </script>

</body>

</html>