<?php
session_start();  // Start the session at the very top

// Check if the doctor is logged in by checking if the session variable 'username' is set
if (!isset($_SESSION['username'])) {
    header("Location: Doctors_profile.php");  // Redirect to profile page if not logged in
    exit();
}

// Check if the 'email' session variable is set before using it
if (!isset($_SESSION['email'])) {
    die("Email not found in session. Please log in again.");
}

// Get the doctor details from the session
$doctorUsername = $_SESSION['username'];
$doctorEmail = $_SESSION['email'];
$specialist = $_SESSION['specialist'];
$address = $_SESSION['address'];

// Initialize variables for doctor availability
$availability = [];

// Database connection
require 'db.php';  // Include the database connection

// Fetch the availability from the doctor_schedule table
$stmt = $mysqli->prepare("SELECT ds.id, ds.availability FROM doctor_schedule ds JOIN doctors d ON ds.doctor_id = d.id WHERE d.email = ?");
$stmt->bind_param("s", $doctorEmail);
$stmt->execute();
$result = $stmt->get_result();

$availabilityList = [];
if ($result->num_rows > 0) {
    $availabilityList = $result->fetch_all(MYSQLI_ASSOC);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newUsername = $_POST['username'];
    $newSpecialist = $_POST['specialist'];
    $newAddress = $_POST['address'];
    $newAvailability = $_POST['availability'] ?? [];  // New availability items
    $deleteAvailability = $_POST['delete_availability'] ?? [];  // IDs to delete

    // Start a transaction for atomicity
    $mysqli->begin_transaction();

    try {
        // Update the doctors table
        $stmt1 = $mysqli->prepare("UPDATE doctors SET username = ?, specialist = ?, address = ? WHERE email = ?");
        $stmt1->bind_param("ssss", $newUsername, $newSpecialist, $newAddress, $doctorEmail);
        $stmt1->execute();

        // Delete selected availability if any
        if (!empty($deleteAvailability)) {
            // Prepare the DELETE statement using the IDs to be deleted
            $deletePlaceholders = implode(",", array_fill(0, count($deleteAvailability), "?"));
            $stmt2 = $mysqli->prepare("DELETE FROM doctor_schedule WHERE id IN ($deletePlaceholders)");
            $stmt2->bind_param(str_repeat("i", count($deleteAvailability)), ...$deleteAvailability);
            $stmt2->execute();
        }

        // Insert new availability only if it's not already in the database
        foreach ($newAvailability as $availabilityValue) {
            // Check if it's already in the availability list
            $stmtCheck = $mysqli->prepare("SELECT id FROM doctor_schedule WHERE availability = ? AND doctor_id = (SELECT id FROM doctors WHERE email = ?)");
            $stmtCheck->bind_param("ss", $availabilityValue, $doctorEmail);
            $stmtCheck->execute();
            $resultCheck = $stmtCheck->get_result();

            if ($resultCheck->num_rows === 0) {
                // If the availability does not exist, insert it
                $stmt3 = $mysqli->prepare("
                    INSERT INTO doctor_schedule (doctor_id, availability)
                    SELECT id, ? FROM doctors WHERE email = ?
                ");
                $stmt3->bind_param("ss", $availabilityValue, $doctorEmail);
                $stmt3->execute();
            }
        }

        // Commit the transaction
        $mysqli->commit();

        // Update session variables
        $_SESSION['username'] = $newUsername;
        $_SESSION['specialist'] = $newSpecialist;
        $_SESSION['address'] = $newAddress;

        // Redirect to the profile page
        header("Location: Doctors_profile.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction on error
        $mysqli->rollback();
        echo "Error updating profile: " . $e->getMessage();
    }
}
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
        <h1>Edit Doctors Profile</h1>
    </header>

    <div class="profile-container">
    <form action="updated_doctor_profile.php" method="POST">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($doctorUsername); ?>" required>

        <label for="specialist">Specialist</label>
        <input type="text" id="specialist" name="specialist" value="<?php echo htmlspecialchars($specialist); ?>" required>

        <label for="address">Address</label>
        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($address); ?>" required>

        <label>Availability</label>
        <ul class="availability-list">
        <?php if (!empty($availabilityList)): ?>
            <?php foreach ($availabilityList as $availability): ?>
                <li>
                    <input type="text" name="availability[<?php echo htmlspecialchars($availability['id']); ?>]" value="<?php echo htmlspecialchars($availability['availability']); ?>" required />
                    <button type="button" class="delete-availability" onclick="markForDeletion(this, <?php echo (int) $availability['id']; ?>)">Delete</button>
                </li><br>
            <?php endforeach; ?>
        <?php else: ?>
            <li>
                <input type="text" name="availability[]" placeholder="Enter availability" required />
            </li>
        <?php endif; ?>
        </ul>

        <!-- Hidden input to store the deleted availability IDs -->
        <input type="hidden" id="delete_availability" name="delete_availability" value="">

        <button type="button" onclick="addAvailability()">Add Availability</button><br>

        <button type="submit">Update Profile</button><br>

        <a href="Doctor_profile.php">
            <button type="submit">Back</button>
        </a>
    </form>
</div>

<script>
    // Add a new availability field dynamically
    function addAvailability() {
        const ul = document.querySelector('.availability-list');
        const li = document.createElement('li');
        li.innerHTML = `<input type="text" name="availability[]" placeholder="Enter availability" required>
                        <button type="button" onclick="removeAvailability(this)">Delete</button>`;
        ul.appendChild(li);
    }

    // Mark availability for deletion
    function markForDeletion(button, id) {
        const form = button.closest('form');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'delete_availability[]';
        input.value = id;
        form.appendChild(input);
        button.parentElement.remove();
    }
</script>

</script>
</body>
</html>
