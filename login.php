<?php
// db config
$db_server = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "rauldb";

// establish connection
$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// form check
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // form validation
    if (empty($email) || empty($password)) {
        die("Both email and password are required!");
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format!");
    }

    // check role
    $role = $_POST['role'] ?? '';
    if (empty($role)) {
        die("Please select a role!");
    }

    // determine role
    if ($role === 'student') {
        $sql = "SELECT * FROM STUDENT WHERE student_email = ?";
    } elseif ($role === 'lecturer') {
        $sql = "SELECT * FROM LECTURER WHERE lec_email = ?";
    } else {
        die("Invalid role selected!");
    }

    // sql statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Database error: " . $conn->error);
    }

    
    $stmt->bind_param("s", $email);
    $stmt->execute();

    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    // user check
    if ($user) {
        // pw verification
        if ($role === 'student' && password_verify($password, $user['student_password'])) {
            // Start session and set session variables
            session_start();
            $_SESSION['user_id'] = $user['student_id'];
            $_SESSION['role'] = 'student';
            echo "Login successful! Welcome, " . $user['student_name'];
        } elseif ($role === 'lecturer' && password_verify($password, $user['lec_password'])) {
            // start session
            session_start();
            $_SESSION['user_id'] = $user['lec_id'];
            $_SESSION['role'] = 'lecturer';
            echo "Login successful! Welcome, " . $user['lec_name'];
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No user found with that email!";
    }

    
    $stmt->close();
    $conn->close();
} 
    
?>
