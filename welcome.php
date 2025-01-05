<?php
// Initialize the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Database connection
$link = mysqli_connect("localhost", "root", "", "demo"); // Use your database name here

// Check connection
if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Query to select users
$sql = "SELECT * FROM users";
$result = mysqli_query($link, $sql);

// Check for query errors
if ($result === false) {
    die("ERROR: Could not execute query: $sql. " . mysqli_error($link));
}

// Fetch all user data for the pie chart
$login_counts = [];
$usernames = [];
$user_ids = [];
while ($row = mysqli_fetch_assoc($result)) {
    $login_counts[] = $row['times'];  // Collect login count for each user
    $usernames[] = $row['username'];  // Collect username for labels
    $user_ids[] = $row['id'];         // Collect user IDs
}

// Reset the result pointer for table rendering
mysqli_data_seek($result, 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <link rel="stylesheet" href="style2.css">
    <!-- Include Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="welcome-container">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to our site.</h1>
        <p class="welcome-links">
            <a href="reset-password.php" class="btn btn-warning">Reset Your Password</a>
            <a href="logout.php" class="btn btn-danger ml-3">Sign Out of Your Account</a>
        </p>

        <h2>User List</h2>
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>IP Address</th>
                    <th>Login Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['username']; ?></td>
                    <td><?php echo $row['ip_address']; ?></td>
                    <td><?php echo $row['times']; ?></td>
                    <td>
                        <a href="update.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">Update</a>
                        <a href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="btn btn-danger btn-sm">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Login Count Distribution</h2>
        <div>
            <canvas id="loginPieChart" width="400" height="400"></canvas>
        </div>
    </div>

    <script>
        // Data for the pie chart
        const loginCounts = <?php echo json_encode($login_counts); ?>;
        const usernames = <?php echo json_encode($usernames); ?>;
        const userIds = <?php echo json_encode($user_ids); ?>;

        // Keep track of which users are currently selected
        let selectedUsers = Array(usernames.length).fill(true);  // All users are selected initially

        // Pie chart data
        const getChartData = () => {
            // Filter the data based on selected users
            const filteredUsernames = [];
            const filteredLoginCounts = [];
            const filteredBackgroundColors = [];

            for (let i = 0; i < usernames.length; i++) {
                if (selectedUsers[i]) {
                    filteredUsernames.push(usernames[i]);
                    filteredLoginCounts.push(loginCounts[i]);
                    filteredBackgroundColors.push('#'+Math.floor(Math.random()*16777215).toString(16));  // Random color for each slice
                }
            }

            return {
                labels: filteredUsernames,
                datasets: [{
                    data: filteredLoginCounts,
                    backgroundColor: filteredBackgroundColors,
                    hoverBackgroundColor: filteredBackgroundColors,
                }]
            };
        };

        // Pie chart configuration
        const config = {
            type: 'pie',
            data: getChartData(),
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return usernames[tooltipItem.dataIndex] + ': ' + tooltipItem.raw + ' logins';
                            }
                        }
                    }
                },
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const clickedIndex = elements[0].index;
                        selectedUsers[clickedIndex] = !selectedUsers[clickedIndex];  // Toggle selection
                        chart.update();  // Re-render the chart
                    }
                }
            },
        };

        // Render the chart
        const ctx = document.getElementById('loginPieChart').getContext('2d');
        const chart = new Chart(ctx, config);
    </script>

</body>
</html>

<?php
// Close the database connection
mysqli_close($link);
?>
