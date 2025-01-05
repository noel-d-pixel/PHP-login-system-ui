<?php
require_once "config.php";

// Check if the user ID is passed via URL
if(isset($_GET["id"]) && !empty($_GET["id"])){
    $id = $_GET["id"];
    $sql = "SELECT * FROM users WHERE id = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        // Execute statement
        if(mysqli_stmt_execute($stmt)){
            $result = mysqli_stmt_get_result($stmt);
            if(mysqli_num_rows($result) == 1){
                // Fetch the user's data
                $row = mysqli_fetch_assoc($result);
                $username = $row["username"];
                $ip_address = $row["ip_address"];
                $times = $row["times"];
            } else {
                echo "No user found with this ID.";
                exit;
            }
        } else {
            echo "Error executing query.";
            exit;
        }
        mysqli_stmt_close($stmt);
    }
} else {
    echo "Invalid ID.";
    exit;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Sanitize input
    $username = trim($_POST["username"]);
    $ip_address = trim($_POST["ip_address"]);  // Allow editing IP address
    $times = trim($_POST["times"]);

    // Validate times
    if (!filter_var($times, FILTER_VALIDATE_INT)) {
        echo "Invalid number of logins.";
        exit;
    }

    // Prepare an update statement
    $sql = "UPDATE users SET username = ?, ip_address = ?, times = ? WHERE id = ?";

    if($stmt = mysqli_prepare($link, $sql)){
        // Bind variables to the prepared statement
        mysqli_stmt_bind_param($stmt, "ssii", $username, $ip_address, $times, $id);

        // Execute the update
        if(mysqli_stmt_execute($stmt)){
            header("location: welcome.php"); // Redirect to the welcome page after successful update
            exit();
        } else {
            echo "Error updating profile.";
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .login-container {
            width: 450px;
            margin: 80px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            font-size: 28px;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            color: #777;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-size: 16px;
            font-weight: 600;
            color: #444;
            display: block;
            margin-bottom: 8px;
        }

        input.form-control {
            width: 90%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
            background-color: #f9f9f9;
            transition: all 0.3s ease;
        }

        input.form-control:focus {
            border-color: #4CAF50;
            background-color: #fff;
        }

        input[type="submit"] {
            width: 60%;
            padding: 12px;
            background-color: #4CAF50;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        p a {
            color: #4CAF50;
            text-decoration: none;
        }

        p a:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .login-container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Update Profile</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $id; ?>" method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>" required>
            </div>
            <div class="form-group">
                <label>IP Address</label>
                <input type="text" name="ip_address" class="form-control" value="<?php echo $ip_address; ?>" required>
            </div>
            <div class="form-group">
                <label>Times Logged In</label>
                <input type="number" name="times" class="form-control" value="<?php echo $times; ?>" required>
            </div>
            <div class="form-group">
                <input type="submit" value="Update">
                <p><a href="welcome.php">Cancel</a></p>
            </div>
        </form>
    </div>
</body>
</html>
