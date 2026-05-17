<?php
// login.php - Clean & Attractive Login
session_start();

if (isset($_SESSION["user"])) {
    header("Location: home.php");
    exit();
}

include "db.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $message = "Please enter email and password!";
        $message_type = "error";
    } else {
        $stmt = $conn->prepare("SELECT id, fullname, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user["password"])) {
                $_SESSION["user"] = $user["fullname"];
                $_SESSION["user_id"] = $user["id"];
                header("Location: home.php");
                exit();
            } else {
                $message = "Invalid password!";
                $message_type = "error";
            }
        } else {
            $message = "Email not found! Please register first.";
            $message_type = "error";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Back</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="card">
    <div class="header">
        <div class="icon-circle">
            <i class="fas fa-rocket"></i>
        </div>
        <h2>Welcome Back</h2>
        <p class="subtitle">Sign in to continue</p>
    </div>

    <?php if ($message != ""): ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo $message; ?></span>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" name="email" placeholder="Email Address" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button type="submit" name="login" class="btn-login">
            <i class="fas fa-sign-in-alt"></i>
            Sign In
        </button>
    </form>

    <div class="register-link">
        Don't have an account? <a href="register.php">Create Account</a>
    </div>
</div>

</body>
</html>