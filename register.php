<?php
// register.php - Clean & Attractive Registration
session_start();
include "db.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["register"])) {

    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (empty($fullname) || empty($email) || empty($password)) {
        $message = "All fields are required!";
        $message_type = "error";
    }
    elseif ($password !== $confirm_password) {
        $message = "Passwords do not match!";
        $message_type = "error";
    }
    elseif (strlen($password) < 4) {
        $message = "Password must be at least 4 characters!";
        $message_type = "error";
    }
    else {
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $message = "Email already exists! Please login.";
            $message_type = "error";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $conn->prepare("INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
            $insert_stmt->bind_param("sss", $fullname, $email, $hashed_password);

            if ($insert_stmt->execute()) {
                $message = "Registration successful! Redirecting to login...";
                $message_type = "success";
                echo '<script>setTimeout(function(){ window.location.href = "login.php"; }, 1500);</script>';
            } else {
                $message = "Registration failed! Please try again.";
                $message_type = "error";
            }
            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="card">
    <div class="header">
        <div class="icon-circle">
            <i class="fas fa-user-plus"></i>
        </div>
        <h2>Create Account</h2>
        <p class="subtitle">Join our community today</p>
    </div>

    <?php if ($message != ""): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <i class="fas <?php echo $message_type == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
            <span><?php echo $message; ?></span>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" name="fullname" placeholder="Full Name" required value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
        </div>

        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="Email Address" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password (min 4 chars)" required>
        </div>

        <div class="input-group">
            <i class="fas fa-check-circle"></i>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required>
        </div>

        <button type="submit" name="register" class="btn-register">
            <i class="fas fa-arrow-right"></i>
            Register Now
        </button>
    </form>

    <div class="login-link">
        Already have an account? <a href="login.php">Sign In</a>
    </div>
</div>

</body>
</html>