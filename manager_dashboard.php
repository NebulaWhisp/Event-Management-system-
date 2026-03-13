<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['UserID']) || $_SESSION['Role'] != 'manager') {
    header("Location: login.php");
    exit();
}
echo "<h2>Welcome, Manager " . $_SESSION['FullName'] . "</h2>";


if (isset($_GET['delete_booking'])) {
    $bookingID = intval($_GET['delete_booking']);
    $stmtDelete = $conn->prepare("DELETE FROM bookings WHERE BookingID = ?");
    $stmtDelete->bind_param("i", $bookingID);
    $stmtDelete->execute();
    $message = "✅ Booking ID $bookingID removed successfully!";
}

$sql = "SELECT b.BookingID, b.Status, b.BookingDate, 
               e.EventName, e.EventDate, e.Location, 
               u.FullName AS ClientName, u.Email AS ClientEmail
        FROM bookings b
        JOIN events e ON b.EventID = e.EventID
        JOIN users u ON b.ClientID = u.UserID
        ORDER BY b.BookingDate DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manager Dashboard</title>
<style>
body { font-family: 'Segoe UI', sans-serif; background:#f0f4f7; margin:0; padding:0;}
.navbar { background:#fff; padding:15px 25px; display:flex; justify-content:space-between; align-items:center; box-shadow:0 2px 10px rgba(0,0,0,0.1);}
.navbar h2 { margin:0; color:#0083b0; }
.navbar a { text-decoration:none; background:#0083b0; color:white; padding:8px 15px; border-radius:5px; margin-left:10px;}
.navbar a:hover { background:#00b4db; }
.container { max-width:1000px; margin:30px auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 0 15px rgba(0,0,0,0.2);}
h1 { text-align:center; color:#0083b0;}
table { width:100%; border-collapse:collapse; margin-top:20px; }
table, th, td { border:1px solid #ccc; }
th, td { padding:10px; text-align:left; }
th { background:#0083b0; color:white; }
tr:nth-child(even) { background:#f9f9f9; }
a.delete-btn { color:white; background:red; padding:5px 10px; border-radius:5px; text-decoration:none; }
a.delete-btn:hover { background:#cc0000; }
.message { text-align:center; margin:15px 0; font-weight:bold; color:green;}
</style>
</head>
<body>

<div class="navbar">
    <h2>Welcome, <?=$_SESSION['FullName']?> 👋</h2>
    <div>
        <a href="logout.php">🚪 Logout</a>
    </div>
</div>

<div class="container">
<h1>All Bookings</h1>
<?php if (!empty($message)) echo "<p class='message'>{$message}</p>"; ?>

<table>
    <tr>
        <th>Booking ID</th>
        <th>Client</th>
        <th>Email</th>
        <th>Event Name</th>
        <th>Event Date</th>
        <th>Location</th>
        <th>Status</th>
        <th>Booking Date</th>
        <th>Action</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['BookingID'] ?></td>
                <td><?= htmlspecialchars($row['ClientName']) ?></td>
                <td><?= htmlspecialchars($row['ClientEmail']) ?></td>
                <td><?= htmlspecialchars($row['EventName']) ?></td>
                <td><?= $row['EventDate'] ?></td>
                <td><?= htmlspecialchars($row['Location']) ?></td>
                <td><?= $row['Status'] ?></td>
                <td><?= $row['BookingDate'] ?></td>
                <td>
                    <a class="delete-btn" href="manager_dashboard.php?delete_booking=<?= $row['BookingID'] ?>" onclick="return confirm('Are you sure you want to delete this booking?');">Remove</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="9" style="text-align:center;">No bookings found.</td></tr>
    <?php endif; ?>
</table>

<a href="Services.php" style="display:inline-block; margin-bottom:15px; background:#0083b0; color:#fff; padding:8px 15px; border-radius:6px; text-decoration:none;">🛠 Go to Services</a>

</div>
</body>
</html>
