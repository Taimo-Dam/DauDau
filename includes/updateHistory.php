<?php
session_start();
require_once 'db.php';

header('Content-Type: application/json');

// Debug logging
error_log('UpdateHistory.php called with POST data: ' . print_r($_POST, true));

// Validate request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'error' => 'Method not allowed']));
}

if (!isset($_POST['songId'])) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Song ID required']));
}

try {
    $songId = (int)$_POST['songId'];
    $userId = $_SESSION['user_id'] ?? null;

    // Start transaction
    $conn->begin_transaction();

    // Update song play count
    $stmt = $conn->prepare("
        UPDATE songs 
        SET play_count = COALESCE(play_count, 0) + 1,
            last_played = CURRENT_TIMESTAMP
        WHERE id = ?
    ");
    
    if (!$stmt->bind_param("i", $songId)) {
        throw new Exception("Failed to bind parameters");
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update play count");
    }

    // Add to listening history if user is logged in
    if ($userId) {
        $stmt = $conn->prepare("
            INSERT INTO listening_history (user_id, song_id)
            VALUES (?, ?)
        ");
        
        if (!$stmt->bind_param("ii", $userId, $songId)) {
            throw new Exception("Failed to bind history parameters");
        }
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to add to listening history");
        }
    }

    // Get updated play count
    $stmt = $conn->prepare("SELECT play_count FROM songs WHERE id = ?");
    $stmt->bind_param("i", $songId);
    $stmt->execute();
    $result = $stmt->get_result();
    $playCount = $result->fetch_assoc()['play_count'];

    $conn->commit();

    echo json_encode([
        'success' => true,
        'playCount' => $playCount
    ]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Play count update error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'error' => 'Server error occurred'
    ]);
}