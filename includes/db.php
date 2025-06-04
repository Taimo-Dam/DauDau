<?php
// filepath: c:\xampp\htdocs\web\Nhóm7\includes\db.php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Database configuration
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'MeandYou';

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
/**
 * Utility function to safely execute a query
 * 
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters to bind
 * @return mysqli_result|false
 */
function executeQuery($sql, $params = []) {
    global $conn;
    
    try {
        $stmt = $conn->prepare($sql);
        if ($params) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result();
    } catch (mysqli_sql_exception $e) {
        error_log("Query execution error: " . $e->getMessage() . " | Query: " . $sql);
        return false;
    }
}

/**
 * Utility function to get a single record
 * 
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters to bind
 * @return array|false Single record or false if not found
 */
function fetchOne($sql, $params = []) {
    $result = executeQuery($sql, $params);
    return ($result) ? $result->fetch_assoc() : false;
}

/**
 * Utility function to get multiple records
 * 
 * @param string $sql SQL query with placeholders
 * @param array $params Parameters to bind
 * @return array Array of records
 */
function fetchAll($sql, $params = []) {
    $result = executeQuery($sql, $params);
    return ($result) ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Utility function to insert a record and return the ID
 * 
 * @param string $table Table name
 * @param array $data Associative array of column => value
 * @return int|false The last insert ID or false on failure
 */
function insert($table, $data) {
    global $conn;
    
    try {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $conn->prepare($sql);
        $types = str_repeat('s', count($data));
        $stmt->bind_param($types, ...array_values($data));
        $stmt->execute();
        
        return $conn->insert_id;
    } catch (mysqli_sql_exception $e) {
        error_log("Insert error: " . $e->getMessage());
        return false;
    }
}

/**
 * Utility function to update a record
 * 
 * @param string $table Table name
 * @param array $data Associative array of column => value to update
 * @param array $where Associative array of column => value for WHERE clause
 * @return int|false Number of affected rows or false on failure
 */
function update($table, $data, $where) {
    global $conn;
    
    try {
        $setParts = [];
        $params = [];
        
        foreach ($data as $column => $value) {
            $setParts[] = "{$column} = ?";
            $params[] = $value;
        }
        
        $whereParts = [];
        foreach ($where as $column => $value) {
            $whereParts[] = "{$column} = ?";
            $params[] = $value;
        }
        
        $setClause = implode(', ', $setParts);
        $whereClause = implode(' AND ', $whereParts);
        
        $sql = "UPDATE {$table} SET {$setClause} WHERE {$whereClause}";
        $stmt = $conn->prepare($sql);
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        return $stmt->affected_rows;
    } catch (mysqli_sql_exception $e) {
        error_log("Update error: " . $e->getMessage());
        return false;
    }
}

/**
 * Utility function to delete a record
 * 
 * @param string $table Table name
 * @param array $where Associative array of column => value for WHERE clause
 * @return int|false Number of affected rows or false on failure
 */
function delete($table, $where) {
    global $conn;
    
    try {
        $whereParts = [];
        $params = [];
        
        foreach ($where as $column => $value) {
            $whereParts[] = "{$column} = ?";
            $params[] = $value;
        }
        
        $whereClause = implode(' AND ', $whereParts);
        
        $sql = "DELETE FROM {$table} WHERE {$whereClause}";
        $stmt = $conn->prepare($sql);
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        
        return $stmt->affected_rows;
    } catch (mysqli_sql_exception $e) {
        error_log("Delete error: " . $e->getMessage());
        return false;
    }
}

/**
 * Update listening history for a user
 * 
 * @param int $userId User ID
 * @param int $songId Song ID
 * @return bool True on success, false on failure
 */
function updateListeningHistory($userId, $songId) {
    global $conn;
    
    try {
        // Insert new listening record
        $sql = "INSERT INTO listening_history (user_id, song_id, listened_at) 
                VALUES (?, ?, CURRENT_TIMESTAMP)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $songId);
        return $stmt->execute();
    } catch (Exception $e) {
        error_log("Error updating listening history: " . $e->getMessage());
        return false;
    }
}
function updatePlayCount($songId) {
    global $conn;
    $sql = "UPDATE songs SET play_count = play_count + 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $songId);
    $stmt->execute();
}