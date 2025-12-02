<?php
require_once 'config.php';

$matric = $name = $password = $role = "";
$error_message = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric = trim($_POST["matric"]);
    $name = trim($_POST["name"]);
    $password = $_POST["password"]; 
    $role = $_POST["role"];

    if (empty($matric) || empty($name) || empty($password) || empty($role)) {
        $error_message = "Please fill in all fields.";
    } else {
        $sql = "INSERT INTO users (matric, name, password, role) VALUES (?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // Hash the password for security
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt->bind_param("ssss", $param_matric, $param_name, $param_password, $param_role);
            
            $param_matric = $matric;
            $param_name = $name;
            $param_password = $hashed_password;
            $param_role = $role;
            
            if ($stmt->execute()) {
                // Successful registration, redirect to login
                header("location: login.php?registration=success"); 
                exit();
            } else {
                if ($conn->errno == 1062) {
                    $error_message = "Registration failed: Matric number already exists.";
                } else {
                    $error_message = "Something went wrong. Please try again later. Error: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Registration</title>
    <style>
        label, input, select { display: block; margin-bottom: 5px; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>User Registration</h2>
    
    <?php if (!empty($error_message)): ?>
        <p class="error"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label>Matric: <input type="text" name="matric" value="<?php echo htmlspecialchars($matric); ?>" required></label>
        <label>Name: <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required></label>
        <label>Password: <input type="password" name="password" required></label>
        <label>Role:</label>
        <select name="role" required>
            <option value="">Please select</option>
            <option value="student" <?php if ($role == "student") echo "selected"; ?>>student</option>
            <option value="lecturer" <?php if ($role == "lecturer") echo "selected"; ?>>lecturer</option>
        </select>
        <br>
        <input type="submit" value="Submit">
    </form>
    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</body>
</html>