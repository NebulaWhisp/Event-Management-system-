<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Client Dashboard | Event Management</title>
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #00b4db, #0083b0);
    color: #333;
    margin: 0;
    padding: 0;
}
.navbar {
    background: #fff;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}
.navbar h2 {
    margin: 0;
    color: #0083b0;
}
.navbar a {
    text-decoration: none;
    background: #0083b0;
    color: white;
    padding: 8px 15px;
    border-radius: 5px;
    margin-left: 10px;
}
.navbar a:hover {
    background: #00b4db;
}
.dashboard {
    text-align: center;
    margin-top: 80px;
}
.dashboard a.btn {
    display: inline-block;
    padding: 15px 25px;
    margin: 15px;
    border-radius: 10px;
    background: #fff;
    color: #0083b0;
    font-weight: bold;
    text-decoration: none;
    box-shadow: 0px 0px 10px rgba(0,0,0,0.2);
}
.dashboard a.btn:hover {
    background: #00b4db;
    color: white;

}
</style>
</head>
<body>

<div class="navbar">
    <h2>Welcome, <?=$_SESSION['FullName']?> 👋</h2>
    <div>
        <a href="client_booking.php">➕ New Booking</a>
        <a href="logout.php">🚪 Logout</a>
    </div>
</div>

<div class="dashboard">
    <h1>Client Dashboard</h1>
    <p>Welcome to your event management portal. You can create and view your event bookings here.</p>
    <a class="btn" href="client_booking.php">Create New Event Booking</a>
    <a class="btn" href="ViewBookings.php">View My Bookings</a>
    <a class="btn" href="viewservices.php">View Services & Prices</a>
</div>

</body>
</html>