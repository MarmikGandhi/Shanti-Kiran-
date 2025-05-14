<?php
$host = 'localhost';
$db   = 'Shantikiran';
$user = 'root';
$pass = ''; // Change to your MySQL password

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Sanitize input
$full_name     = $_POST['full_name'] ?? '';
$email         = $_POST['email'] ?? '';
$phone         = $_POST['phone'] ?? '';
$address       = $_POST['address'] ?? '';
$city          = $_POST['city'] ?? '';
$state         = $_POST['state'] ?? '';
$zip_code      = $_POST['zip_code'] ?? '';
$country       = $_POST['country'] ?? '';
$yoga_type     = $_POST['yoga_type'] ?? '';
$price         = $_POST['price'] ?? '';
$payment_date  = $_POST['payment_date'] ?? '';
$transaction_id = $_POST['transaction_id'] ?? '';
$payer_name     = $_POST['payer_name'] ?? '';

// Step 1: Check if member exists
$stmt = $conn->prepare("SELECT member_id FROM members WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($member_id);
    $stmt->fetch();
} else {
    $stmt->close();
    $stmt = $conn->prepare("INSERT INTO members (full_name, email, phone, address, city, state, zip_code, country) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $full_name, $email, $phone, $address, $city, $state, $zip_code, $country);
    $stmt->execute();
    $member_id = $stmt->insert_id;
}

// Step 2: Get yoga_id
$stmt = $conn->prepare("SELECT yoga_id FROM yoga_plans WHERE yoga_type = ?");
$stmt->bind_param("s", $yoga_type);
$stmt->execute();
$stmt->bind_result($yoga_id);
$stmt->fetch();
$stmt->close();

// Step 3: Insert payment
$stmt = $conn->prepare("INSERT INTO payments (member_id, yoga_id, price, payment_date, transaction_id, payer_name, payment_status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
$stmt->bind_param("iissss", $member_id, $yoga_id, $price, $payment_date, $transaction_id, $payer_name);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "<h3>Payment recorded successfully!</h3>";
} else {
    echo "<h3>Error processing payment. Please try again.</h3>";
}

$conn->close();
?>
