<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['UserID']) || strcasecmp($_SESSION['Role'], 'manager') !== 0) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_service'])) {
    $serviceName = trim($_POST['service_name']);
    $description = trim($_POST['description']);
    $price       = floatval($_POST['price']);

    $sql = "INSERT INTO services (ServiceName, Description, Price) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssd", $serviceName, $description, $price);

    if ($stmt->execute()) {
        $message = "✅ Service added successfully!";
    } else {
        $message = "❌ Error adding service: " . $stmt->error;
    }
    
}

if (isset($_GET['delete'])) {
    $serviceID = intval($_GET['delete']);

    $sql = "DELETE FROM services WHERE ServiceID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $serviceID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message = "✅ Service deleted successfully!";
    } else {
        $message = "⚠ Unable to delete service!";
    }
header("Location: Services.php");
    exit();
}
   
 

$sql = "SELECT * FROM services ORDER BY ServiceID DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Services | Event Management</title>
<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f0f4f7;
    margin: 0;
    padding: 0;
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
    max-width: 900px;
    margin: 40px auto;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
}

h1 { color: #0083b0; text-align: center; }

form input, form textarea {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 6px;
    border: 1px solid #ccc;
    box-sizing: border-box;
}

form input[type="submit"] {
    background: #0083b0;
    color: white;
    border: none;
    padding: 12px 20px;
    cursor: pointer;
    font-weight: bold;
    border-radius: 6px;
}
form input[type="submit"]:hover {
    background: #00b4db;
}

.message {
    text-align: center;
    font-weight: bold;
    margin: 15px 0;
    color: green;
}
.message.error { color: red; }

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
table th, table td {
    padding: 12px;
    border-bottom: 1px solid #ddd;
    text-align: left;
}
table th {
    background: #0083b0;
    color: white;
}
.delete-btn {
    background: red;
    color: white;
    padding: 6px 10px;
    border-radius: 5px;
    text-decoration: none;
}
.delete-btn:hover { background: darkred; }

.back-button {
    display: inline-block;
    margin-bottom: 15px;
    background: #0083b0;
    color: #fff;
    padding: 8px 15px;
    border-radius: 6px;
    text-decoration: none;
}
.back-button:hover { background: #00b4db; }
</style>
</head>
<body>

<div class="container">
<a href="manager_dashboard.php" class="back-button">⬅ Back to Dashboard</a>
<h1>Manage Services</h1>

<?php if ($message): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="POST">
    <h2>Add New Service</h2>
    <input type="text" name="service_name" placeholder="Service Name" required>
    <textarea name="description" placeholder="Description" rows="3" required></textarea>
    <input type="number" step="0.01" name="price" placeholder="Price" required>
    <input type="submit" name="add_service" value="Add Service">
</form>

<h2>All Services</h2>
<table>
    <tr>
        <th>Service Name</th>
        <th>Description</th>
        <th>Price</th>
        <th>Action</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['ServiceName']) ?></td>
            <td><?= htmlspecialchars($row['Description']) ?></td>
            <td>$<?= number_format($row['Price'], 2) ?></td>
            <td>
                <a class="delete-btn" 
                   href="Services.php?delete=<?= $row['ServiceID'] ?>"
                   onclick="return confirm('Are you sure you want to delete this service?');">
                   ❌ Delete
                </a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>

</div>
</body>
</html>
