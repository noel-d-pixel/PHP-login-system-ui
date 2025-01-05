<?php
require_once 'config.php';

if ($link) {
    echo "Database connection successful!";
} else {
    echo "Database connection failed: " . mysqli_connect_error();
}
?>
