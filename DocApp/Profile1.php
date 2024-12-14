<?php 
session_start();
if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
} else {
    $username = "Guest"; // Or redirect to login page
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dr. Samuel Goe - Your Health Guardian</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #3498db;
            color: #fff;
            text-align: center;
            padding: 20px;
        }

        section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 100vw;
            margin: 20px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        button {
            display: block;
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: none;
            background-color: #3498db;
            color: #fff;
            cursor: pointer;
        }

        button:hover {
            background-color: #2980b9;
        }

        img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }

    </style>
</head>
<body>

    <header>
        <h1>Dr. Samuel Goe - Your Health Guardian</h1>
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>!</h2>  <!-- Display the username -->
    </header>

    <section>
        <div>
            <h2>About Dr. Samuel Goe</h2>
            <p>Dr. Samuel Goe is a highly skilled and experienced Heart specialist dedicated to providing top-notch healthcare services...</p>

            <h2>Services Offered</h2>
            <ul>
                <li>Comprehensive health assessments</li>
                <li>Accurate diagnoses</li>
                <li>Personalized treatment plans</li>
                <li>Follow-up care and monitoring</li>
            </ul>

            <h2>Location</h2>
            <p>Mirpur, Dhaka</p>

            <h2>Visit Fee</h2>
            <p>1200 BDT</p>

            <h2>Appointment Details</h2>
            <p>Ready to take the first step towards a healthier you? Schedule an appointment with Dr. Samuel Goe at his Mirpur clinic.</p>

            <button>Call</button>
            <div>
                <a href="Payment.php"> <!-- Example: pass doctor info via URL -->
                    <button>Payment</button>
                </a>

            </div>
            
        </div>
        
        <div>
            <img src="images/doc-1.png" alt="Dr. Samuel Goe">
        </div>
    </section>

</body>
</html>
