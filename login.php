<?php
session_start();
require 'db_connect.php';

$redirect = trim($_REQUEST['redirect'] ?? '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $redirect = trim($_POST['redirect'] ?? $redirect);

    $sql = "SELECT * FROM users WHERE Email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && $password === $user['Password']) {
        $_SESSION['UserID']   = $user['UserID'];
        $_SESSION['FullName'] = $user['FullName'];
        $_SESSION['Role']     = $user['Role'];

        // If there's a redirect and it's a local path (no protocol), go there
        if (!empty($redirect) && strpos($redirect, '://') === false) {
            header("Location: " . $redirect);
            exit();
        }

        if ($user['Role'] === 'manager') {
            header("Location: manager_dashboard.php");
            exit();
        } else {
            header("Location: client_dashboard.php");
            exit();
        }
    } else {
        $error = "Invalid email or password!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login | Event Management System</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #00b4db, #0083b0);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-box {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0,0,0,0.2);
            width: 350px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #0083b0;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background: #0083b0;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        input[type="submit"]:hover {
            background: #00b4db;
        }
        .error {
            color: red;
            text-align: center;
        }

        .back-button {
    position: fixed;
    right: 20px;     
    top: 10%;     
    transform: translateY(-50%);
    background-color: #0083b0;
    color: white;
    padding: 12px 18px;
    text-decoration: none;
    font-weight: bold;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    transition: background 0.3s, transform 0.2s;
    z-index: 1000;   
}

.back-button:hover {
    background-color: #00b4db;
    transform: translateY(-50%) scale(1.05);
}
    </style>
</head>
<body>
    <a href="index.html" class="back-button">⬅ Back</a>
    <div class="login-box">
        <h2>Event Management Login</h2>
        <form method="POST">
            <input type="text" name="email" placeholder="Enter Email" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect) ?>">
            <input type="submit" value="Login" >
        </form>
        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    </div>
</body>
</html>
