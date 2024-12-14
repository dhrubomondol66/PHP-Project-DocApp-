<?php
session_start();  // Start the session at the very top

require 'db.php';

$username = $_SESSION['username'];
$stmt = $mysqli->prepare("SELECT * FROM doctors WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user) {
    // Populate session variables if they are not already set
    if (!isset($_SESSION["email"])) {
        $_SESSION["email"] = $user["email"];
    }
    if (!isset($_SESSION["specialist"])) {
        $_SESSION["specialist"] = $user["specialist"];
    }
    if (!isset($_SESSION["address"])) {
        $_SESSION["address"] = $user["address"];
    }
} else {
    echo "User not found in the database.";
    exit();
}

// Fetch availability from doctor_schedule table
$stmt = $mysqli->prepare("
    SELECT ds.availability
    FROM doctors d
    LEFT JOIN doctor_schedule ds ON d.id = ds.doctor_id
    WHERE d.username = ?
");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$availabilityList = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $availabilityList[] = $row['availability'];
    }
}

// Get doctor details from session
$doctorUsername = $_SESSION['username'];
$doctorEmail = $_SESSION['email'];
$specialist = isset($_SESSION['specialist']) ? $_SESSION['specialist'] : "Specialist not set";
$address = isset($_SESSION['address']) ? $_SESSION['address'] : "Address not set";

?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Doctor Profile</title>
    <style>
    /* General Styles */
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
        flex-direction: column; /* Changed to column for vertical layout */
        justify-content: center;
        align-items: center;
        max-width: 100vw;
        margin: 20px;
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    /* Profile Container */
    .profile-container {
        max-width: 800px;
        margin: 50px auto;
        background-color: #ffffff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 30px;
    }

    /* Form Styles */
    form {
        display: flex;
        flex-direction: column;
        width: 100%; /* Ensures form takes full width */
    }

    label {
        font-size: 16px;
        margin-bottom: 8px;
        color: #555;
    }

    input[type="text"] {
        padding: 12px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 16px;
        width: 100%;
        background-color: #f9f9f9;
    }

    button[type="submit"] {
        background-color: #3498db;
        color: white;
        padding: 12px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button[type="submit"]:hover {
        background-color: #2980b9;
    }

    /* Doctor Icon Styles */
    .doctor-icon {
        width: 100px; /* Adjusted to make the icon a bit larger */
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 20px; /* Space below the icon */
    }

    /* Button container */
.button-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

/* Edit Profile Button */
button {
    background-color: #3498db; /* Blue background */
    color: white; /* White text */
    padding: 12px 20px; /* Padding around the text */
    border: none; /* Remove default border */
    border-radius: 5px; /* Rounded corners */
    font-size: 16px; /* Text size */
    cursor: pointer; /* Pointer cursor on hover */
    transition: background-color 0.3s ease, transform 0.3s ease; /* Smooth transition */
}

/* Button hover effect */
button:hover {
    background-color: #2980b9; /* Darker blue on hover */
    transform: translateY(-2px); /* Slight upward movement on hover */
}

/* Button focus (when clicked) */
button:focus {
    outline: none; /* Remove outline */
    box-shadow: 0 0 10px rgba(52, 152, 219, 0.5); /* Add glow effect */
}

/* Button disabled state */
button:disabled {
    background-color: #bdc3c7; /* Grey background */
    cursor: not-allowed; /* Change cursor to show it's disabled */
}


    /* Button Container for the Doctor Icon */
    .user-container {
        display: flex;
        flex-direction: column; /* Stack the icon and username */
        align-items: center;
        margin-bottom: 20px;
    }

    .user-btn {
        display: flex;
        align-items: center;
        background-color: #3498db;
        color: white;
        padding: 12px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        text-decoration: none;
    }

    .user-btn:hover {
        background-color: #2980b9;
    }
</style>
</head>
<body>

    <header>
        <h1>Doctors Profile</h1>
    </header>

    <div class="profile-container">
        <section>
            <!-- Doctor Icon Button -->
            <div class="user-container">
                <a href="updated_doctor_profile.php" class="user-btn">
                    <!-- Doctor Icon -->
                    <img src="images/Doctor_icon.jpg" alt="Doctor Icon" class="doctor-icon">
                    <!-- Doctor Username -->
                    </a>
                    <span><?php echo $doctorUsername; ?></span>
            </div>

            <!-- Edit Profile Form -->
            <form action="update_doctor_profile.php" method="POST">
            <div class="profile-info">
                <p><strong>Username:</strong> <?php echo $doctorUsername; ?></p>
                <p><strong>Email:</strong> <?php echo $doctorEmail; ?></p>
                <p><strong>Specialist:</strong> <?php echo $specialist; ?></p>
                <p><strong>Address:</strong> <?php echo $address; ?></p>
                <p><strong>Time Schedule:</strong></p>
                <ul>
                    <?php if (!empty($availabilityList)): ?>
                        <?php foreach ($availabilityList as $availability): ?>
                            <li><?php echo htmlspecialchars($availability); ?></li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>No availability set.</li>
                    <?php endif; ?>
                </ul>
            </div>
            
            </form>
            <a href="updated_doctor_profile.php">
                    <button type="button">Edit Profile</button>
            </a><br>
            <a href="index3.php">
                <button type="submit">Back</button>
            </a>

        </section>
    </div>

</body>
</html>
