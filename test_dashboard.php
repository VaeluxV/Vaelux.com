<?php
session_start();
require_once __DIR__ . '/includes/db.php';

echo "<h2>Dashboard Debug Test</h2>";

echo "<h3>Session Data:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    echo "<h3>Querying user ID: $user_id</h3>";
    
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h3>User Data:</h3>";
    echo "<pre>";
    print_r($user);
    echo "</pre>";
    
    if ($user) {
        echo "<h3>Individual Fields:</h3>";
        echo "Username: " . ($user['username'] ?? 'NULL') . "<br>";
        echo "Email: " . ($user['email'] ?? 'NULL') . "<br>";
        echo "Display Name: " . ($user['display_name'] ?? 'NULL') . "<br>";
        echo "Profile Picture: " . ($user['profile_picture'] ?? 'NULL') . "<br>";
    } else {
        echo "<p style='color: red;'>No user found with ID: $user_id</p>";
    }
} else {
    echo "<p style='color: red;'>No user_id in session</p>";
}

echo "<h3>All Users in Database:</h3>";
$stmt = $db->prepare("SELECT id, username, email, display_name FROM users");
$stmt->execute();
$allUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($allUsers);
echo "</pre>";
?> 