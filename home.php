<?php
// home.php - Dashboard with Profile Settings
session_start();

if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

include "db.php";

$user_id = $_SESSION["user_id"];
$message = "";
$message_type = "";

// Handle profile update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["update_name"])) {
        $new_name = trim($_POST["fullname"]);
        if (!empty($new_name)) {
            $stmt = $conn->prepare("UPDATE users SET fullname = ? WHERE id = ?");
            $stmt->bind_param("si", $new_name, $user_id);
            if ($stmt->execute()) {
                $_SESSION["user"] = $new_name;
                $message = "Name updated successfully!";
                $message_type = "success";
            } else {
                $message = "Failed to update name!";
                $message_type = "error";
            }
            $stmt->close();
        }
    }
    
    if (isset($_POST["change_password"])) {
        $current_password = $_POST["current_password"];
        $new_password = $_POST["new_password"];
        $confirm_password = $_POST["confirm_password"];
        
        // Get current password from database
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (password_verify($current_password, $user["password"])) {
            if ($new_password == $confirm_password) {
                if (strlen($new_password) >= 4) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                    $update_stmt->bind_param("si", $hashed_password, $user_id);
                    if ($update_stmt->execute()) {
                        $message = "Password changed successfully!";
                        $message_type = "success";
                    } else {
                        $message = "Failed to change password!";
                        $message_type = "error";
                    }
                    $update_stmt->close();
                } else {
                    $message = "New password must be at least 4 characters!";
                    $message_type = "error";
                }
            } else {
                $message = "New passwords do not match!";
                $message_type = "error";
            }
        } else {
            $message = "Current password is incorrect!";
            $message_type = "error";
        }
        $stmt->close();
    }
}

// Get user data
$stmt = $conn->prepare("SELECT fullname, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<nav class="navbar">
    <div class="logo">dev<span>hub</span></div>
    <a href="logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</nav>

<div class="home-container">
    <?php if ($message != ""): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <i class="fas <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <span><?php echo $message; ?></span>
        </div>
    <?php endif; ?>

    <div class="welcome-card">
        <div class="avatar">
            <i class="fas fa-user-astronaut"></i>
        </div>
        <h1>Welcome back, <?php echo htmlspecialchars($_SESSION["user"]); ?>! 🎉</h1>
        <div class="email">
            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user_data['email']); ?>
        </div>
        <div class="member-since">
            <i class="fas fa-calendar"></i> Joined <?php echo date('F Y', strtotime($user_data['created_at'])); ?>
        </div>
    </div>

    <!-- Profile Settings Section -->
    <div class="profile-section">
        <div class="section-header">
            <i class="fas fa-user-cog"></i>
            <h3>Profile Settings</h3>
        </div>
        
        <div class="profile-grid">
            <!-- Update Name Card -->
            <div class="profile-card">
                <div class="profile-icon">
                    <i class="fas fa-user-edit"></i>
                </div>
                <h4>Update Name</h4>
                <form method="POST" class="profile-form">
                    <input type="text" name="fullname" placeholder="New Full Name" value="<?php echo htmlspecialchars($user_data['fullname']); ?>" required>
                    <button type="submit" name="update_name" class="btn-update">
                        <i class="fas fa-save"></i> Update Name
                    </button>
                </form>
            </div>

            <!-- Change Password Card -->
            <div class="profile-card">
                <div class="profile-icon">
                    <i class="fas fa-key"></i>
                </div>
                <h4>Change Password</h4>
                <form method="POST" class="profile-form">
                    <input type="password" name="current_password" placeholder="Current Password" required>
                    <input type="password" name="new_password" placeholder="New Password (min 4 chars)" required>
                    <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
                    <button type="submit" name="change_password" class="btn-update">
                        <i class="fas fa-lock"></i> Change Password
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>