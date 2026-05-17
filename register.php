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

// Fetch user data
$stmt = $conn->prepare("SELECT fullname, email, created_at FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();
$stmt->close();

// Update Name
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_name"])) {
    $new_name = trim($_POST["fullname"]);
    
    if (empty($new_name)) {
        $message = "Name cannot be empty!";
        $message_type = "error";
    } else {
        $update_stmt = $conn->prepare("UPDATE users SET fullname = ? WHERE id = ?");
        $update_stmt->bind_param("si", $new_name, $user_id);
        
        if ($update_stmt->execute()) {
            $_SESSION["user"] = $new_name;
            $user_data['fullname'] = $new_name;
            $message = "Name updated successfully!";
            $message_type = "success";
        } else {
            $message = "Failed to update name!";
            $message_type = "error";
        }
        $update_stmt->close();
    }
}

// Update Password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_password"])) {
    $current_password = $_POST["current_password"];
    $new_password = $_POST["new_password"];
    $confirm_password = $_POST["confirm_password"];
    
    // Verify current password
    $pass_stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $pass_stmt->bind_param("i", $user_id);
    $pass_stmt->execute();
    $pass_result = $pass_stmt->get_result();
    $user_pass = $pass_result->fetch_assoc();
    $pass_stmt->close();
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $message = "All password fields are required!";
        $message_type = "error";
    } elseif (!password_verify($current_password, $user_pass['password'])) {
        $message = "Current password is incorrect!";
        $message_type = "error";
    } elseif ($new_password !== $confirm_password) {
        $message = "New passwords do not match!";
        $message_type = "error";
    } elseif (strlen($new_password) < 4) {
        $message = "Password must be at least 4 characters!";
        $message_type = "error";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_stmt->bind_param("si", $hashed_password, $user_id);
        
        if ($update_stmt->execute()) {
            $message = "Password changed successfully!";
            $message_type = "success";
            // Clear password fields
            echo '<script>document.getElementById("current_password").value = ""; document.getElementById("new_password").value = ""; document.getElementById("confirm_password").value = "";</script>';
        } else {
            $message = "Failed to change password!";
            $message_type = "error";
        }
        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - devhub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Profile Section Styles */
        .profile-section {
            background: white;
            border-radius: 32px;
            padding: 40px;
            margin-top: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a1c2e;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }

        .section-title i {
            color: #764ba2;
            font-size: 24px;
        }

        .profile-form {
            margin-bottom: 30px;
        }

        .form-row {
            margin-bottom: 20px;
        }

        .form-row label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4b5563;
            font-size: 14px;
        }

        .form-row input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            border-radius: 16px;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.3s;
            background: #f8f9fa;
        }

        .form-row input:focus {
            outline: none;
            border-color: #764ba2;
            background: white;
            box-shadow: 0 0 0 4px rgba(118, 75, 162, 0.1);
        }

        .btn-update {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 28px;
            border: none;
            border-radius: 30px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-update:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
        }

        .divider {
            height: 1px;
            background: #e9ecef;
            margin: 30px 0;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 20px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4caf50;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #ef5350;
        }

        .info-text {
            font-size: 12px;
            color: #6b7280;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo">dev<span>hub</span></div>
    <a href="logout.php" class="logout-btn">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</nav>

<div class="home-container">
    <!-- Welcome Card -->
    <div class="welcome-card">
        <div class="avatar">
            <i class="fas fa-user-astronaut"></i>
        </div>
        <h1>Welcome back, <?php echo htmlspecialchars($user_data['fullname']); ?>! 🎉</h1>
        <div class="email">
            <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user_data['email']); ?>
        </div>
        <div class="member-since">
            <i class="fas fa-calendar"></i> Joined <?php echo date('F Y', strtotime($user_data['created_at'])); ?>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if ($message != ""): ?>
        <div class="alert alert-<?php echo $message_type; ?>" style="margin-top: 20px;">
            <i class="fas <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <span><?php echo $message; ?></span>
        </div>
    <?php endif; ?>

    <!-- Profile Settings -->
    <div class="profile-section">
        <div class="section-title">
            <i class="fas fa-user-edit"></i>
            <span>Profile Settings</span>
        </div>

        <!-- Update Name Form -->
        <form method="POST" class="profile-form">
            <div class="form-row">
                <label><i class="fas fa-user"></i> Full Name</label>
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($user_data['fullname']); ?>" required>
            </div>
            <button type="submit" name="update_name" class="btn-update">
                <i class="fas fa-save"></i> Update Name
            </button>
        </form>

        <div class="divider"></div>

        <!-- Update Password Form -->
        <form method="POST" class="profile-form">
            <div class="section-title" style="font-size: 20px; margin-bottom: 20px;">
                <i class="fas fa-lock"></i>
                <span>Change Password</span>
            </div>

            <div class="form-row">
                <label><i class="fas fa-key"></i> Current Password</label>
                <input type="password" name="current_password" id="current_password" placeholder="Enter current password" required>
            </div>

            <div class="form-row">
                <label><i class="fas fa-lock"></i> New Password</label>
                <input type="password" name="new_password" id="new_password" placeholder="Enter new password (min 4 chars)" required>
                <div class="info-text">Password must be at least 4 characters</div>
            </div>

            <div class="form-row">
                <label><i class="fas fa-check-circle"></i> Confirm New Password</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm new password" required>
            </div>

            <button type="submit" name="update_password" class="btn-update">
                <i class="fas fa-key"></i> Change Password
            </button>
        </form>
    </div>
</div>

<script>
// Optional: Add password match validation on client side
document.querySelector('form[method="POST"]:last-child').addEventListener('submit', function(e) {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('confirm_password').value;
    
    if (newPass !== confirmPass) {
        e.preventDefault();
        alert('New passwords do not match!');
    } else if (newPass.length > 0 && newPass.length < 4) {
        e.preventDefault();
        alert('Password must be at least 4 characters!');
    }
});
</script>

</body>
</html>