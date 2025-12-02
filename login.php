<?php
require_once 'config.php';

// Check if the user is already logged in, if so then redirect to index page
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: index.php");
    exit;
}

$matric = $password = "";
$error_message = "";

// Check for successful registration message
if (isset($_GET['registration']) && $_GET['registration'] == 'success') {
    $error_message = "<p style='color: green;'>Registration successful! Please login.</p>";
}

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $matric = trim($_POST["matric"]);
    $password = $_POST["password"];
    $hashed_password = ""; // Initialize variable to satisfy IDE warning

    if (empty($matric) || empty($password)) {
        $error_message = "Please enter matric and password.";
    } else {
        // Prepare a select statement
        $sql = "SELECT matric, name, password FROM users WHERE matric = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $param_matric);
            $param_matric = $matric;
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                // Check if matric exists, then verify password
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($matric_db, $name_db, $hashed_password);
                    if ($stmt->fetch()) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct, start a new session
                            $_SESSION["loggedin"] = true;
                            $_SESSION["matric"] = $matric_db;
                            $_SESSION["name"] = $name_db;                            
                            
                            header("location: index.php"); // Redirect to index page
                            exit; // Crucial to prevent unexpected execution
                        } else {
                            $error_message = "Invalid username or password, try <a href='login.php'>login</a> again."; // Figure 6
                        }
                    }
                } else {
                    $error_message = "Invalid username or password, try <a href='login.php'>login</a> again."; // Figure 6
                }
            } else {
                $error_message = "Oops! Something went wrong. Please try again later.";
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
    <title>User Login</title>
    <style>
        label, input { display: block; margin-bottom: 5px; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>User Login</h2>
    
    <?php echo $error_message; ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label>Matric: <input type="text" name="matric" value="<?php echo htmlspecialchars($matric); ?>" required></label>
        <label>Password: <input type="password" name="password" required></label>
        <br>
        <input type="submit" value="Login">
    </form>
    <p><a href="register.php">Register here if you have not.</a></p>
</body>
</html>