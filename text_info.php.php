<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file
require_once "config.php";

// Prepare a select statement to retrieve user information
$sql = "SELECT id, username, ip_address, times FROM users WHERE id = ?";

if($stmt = mysqli_prepare($link, $sql)){
    // Bind variables to the prepared statement as parameters
    mysqli_stmt_bind_param($stmt, "i", $param_id);

    // Set parameters
    $param_id = $_SESSION["id"];

    // Attempt to execute the prepared statement
    if(mysqli_stmt_execute($stmt)){
        // Store result
        mysqli_stmt_bind_result($stmt, $id, $username, $ip_address, $times);
        mysqli_stmt_fetch($stmt);
    } else{
        echo "Oops! Something went wrong. Please try again later.";
    }

    // Close statement
    mysqli_stmt_close($stmt);
}

// Close connection
mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Information</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <h2>Your Information</h2>
        <p>Here is the information from your account:</p>
        <table border="1">
            <tr>
                <th>Username</th>
                <td><?php echo htmlspecialchars($username); ?></td>
            </tr>
            <tr>
                <th>IP Address</th>
                <td><?php echo htmlspecialchars($ip_address); ?></td>
            </tr>
            <tr>
                <th>Total Logins</th>
                <td><?php echo htmlspecialchars($times); ?></td>
            </tr>
        </table>
        <p><a href="welcome.php" class="btn btn-secondary">Back to Welcome</a></p>
    </div>
</body>
</html>
