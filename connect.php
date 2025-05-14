<?php
// Allow cross-origin requests (for development; remove in production)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// Read the JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Check if data is valid
if (!isset($data['username'], $data['password'], $data['email'], $data['contact'])) {
    echo "Invalid input.";
    exit;
}

// Extract and sanitize input
$username = htmlspecialchars(trim($data['username']));
$password = password_hash(trim($data['password']), PASSWORD_DEFAULT); // Secure hash
$email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
$contact = htmlspecialchars(trim($data['contact']));

// Database credentials
$servername = "localhost";
$dbUsername = "root";       // Change if needed
$dbPassword = "";           // Change if needed
$dbName = "Shantikiran";

// Create connection
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if email already exists
$checkQuery = $conn->prepare("SELECT id FROM users WHERE email = ?");
$checkQuery->bind_param("s", $email);
$checkQuery->execute();
$checkQuery->store_result();

if ($checkQuery->num_rows > 0) {
    echo "Email is already registered.";
} else {
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, password, email, contact) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $email, $contact);

    if ($stmt->execute()) {
        echo "Account created successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$checkQuery->close();
$conn->close();
?>
