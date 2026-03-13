<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['UserID']) ||
   (strcasecmp($_SESSION['Role'], 'Client') !== 0 && strcasecmp($_SESSION['Role'], 'employees') !== 0)) {
    // preserve the requested page so we can redirect back after login
    $requested = $_SERVER['REQUEST_URI'];
    header("Location: login.php?redirect=" . urlencode($requested));
    exit();
}

$clientID = $_SESSION['UserID'];
$message = "";

// If a service was selected from viewservices.php, capture it to prefill/display
$selectedServiceID = isset($_GET['serviceID']) ? intval($_GET['serviceID']) : 0;
$selectedServiceName = "";
if ($selectedServiceID > 0) {
    $stmtSvc = $conn->prepare("SELECT ServiceName FROM services WHERE ServiceID = ?");
    $stmtSvc->bind_param("i", $selectedServiceID);
    $stmtSvc->execute();
    $resSvc = $stmtSvc->get_result();
    if ($rowSvc = $resSvc->fetch_assoc()) {
        $selectedServiceName = $rowSvc['ServiceName'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName    = trim($_POST['full_name']);
    $email       = trim($_POST['email']);
    $phone       = trim($_POST['phone']);

    $eventName   = trim($_POST['event_name']);
    $eventDate   = trim($_POST['event_date']);
    $location    = trim($_POST['location']);
    $description = trim($_POST['description']);

    $status      = "Pending";
    $bookingDate = date("Y-m-d H:i:s");

    $sqlEvent = "INSERT INTO events (EventName, Description, EventDate, Location, CreatedBy)
                 VALUES (?, ?, ?, ?, ?)";
    $stmtEvent = $conn->prepare($sqlEvent);
    $stmtEvent->bind_param("ssssi", $eventName, $description, $eventDate, $location, $clientID);

    if ($stmtEvent->execute()) {
        $eventID = $stmtEvent->insert_id;

        // If a service was selected, save the mapping into eventservices
        $postedServiceID = intval($_POST['service_id'] ?? 0);
        if ($postedServiceID > 0) {
            $stmtES = $conn->prepare("INSERT INTO eventservices (EventID, ServiceID) VALUES (?, ?)");
            $stmtES->bind_param("ii", $eventID, $postedServiceID);
            $stmtES->execute();
            // ignore errors silently for now; manager can fix later
        }

        $sqlBooking = "INSERT INTO bookings (EventID, ClientID, ManagerID, Status, BookingDate)
                       VALUES (?, ?, NULL, ?, ?)";
        $stmtBooking = $conn->prepare($sqlBooking);
        $stmtBooking->bind_param("iiss", $eventID, $clientID, $status, $bookingDate);

        if ($stmtBooking->execute()) {
            $message = "✅ Booking request submitted successfully!";
        } else {
            $message = "❌ Booking failed: " . $stmtBooking->error;
        }
    } else {
        $message = "❌ Event creation failed: " . $stmtEvent->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Event Booking</title>
<style>
body { font-family: 'Segoe UI', sans-serif; background: #f0f4f7; margin:0; padding:0;}
.container { max-width:600px; margin:50px auto; background:#fff; padding:30px; border-radius:10px; box-shadow:0 0 15px rgba(0,0,0,0.2);}
h1 { text-align:center; color:#0083b0;}
form input, form textarea { width:100%; padding:10px; margin:10px 0; border-radius:5px; border:1px solid #ccc; box-sizing:border-box;}
form input[type="submit"] { background:#0083b0; color:white; font-weight:bold; border:none; cursor:pointer; }
form input[type="submit"]:hover { background:#00b4db; }
.message { text-align:center; margin:15px 0; font-weight:bold; color:green;}
.message.error { color:red; }

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

<a href="client_dashboard.php" class="back-button">⬅ Back</a>
<div class="container">
<h1>Create New Booking</h1>
<?php if ($message) echo "<p class='message'>{$message}</p>"; ?>

<form method="POST">
    <h2>Client Details</h2>
    <input type="text" name="full_name" placeholder="Full Name" value="<?= htmlspecialchars($_SESSION['FullName']) ?>" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="text" name="phone" placeholder="Phone Number" required>

    <h2>Event Details</h2>
    <?php if (!empty($selectedServiceName)): ?>
        <p><strong>Selected Service:</strong> <?= htmlspecialchars($selectedServiceName) ?></p>
        <input type="hidden" name="service_id" value="<?= intval($selectedServiceID) ?>">
        <input type="text" name="event_name" placeholder="Event Name" value="<?= htmlspecialchars('Booking: ' . $selectedServiceName) ?>" required>
    <?php else: ?>
        <input type="text" name="event_name" placeholder="Event Name" required>
    <?php endif; ?>
    <input type="date" name="event_date" required>
    <input type="text" name="location" placeholder="Location" required>
    <textarea name="description" placeholder="Event Description" rows="4" required></textarea>

    <input type="submit" value="Submit Booking">
</form>
</div>
</body>
</html>
