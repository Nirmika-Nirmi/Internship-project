<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register Page</title>

  <!-- Google Font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>

  <style>

    *{
      margin:0;
      padding:0;
      box-sizing:border-box;
      font-family:'Poppins',sans-serif;
    }

    body{
      height:100vh;
      display:flex;
      justify-content:center;
      align-items:center;
      background:linear-gradient(135deg,#4f46e5,#9333ea,#ec4899);
      overflow:hidden;
    }

    /* Background circles */

    .circle{
      position:absolute;
      border-radius:50%;
      background:rgba(255,255,255,0.1);
      backdrop-filter: blur(5px);
    }

    .circle1{
      width:250px;
      height:250px;
      top:-80px;
      left:-50px;
    }

    .circle2{
      width:300px;
      height:300px;
      bottom:-100px;
      right:-80px;
    }

    /* Register Card */

    .container{
      width:400px;
      background:rgba(255,255,255,0.15);
      border:1px solid rgba(255,255,255,0.2);
      backdrop-filter:blur(12px);
      border-radius:20px;
      padding:40px;
      box-shadow:0 8px 32px rgba(0,0,0,0.2);
      color:#fff;
      position:relative;
      z-index:10;
    }

    .container h1{
      text-align:center;
      margin-bottom:10px;
      font-size:32px;
    }

    .container p{
      text-align:center;
      margin-bottom:30px;
      font-size:14px;
      color:#f1f1f1;
    }

    .input-box{
      position:relative;
      margin-bottom:22px;
    }

    .input-box input{
      width:100%;
      padding:14px 45px 14px 15px;
      border:none;
      outline:none;
      border-radius:12px;
      background:rgba(255,255,255,0.15);
      color:#fff;
      font-size:15px;
      transition:0.3s;
    }

    .input-box input::placeholder{
      color:#eee;
    }

    .input-box input:focus{
      background:rgba(255,255,255,0.25);
      transform:scale(1.02);
    }

    .input-box i{
      position:absolute;
      right:15px;
      top:50%;
      transform:translateY(-50%);
      color:#fff;
      font-size:16px;
    }

    .register-btn{
      width:100%;
      padding:14px;
      border:none;
      border-radius:12px;
      background:#fff;
      color:#6d28d9;
      font-size:16px;
      font-weight:600;
      cursor:pointer;
      transition:0.3s;
    }

    .register-btn:hover{
      background:#f3f4f6;
      transform:translateY(-2px);
    }

    .login-link{
      text-align:center;
      margin-top:20px;
      font-size:14px;
    }

    .login-link a{
      color:#fff;
      text-decoration:none;
      font-weight:600;
    }

    .login-link a:hover{
      text-decoration:underline;
    }

    /* Responsive */

    @media(max-width:450px){

      .container{
        width:90%;
        padding:30px 20px;
      }

      .container h1{
        font-size:28px;
      }

    }

  </style>
</head>
<body>

  <!-- Background Design -->
  <div class="circle circle1"></div>
  <div class="circle circle2"></div>

  <!-- Register Form -->

  <div class="container">

    <h1>Create Account</h1>
    <p>Register to continue</p>

    <form>

      <div class="input-box">
        <input type="text" placeholder="Full Name" required>
        <i class="fa-solid fa-user"></i>
      </div>

      <div class="input-box">
        <input type="email" placeholder="Email Address" required>
        <i class="fa-solid fa-envelope"></i>
      </div>

      <div class="input-box">
        <input type="password" placeholder="Password" required>
        <i class="fa-solid fa-lock"></i>
      </div>

      <div class="input-box">
        <input type="password" placeholder="Confirm Password" required>
        <i class="fa-solid fa-lock"></i>
      </div>

      <button type="submit" class="register-btn">
        Register
      </button>

      <div class="login-link">
        Already have an account?
        <a href="#">Login</a>
      </div>

    </form>

  </div>

</body>
</html>