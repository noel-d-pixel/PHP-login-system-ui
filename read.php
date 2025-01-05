<?php
require_once "config.php";

$sql = "SELECT * FROM users";
$result = mysqli_query($link, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Users</title>
</head>
<body>
    <h2>User List</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>IP Address</th>
            <th>Login Count</th>
            <th>Actions</th>
        </tr>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['username']; ?></td>
            <td><?php echo $row['ip_address']; ?></td>
            <td><?php echo $row['times']; ?></td>
            <td>
                <a href="update.php?id=<?php echo $row['id']; ?>">Update</a>
                <a href="delete.php?id=<?php echo $row['id']; ?>">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>

<?php mysqli_close($link); ?>
