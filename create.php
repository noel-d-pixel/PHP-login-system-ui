<?php
require_once "config.php";

$username = $password = $ip_address = "";
$username_err = $password_err = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        $username = trim($_POST["username"]);
    }

    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";
    } else{
        $password = password_hash(trim($_POST["password"]), PASSWORD_DEFAULT);
    }

    if(empty($username_err) && empty($password_err)){
        $sql = "INSERT INTO users (username, password, ip_address, times) VALUES (?, ?, ?, 0)";
        if($stmt = mysqli_prepare($link, $sql)){
            mysqli_stmt_bind_param($stmt, "sss", $username, $password, $ip_address);
            $ip_address = $_SERVER['REMOTE_ADDR'];
            if(mysqli_stmt_execute($stmt)){
                header("location: read.php");
                exit();
            } else{
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create User</title>
</head>
<body>
    <h2>Create User</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label>Username</label>
        <input type="text" name="username">
        <span><?php echo $username_err; ?></span><br>

        <label>Password</label>
        <input type="password" name="password">
        <span><?php echo $password_err; ?></span><br>

        <input type="submit" value="Create">
    </form>
</body>
</html>
