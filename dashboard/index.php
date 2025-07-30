<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /auth');
    exit;
}

require_once __DIR__ . '/../includes/db.php';

$user_id = $_SESSION['user_id'];

$stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if user data is retrieved
if (!$user) {
    // User not found in database, clear session and redirect
    session_destroy();
    header('Location: /auth');
    exit;
}

// Profile picture options (static for now)
$profile_pic_dir = __DIR__ . '/../images/user_content/pre-made/profile_pictures/';
$profile_pic_url_base = '/images/user_content/pre-made/profile_pictures/';
$profile_pictures = [];
foreach (glob($profile_pic_dir . '*.jpg') as $file) {
    $profile_pictures[] = $profile_pic_url_base . basename($file);
}

// Set default profile picture if user doesn't have one
$defaultProfilePic = $profile_pictures[0] ?? '/images/placeholder_square.jpg';
$userProfilePic = $user['profile_picture'] ?? $defaultProfilePic;

function profile_pic_title($path) {
    $name = basename($path, '.jpg');
    return htmlspecialchars($name);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="/css/main_stylesheet.css">
    <link rel="stylesheet" href="/css/dashboard_stylesheet.css">
</head>
<body>
<?php include __DIR__ . '/../headers/main_header.php'; ?>
<main class="dashboard-container">
    <div class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($user['display_name'] ?? 'User'); ?>!</h1>
        <div class="user-info">
            <strong>Username:</strong> <span><?php echo htmlspecialchars($user['username'] ?? 'Not set'); ?></span><br>
            <strong>Email:</strong> <span><?php echo htmlspecialchars($user['email'] ?? 'Not set'); ?></span>
        </div>

    </div>
    
    <div class="dashboard-forms-left">
        <form class="dashboard-form" id="profile-form" method="POST">
            <label for="display_name">Display Name:</label>
            <input type="text" name="display_name" id="display_name" value="<?php echo htmlspecialchars($user['display_name'] ?? ''); ?>" maxlength="32" required>
            <button type="submit">Update Display Name</button>
            <div id="display-name-info"></div>
        </form>
        
        <form class="dashboard-form" id="profile-picture-form" method="POST">
            <label>Profile Picture:</label>
            <div class="profile-picture-select">
                <img id="current-profile-pic" src="<?php echo htmlspecialchars($userProfilePic); ?>" alt="Profile">
            </div>
            <input type="hidden" name="profile_picture" id="profile_picture" value="<?php echo htmlspecialchars($userProfilePic); ?>">
            <button type="submit">Update Profile Picture</button>
        </form>
    </div>
    
    <div class="dashboard-forms-center">
        <form class="dashboard-form" id="email-form" method="POST">
            <label for="new_email">Change Email:</label>
            <input type="email" name="new_email" id="new_email" placeholder="New Email" required>
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password_email" id="current_password_email" placeholder="Current Password" required>
            <button type="submit">Change Email</button>
        </form>
    </div>
    
    <div class="dashboard-forms-right">
        <form class="dashboard-form" id="password-form" method="POST">
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" id="current_password" placeholder="Current Password" required>
            <label for="new_password">New Password:</label>
            <input type="password" name="new_password" id="new_password" placeholder="New Password" required>
            <button type="submit">Change Password</button>
        </form>
    </div>
    
    <div class="account-actions">
        <form method="POST" action="/api/logout.php" style="display:inline;">
            <button type="submit">Log Out</button>
        </form>
    </div>
</main>
<div id="profile-pic-modal">
    <div class="profile-pic-modal-content">
        <h2>Choose a Profile Picture</h2>
        <div id="profile-pic-grid">
            <?php if (!empty($profile_pictures)): ?>
                <?php foreach ($profile_pictures as $pic): ?>
                    <div class="profile-pic-option" data-pic="<?php echo htmlspecialchars($pic); ?>">
                        <img src="<?php echo htmlspecialchars($pic); ?>" alt="Profile Option">
                        <div class="pic-title"><?php echo profile_pic_title($pic); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No profile pictures available.</p>
            <?php endif; ?>
        </div>
        <div class="modal-buttons">
            <button id="close-profile-pic-modal" class="modal-button cancel">Cancel</button>
            <button id="confirm-profile-pic-modal" class="modal-button confirm">Confirm</button>
        </div>
    </div>
</div>
<script>
window.profilePictures = <?php echo json_encode($profile_pictures); ?>;
window.currentProfilePic = <?php echo json_encode($userProfilePic); ?>;
</script>
<script src="/js/dashboard_profile_pic.js"></script>
<script src="/js/dashboard_forms.js"></script>
<?php include __DIR__ . '/../footers/main_footer.php'; ?>
</body>
</html> 