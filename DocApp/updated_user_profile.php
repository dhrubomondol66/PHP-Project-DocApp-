<?php
session_start();
$conn = include('db.php'); // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}

// Get user details from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();

// Process form submission to update the user name
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'])) {
    $new_username = htmlspecialchars($_POST['username']);

    // Update the user's name in the database
    $update_query = "UPDATE users SET username = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param('si', $new_username, $user_id);

    if ($update_stmt->execute()) {
        // Success message or redirect to another page
        $success_message = "Username updated successfully!";
    } else {
        $error_message = "Failed to update username. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <style>
        body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

.profile-container {
    background-color: #fff;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    width: 400px;
    text-align: center;
}

h1 {
    font-size: 2em;
    color: #333;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
    text-align: left;
}

label {
    font-size: 16px;
    color: #333;
    display: block;
    margin-bottom: 5px;
}

input[type="text"] {
    width: 100%;
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

button[type="submit"] {
    background-color: #3498db;
    color: white;
    padding: 12px 25px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    cursor: pointer;
    transition: background-color 0.3s;
}

button[type="submit"]:hover {
    background-color: #2980b9;
}

.success {
    color: green;
    font-size: 16px;
    margin-bottom: 15px;
}

.error {
    color: red;
    font-size: 16px;
    margin-bottom: 15px;
}

@media (max-width: 768px) {
    .profile-container {
        width: 90%;
        padding: 20px;
    }

    h1 {
        font-size: 1.8em;
    }

    input[type="text"] {
        font-size: 14px;
    }

    button[type="submit"] {
        font-size: 14px;
    }
}
/* General button styling */
.btn {
    display: inline-block;
    padding: 10px 20px;
    font-size: 16px;
    font-weight: bold;
    text-align: center;
    color: #ffffff;
    background-color: #3498db; /* Blue color */
    border: none;
    border-radius: 5px;
    text-decoration: none; /* Remove underline for links styled as buttons */
    cursor: pointer;
    transition: background-color 0.3s, transform 0.2s;
}

/* Hover effect */
.btn:hover {
    background-color: #2980b9; /* Darker blue */
    transform: translateY(-2px); /* Slight upward lift */
}

/* Active effect */
.btn:active {
    background-color: #1c6ea4; /* Even darker blue */
    transform: translateY(1px); /* Slight downward press */
}

    </style>
    <div class="profile-container">
        <h1>Update Your Profile</h1>

        <!-- Display success or error message -->
        <?php if (isset($success_message)) { echo "<p class='success'>$success_message</p>"; } ?>
        <?php if (isset($error_message)) { echo "<p class='error'>$error_message</p>"; } ?>

        <!-- Form to update user name -->
        <form method="POST" action="updated_user_profile.php">
            <div class="form-group">
                <label for="username">New Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user_data['username']); ?>" required>
            </div>

            <form action="update_name.php" method="POST">
                <button type="submit" class="btn">Update Name</button>
            </form><br>

            <a href="Users_profile.php" class="btn">Back</a>

        </form>
    </div>
</body>

</html>
