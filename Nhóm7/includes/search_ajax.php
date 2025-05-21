<?php
require_once 'db.php';
header('Content-Type: application/json');

if (isset($_GET['query'])) {
    $search = mysqli_real_escape_string($conn, $_GET['query']);
    
    if (strlen($search) >= 2) {
        try {
            // Search songs with Vietnamese character support
            $songsQuery = "SELECT id, title, artist, image_path FROM songs 
                          WHERE title LIKE ? OR artist LIKE ? 
                          COLLATE utf8mb4_unicode_ci
                          ORDER BY CASE 
                            WHEN artist LIKE ? THEN 1
                            WHEN artist LIKE ? THEN 2
                            ELSE 3
                          END
                          LIMIT 5";
            
            $stmt = $conn->prepare($songsQuery);
            $searchParam = "%$search%";
            $exactMatch = "$search";
            $stmt->bind_param("ssss", $searchParam, $searchParam, $exactMatch, $searchParam);
            $stmt->execute();
            $songsResult = $stmt->get_result();
            $songs = $songsResult->fetch_all(MYSQLI_ASSOC);

            // Search artists with Vietnamese character support
            $artistsQuery = "SELECT DISTINCT artist, 
                           MIN(image_path) as image_path,
                           COUNT(*) as song_count 
                           FROM songs 
                           WHERE artist LIKE ? 
                           COLLATE utf8mb4_unicode_ci
                           GROUP BY artist 
                           ORDER BY CASE 
                            WHEN artist LIKE ? THEN 1
                            WHEN artist LIKE ? THEN 2
                            ELSE 3
                           END
                           LIMIT 3";
            
            $stmt = $conn->prepare($artistsQuery);
            $stmt->bind_param("sss", $searchParam, $exactMatch, $searchParam);
            $stmt->execute();
            $artistsResult = $stmt->get_result();
            $artists = $artistsResult->fetch_all(MYSQLI_ASSOC);

            echo json_encode([
                'songs' => $songs,
                'artists' => $artists
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'Query too short']);
    }
} else {
    echo json_encode(['error' => 'No search query provided']);
}