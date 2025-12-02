<?php
require_once 'config.php';

// Session check (Question 8)
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if matric parameter is set and is not empty
if (isset($_GET["matric"]) && !empty(trim($_GET["matric"]))) {
    $matric_to_delete = trim($_GET["matric"]);

    // Prepare a delete statement
    $sql = "DELETE FROM users WHERE matric = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("s", $param_matric);
        $param_matric = $matric_to_delete;
        
        if ($stmt->execute()) {
            // Success, redirect to index page
            header("location: index.php");
            exit();
        } else {
            echo "Error deleting record: " . $stmt->error;
        }
        $stmt->close();
    }
    $conn->close();
} else {
    // If matric parameter is not set, redirect to index
    header("location: index.php");
    exit();
}
?>