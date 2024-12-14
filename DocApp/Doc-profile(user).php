<?php 
// Include the database connection
include('db.php');

// Fetch doctors' profiles with their availability from the database
$query = "
    SELECT * FROM doctors
";
$result = mysqli_query($mysqli, $query);

// Check if there are results
if (!$result) {
    die("Error fetching data: " . mysqli_error($mysqli));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctors List</title>
    <link rel="stylesheet" href="styles2.css"> <!-- Link to your external CSS -->
</head>
<body>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #005f8f;
        margin: 0;
        padding: 0;
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 3rem; /* Larger font size */
        font-weight: bold; /* Make the text bold */
        color: white;
        text-transform: uppercase; /* Make the text uppercase */
        letter-spacing: 2px; /* Increase letter spacing for better readability */
        background-image: linear-gradient(to right, #3498db, #8e44ad); /* Gradient background */
        -webkit-background-clip: text; /* Apply gradient to text only */
        background-clip: text;
        padding: 10px; /* Add some padding around the text */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Shadow effect to lift the text */
        transition: all 0.3s ease-in-out; /* Smooth transition effect */
    }

    h1:hover {
        transform: translateY(-5px); /* Slight lift effect on hover */
        color: #ecf0f1; /* Light color change on hover */
    }

    button {
        display: block;
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: none;
        background-color: #3498db; /* Sky Blue */
        color: #fff;
        cursor: pointer;
        font-size: 1.1rem; /* Slightly larger font size */
    }

    button:hover {
        background-color: #2980b9; /* Darker blue on hover */
    }

    @media (max-width: 768px) {
        h1 {
            font-size: 2rem; /* Smaller font size on smaller screens */
        }
        button {
            width: 80%; /* Adjust button width for mobile */
        }
    }
</style>


<div class="container">
    <h1>Registered Doctors List</h1>
    <div class="doctor-list">
        <?php
        // Database connection
        $mysqli = require __DIR__ . "/db.php"; // Include your database connection
        if (!$mysqli) {
            die("Database connection failed: " . $mysqli->connect_error);
        }

        // Query to fetch doctors and their availability
        $sql = "
            SELECT d.id, d.username, d.phone, d.email, d.specialist, d.address, ds.availability
            FROM doctors d
            LEFT JOIN doctor_schedule ds ON d.id = ds.doctor_id
            ORDER BY d.id
        ";
        $result = $mysqli->query($sql);

        // Check if there are any doctors in the database
        if ($result->num_rows > 0) {
            while ($doctor = $result->fetch_assoc()) {
                ?>
                <div class="doctor-profile">
                    <h2><?php echo htmlspecialchars($doctor['username']); ?></h2><br>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($doctor['phone']); ?></p><br>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($doctor['email']); ?></p><br>
                    <p><strong>Specialization:</strong> <?php echo htmlspecialchars($doctor['specialist']); ?></p><br>
                    <?php if (!empty($doctor['availability'])): ?>
                        <ul class="availability-list">
                            <li><?php echo htmlspecialchars($doctor['availability']); ?></li>
                        </ul>
                    <?php else: ?>
                        <p>No availability set.</p><br>
                    <?php endif; ?>
                    <button>Call</button>
                    <div>
                        <a href="Payment.php?doctor_id=<?php echo urlencode($doctor['id']); ?>"> <!-- Pass doctor ID via URL -->
                            <button>Payment</button>
                        </a>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No doctors registered yet.</p>";
        }
        ?>
    </div>
</div>

</body>

</html>
