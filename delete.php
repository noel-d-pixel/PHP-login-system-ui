<?php
require_once "config.php";

// Check if the user ID is passed via URL
if(isset($_GET["id"]) && !empty($_GET["id"])){
    $id = $_GET["id"];
    
    // Prepare DELETE statement
    $sql = "DELETE FROM users WHERE id = ?";
    
    if($stmt = mysqli_prepare($link, $sql)){
        // Bind the ID to the statement
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        // Execute the statement
        if(mysqli_stmt_execute($stmt)){
            // After successful deletion, redirect back to the welcome page
            header("location: welcome.php");
            exit();
        } else {
            echo "Error deleting the user.";
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing the statement.";
    }

    // Close connection
    mysqli_close($link);
} else {
    echo "Invalid ID.";
}
?>
