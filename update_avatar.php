<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    die('Not authorized');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $file = $_FILES['avatar'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    try {
        // Validate file
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, PNG and GIF allowed.');
        }

        if ($file['size'] > $maxSize) {
            throw new Exception('File too large. Maximum size is 5MB.');
        }

        // Create uploads directory if it doesn't exist
        $uploadDir = 'uploads/avatars/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename
        $filename = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            // Update database
            $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE id = ?");
            $stmt->execute([$filename, $_SESSION['user_id']]);

            // Delete old avatar if it exists
            $oldAvatarStmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
            $oldAvatarStmt->execute([$_SESSION['user_id']]);
            $oldAvatar = $oldAvatarStmt->fetchColumn();

            if ($oldAvatar && $oldAvatar !== 'default.jpg' && file_exists($uploadDir . $oldAvatar)) {
                unlink($uploadDir . $oldAvatar);
            }

            echo json_encode(['success' => true, 'avatar' => $filename]);
        } else {
            throw new Exception('Failed to upload file.');
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    }
}