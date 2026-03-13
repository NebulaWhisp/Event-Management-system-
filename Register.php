<?php
session_start();
require 'db_connect.php';

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {

    $name  = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);

    $sql = "INSERT INTO Users (FullName, Email, Password, Role) VALUES (?, ?, ?, 'Client')";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("sss", $name, $email, $pass);

    try {
        $stmt->execute();

        $_SESSION['UserID'] = $conn->insert_id;

        $_SESSION['FullName'] = $name;
        $_SESSION['Role'] = "Client";

        $message = "Registration successful! You can now book events.";
        header("Location: login.php");
        exit();

    } catch (Exception $e) {
        $message = "⚠ Email already exists!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Our Services | Event Management</title>

<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        background: linear-gradient(135deg, #00b4db, #0083b0);
        color: #fff;
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

    .container {
        width: 90%;
        max-width: 900px;
        margin: 40px auto;
        text-align: center;
    }

    .header h1 {
        font-size: 40px;
        margin-bottom: 5px;
    }

    .reg-box {
        background: #fff;
        color: #333;
        padding: 20px;
        border-radius: 12px;
        width: 60%;
        margin: 20px auto;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .reg-box input {
        width: 90%;
        padding: 12px;
        margin: 8px 0;
        border-radius: 6px;
        border: none;
    }

    .reg-btn {
        background: #0083b0;
        color: white;
        padding: 12px 20px;
        border: none;
        width: 50%;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
    }

    .reg-btn:hover {
        background: #00b4db;
    }

    .service-section {
        padding: 40px 20px;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 25px;
    }

    .service-card {
        background: #fff;
        color: #333;
        width: 300px;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        transition: 0.3s;
    }

    .service-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.4);
    }

    .service-card h3 {
        color: #0083b0;
        font-size: 24px;
    }

    .book-btn {
        background: #fff;
        color: #0083b0;
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: bold;
        text-decoration: none;
        margin-top: 20px;
        display: inline-block;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        transition: 0.3s;
    }

    .book-btn:hover {
        transform: scale(1.05);
        background: #f0f0f0;
    }

</style>
</head>

<body>

<a href="client_dashboard.html" class="back-button">⬅ Back</a>

<div class="container">

    <div class="header">
        <h1>Our Event Services</h1>
        <p>Register below to book events with us.</p>
    </div>


    <?php if ($message != ""): ?>
        <p style="background:#fff;padding:10px;color:#333;border-radius:8px;">
            <?= $message ?>
        </p>
    <?php endif; ?>


    <div class="reg-box">
        <h2 style="color:#0083b0;">Client Registration</h2>

        <form method="POST">
    <input type="text" name="fullname" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="password" name="password" placeholder="Create Password" required>

    <button type="submit" name="register" class="reg-btn">Register</button>

    <p style="margin-top:15px;">
        Already have an account? 
        <a href="login.php" style="color:#0083b0; font-weight:bold;">Login</a>
    </p>
</form>
    </div>

</div>

</body>
</html>
