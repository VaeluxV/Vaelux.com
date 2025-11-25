<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}
require_once __DIR__ . '/../includes/db.php';
$user_id = $_SESSION['user_id'];
$profile_picture = trim($_POST['profile_picture'] ?? '');
$display_name = trim($_POST['display_name'] ?? '');

// Fetch user info
$stmt = $db->prepare('SELECT display_name, display_name_changes, display_name_last_changed, profile_picture_changes, profile_picture_last_changed FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found']);
    exit;
}

$updates = [];
$params = [];

// Handle profile picture change rate limiting
if ($profile_picture !== ($user['profile_picture'] ?? '')) {
    $now = new DateTime();
    $last_changed = $user['profile_picture_last_changed'] ? new DateTime($user['profile_picture_last_changed']) : null;
    $changes = (int)($user['profile_picture_changes'] ?? 0);
    
    if ($last_changed) {
        $interval = $now->getTimestamp() - $last_changed->getTimestamp();
        if ($interval > 600) { // 10 minutes
            $changes = 0;
        }
        if ($changes >= 8 && $interval <= 600) {
            http_response_code(429);
            echo json_encode(['error' => 'Profile picture change limit reached (8 per 10 minutes). Please wait 2 hours.']);
            exit;
        }
    }
    
    $changes++;
    $updates[] = 'profile_picture = ?, profile_picture_changes = ?, profile_picture_last_changed = ?';
    $params[] = $profile_picture;
    $params[] = $changes;
    $params[] = $now->format('Y-m-d H:i:s');
}

// Handle display name change limit
if ($display_name !== $user['display_name']) {
    $now = new DateTime();
    $last_changed = $user['display_name_last_changed'] ? new DateTime($user['display_name_last_changed']) : null;
    $changes = (int)$user['display_name_changes'];
    $can_change = true;
    
    if ($last_changed) {
        $interval = $now->getTimestamp() - $last_changed->getTimestamp();
        if ($interval > 48 * 3600) {
            $changes = 0;
        }
        if ($changes >= 3 && $interval <= 48 * 3600) {
            $can_change = false;
        }
    }
    
    if (!$can_change) {
        http_response_code(429);
        echo json_encode(['error' => 'Display name change limit reached (3 per 48h)']);
        exit;
    }
    
    $changes++;
    $updates[] = 'display_name = ?, display_name_changes = ?, display_name_last_changed = ?';
    $params[] = $display_name;
    $params[] = $changes;
    $params[] = $now->format('Y-m-d H:i:s');
}

// Update user if there are changes
if (!empty($updates)) {
    $params[] = $user_id;
    $sql = 'UPDATE users SET ' . implode(', ', $updates) . ' WHERE id = ?';
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
}

echo json_encode(['success' => true]); 