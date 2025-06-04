<?php
require_once 'includes/db.php';

try {
    $email = 'congthinh@meandyou.com';
    $password = '12345678';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $query = "INSERT INTO users (email, password, status, role) 
              VALUES (?, ?, 'active', 'admin')
              ON DUPLICATE KEY UPDATE password = ?, status = 'active'";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $email, $hashed_password, $hashed_password);
    
    if ($stmt->execute()) {
        echo "Test user created successfully!\n";
        echo "Email: congthing@example.com\n";
        echo "Password: 12345678\n";
    } else {
        echo "Error creating test user: " . $stmt->error;
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}