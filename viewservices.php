<?php
session_start();
require 'db_connect.php';

$userLoggedIn = false;
if (isset($_SESSION['UserID'])) {
    $userLoggedIn = true;
}

$sql = "SELECT ServiceID, ServiceName, Description, Price FROM services";
$result = $conn->query($sql);
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
    background: #f0f4f7;
    color: #333;
}
.navbar {
    background: #0083b0;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.navbar h2 { color: #fff; margin: 0; }
.navbar a {
    text-decoration: none;
    background: #fff;
    color: #0083b0;
    padding: 8px 15px;
    border-radius: 5px;
    margin-left: 10px;
    font-weight: bold;
}
.navbar a:hover { background: #e0e0e0; }

.container {
    width: 90%;
    max-width: 1000px;
    margin: 40px auto;
    text-align: center;
}

h1 { color: #0083b0; margin-bottom: 30px; }

.service-section {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 20px;
}

.service-card {
    background: #fff;
    width: 280px;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    text-align: left;
    transition: 0.3s;
}
.service-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0,0,0,0.3);
}

.service-card h3 {
    color: #0083b0;
    margin-bottom: 10px;
}
.service-card p {
    margin-bottom: 10px;
}
.service-card span {
    font-weight: bold;
    color: #0083b0;
}

.book-btn {
    display: inline-block;
    text-decoration: none;
    color: #fff;
    background: #0083b0;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: bold;
    transition: 0.3s;
}
.book-btn:hover {
    background: #00b4db;
}
</style>
</head>
<body>

<div class="navbar">
    <h2>Event Management</h2>
    <div>
        <?php if ($userLoggedIn): ?>
            <a href="client_dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="Register.php">Register</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <h1>Our Services</h1>
    <div class="service-section">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="service-card">
                    <h3><?= htmlspecialchars($row['ServiceName']) ?></h3>
                    <p><?= htmlspecialchars($row['Description']) ?></p>
                    <p>Price: <span>$<?= number_format($row['Price'], 2) ?></span></p>
                    <?php if ($userLoggedIn): ?>
                        <a class="book-btn"
                           href="client_booking.php?serviceID=<?= $row['ServiceID'] ?>"
                           data-service="<?= htmlspecialchars($row['ServiceName']) ?>"
                           target="_blank" rel="noopener noreferrer"
                        >Book Now</a>
                    <?php else: ?>
                        <?php $redir = urlencode('client_booking.php?serviceID=' . $row['ServiceID']); ?>
                        <a class="book-btn" href="login.php?redirect=<?= $redir ?>" target="_blank" rel="noopener noreferrer">Login to Book</a>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No services available at the moment.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
