<?php
// billing.php

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mess_maintenance';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$meal_prices = [
  'Breakfast' => 30,
  'Lunch' => 50,
  'Dinner' => 60
];

$month = $_GET['month'] ?? date('Y-m'); // default current month

// Get first and last date of month
$start_date = $month . '-01';
$end_date = date('Y-m-t', strtotime($start_date));

$message = '';
$bills = [];

// Fetch all users
$users_result = $conn->query("SELECT id, name FROM users ORDER BY name");

if ($users_result->num_rows > 0) {
  while ($user = $users_result->fetch_assoc()) {
    $user_id = $user['id'];
    $user_name = $user['name'];

    // For each meal type, count how many times user was present
    $total = 0;
    foreach ($meal_prices as $meal_type => $price) {
      $stmt = $conn->prepare(
        "SELECT COUNT(*) as meal_count 
         FROM attendance a
         JOIN meals m ON a.attendance_date = m.meal_date AND m.meal_type = ?
         WHERE a.user_id = ? AND a.status = 'Present' AND a.attendance_date BETWEEN ? AND ?"
      );
      $stmt->bind_param('siss', $meal_type, $user_id, $start_date, $end_date);
      $stmt->execute();
      $stmt->bind_result($meal_count);
      $stmt->fetch();
      $stmt->close();

      $total += $meal_count * $price;
    }

    $bills[] = [
      'name' => $user_name,
      'total' => $total
    ];
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Billing - Mess Maintenance</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    body {
      padding: 1rem;
      background: #f9f9f9;
      font-family: Arial, sans-serif;
    }
    h1 {
      text-align: center;
      margin-bottom: 1rem;
    }
    form {
      max-width: 300px;
      margin: 0 auto 2rem;
      text-align: center;
    }
    input[type="month"] {
      padding: 0.5rem;
      font-size: 1rem;
      margin-right: 1rem;
      border-radius: 6px;
      border: 1px solid #ccc;
    }
    button {
      padding: 0.5rem 1rem;
      font-size: 1rem;
      background: #007BFF;
      border: none;
      color: white;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s;
    }
    button:hover {
      background: #0056b3;
    }
    table {
      width: 90%;
      max-width: 600px;
      margin: 0 auto;
      border-collapse: collapse;
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      overflow: hidden;
    }
    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    th {
      background: #007BFF;
      color: white;
    }
    tr:last-child td {
      border-bottom: none;
    }
    .total {
      font-weight: bold;
    }
  </style>
</head>
<body>
  <h1>Mess Billing</h1>

  <form method="GET" action="billing.php">
    <label for="month">Select Month: </label>
    <input type="month" id="month" name="month" value="<?php echo htmlspecialchars($month); ?>" required />
    <button type="submit">Show Bill</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>User</th>
        <th>Total Bill (₹)</th>
      </tr>
    </thead>
    <tbody>
      <?php if (count($bills) > 0): ?>
        <?php foreach ($bills as $bill): ?>
          <tr>
            <td><?php echo htmlspecialchars($bill['name']); ?></td>
            <td class="total"><?php echo number_format($bill['total'], 2); ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="2" style="text-align:center;">No billing data available.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
