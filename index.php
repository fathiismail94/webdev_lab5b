<?php
require_once 'config.php';

// Check if the user is logged in, otherwise redirect to login page (Question 8)
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// SQL query to select matric, name, and role
$sql = "SELECT matric, name, role FROM users";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User List</title>
    <style>
        table { border-collapse: collapse; width: 60%; margin-top: 20px; }
        th, td { border: 2px solid black; padding: 10px; text-align: left; }
        th { background-color: #f0f0f0; text-align: center; }
        td a { margin-right: 5px; text-decoration: none; color: blue; }
        td a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION["name"]); ?>!</h2>
    <p><a href="logout.php">Logout</a></p>

    <h3>Registered Users</h3>
    <table>
        <tr>
            <th>Matric</th>
            <th>Name</th>
            <th>Level</th>
            <th>Action</th> </tr>
        
        <?php
        if ($result->num_rows > 0) {
            // Output data of each row
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row["matric"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["name"]) . "</td>";
                echo "<td>" . htmlspecialchars($row["role"]) . "</td>";
                // Action links (Figure 7)
                echo "<td>";
                echo "<a href='update.php?matric=" . $row["matric"] . "'>Update</a>";
                // Simple JavaScript confirmation for delete
                echo "<a href='delete.php?matric=" . $row["matric"] . "' onclick=\"return confirm('Are you sure you want to delete this user?');\">Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No users found.</td></tr>";
        }
        $conn->close();
        ?>
    </table>
</body>
</html>