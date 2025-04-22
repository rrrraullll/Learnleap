<?php
// db config
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "rauldb";

// establish connection
$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

// check the connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// confirm connection
echo "You are connected<br>";

// error debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = $_POST['role'] ?? ''; // 'student' or 'lecturer'

    // form validation
    if (empty($name) || empty($email) || empty($password) || empty($role)) {
        die("All fields are required!");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format!");
    }

    // password hash
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // table selection
    if ($role === 'student') {
        $sql = "INSERT INTO STUDENT (student_name, student_email, student_password) VALUES (?, ?, ?)";
    } elseif ($role === 'lecturer') {
        $sql = "INSERT INTO LECTURER (lec_name, lec_email, lec_password) VALUES (?, ?, ?)";
    } else {
        die("Invalid role selected!");
    }

    // sql statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $name, $email, $hashedPassword);
    if ($stmt->execute()) {
        $_SESSION['registration_success'] = "Registration successful! Welcome, $name.";
        // After successful registration, redirect to test.php
        
        header("Location: test.php");
        exit(); 
    } else {
        echo "Error: " . $stmt->error;
    }

    
    $stmt->close();
    $conn->close();
} 


?>

