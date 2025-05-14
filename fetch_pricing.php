<?php
$host = 'localhost';
$db   = 'Shantikiran';
$user = 'root';
$pass = ''; // Change this to your MySQL password

$conn = new mysqli($host, $user, $pass, $db);

// Check DB connection
if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed.']));
}

$sql = "SELECT yoga_type, description, price FROM yoga_plans";
$result = $conn->query($sql);

$plans = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $plans[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($plans);
$conn->close();
?>
