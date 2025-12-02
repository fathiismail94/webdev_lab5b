<?php
require_once 'config.php';

// Session check (Question 8)
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$matric = $name = $role = "";
$error_message = "";
$input_matric = $_GET['matric'] ?? null; // Get matric from URL

if (empty($input_matric)) {
    header("location: index.php");
    exit;
}

// 1. Fetch existing user data
$sql_select = "SELECT matric, name, role FROM users WHERE matric = ?";
if ($stmt = $conn->prepare($sql_select)) {
    $stmt->bind_param("s", $input_matric);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $matric = $row['matric'];
            $name = $row['name'];
            $role = $row['role'];
        } else {
            $error_message = "No user found with that matric number.";
        }
    } else {
        $error_message = "Error fetching user data.";
    }
    $stmt->close();
}

// 2. Process form submission for update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $new_name = trim($_POST["name"]);
    $new_role = trim($_POST["role"]);

    // The matric number is passed hidden or derived from the URL (already set)
    $update_matric = $_POST['matric_hidden'] ?? $matric; 

    if (empty($new_name) || empty($new_role)) {
        $error_message = "Name and Access Level are required.";
    } else {
        $sql_update = "UPDATE users SET name = ?, role = ? WHERE matric = ?";
        
        if ($stmt_update = $conn->prepare($sql_update)) {
            $stmt_update->bind_param("sss", $param_name, $param_role, $param_matric);
            
            $param_name = $new_name;
            $param_role = $new_role;
            $param_matric = $update_matric; 
            
            if ($stmt_update->execute()) {
                header("location: index.php");
                exit();
            } else {
                $error_message = "Error updating record: " . $stmt_update->error;
            }
            $stmt_update->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update User</title>
    <style>
        label, input, select { display: block; margin-bottom: 5px; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Update User</h2>
    
    <?php if (!empty($error_message)): ?>
        <p class="error"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form action="update.php?matric=<?php echo htmlspecialchars($matric); ?>" method="post">
        <label>Matric: <input type="text" value="<?php echo htmlspecialchars($matric); ?>" disabled></label>
        <input type="hidden" name="matric_hidden" value="<?php echo htmlspecialchars($matric); ?>">

        <label>Name: <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required></label>
        <label>Access Level:</label>
        <select name="role" required>
            <option value="student" <?php if ($role == "student") echo "selected"; ?>>student</option>
            <option value="lecturer" <?php if ($role == "lecturer") echo "selected"; ?>>lecturer</option>
        </select>
        <br>
        <input type="submit" name="update_user" value="Update"> 
        <a href="index.php">Cancel</a>
    </form>
</body>
</html>