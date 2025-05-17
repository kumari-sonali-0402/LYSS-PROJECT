<?php
// attendance.php

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mess_maintenance';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$message = '';
$today = date('Y-m-d');

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $attendance = $_POST['attendance'] ?? [];

  foreach ($attendance as $user_id => $status) {
    // Check if attendance already marked today
    $stmt = $conn->prepare("SELECT id FROM attendance WHERE user_id=? AND attendance_date=?");
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
      // Update existing record
      $stmt->close();
      $update = $conn->prepare("UPDATE attendance SET status=? WHERE user_id=? AND attendance_date=?");
      $update->bind_param("sis", $status, $user_id, $today);
      $update->execute();
      $update->close();
    } else {
      // Insert new record
      $stmt->close();
      $insert = $conn->prepare("INSERT INTO attendance (user_id, attendance_date, status) VALUES (?, ?, ?)");
      $insert->bind_param("iss", $user_id, $today, $status);
      $insert->execute();
      $insert->close();
    }
  }

  $message = "Attendance recorded for $today.";
}

// Fetch users with existing attendance status for today
$sql = "SELECT u.id, u.name, COALESCE(a.status, 'Absent') as status
        FROM users u
        LEFT JOIN attendance a ON u.id = a.user_id AND a.attendance_date = '$today'
        ORDER BY u.name ASC";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Attendance - Mess Maintenance</title>
  <link rel="stylesheet" href="cssstyle.css" />
  <style>
    table {
      margin: 2rem auto;
      border-collapse: collapse;
      width: 90%;
      max-width: 700px;
      background: #fff;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    th, td {
      padding: 1rem;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    th {
      background-color: #007BFF;
      color: white;
    }
    tr:last-child td {
      border-bottom: none;
    }
    .center {
      text-align: center;
    }
    button {
      background-color: #007BFF;
      color: white;
      border: none;
      padding: 0.7rem 1.2rem;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      margin: 1rem auto;
      display: block;
      font-size: 1rem;
      transition: background-color 0.3s;
    }
    button:hover {
      background-color: #0056b3;
    }
    .message {
      max-width: 700px;
      margin: 1rem auto;
      text-align: center;
      font-weight: 600;
      color: green;
    }
  </style>
</head>
<body>
  <header>
    <h1>Attendance Check-in - <?php echo $today; ?></h1>
  </header>

  <?php if ($message): ?>
    <div class="message"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>

  <form method="POST" action="attendance.php">
    <table>
      <thead>
        <tr>
          <th>Student Name</th>
          <th class="center">Present</th>
          <th class="center">Absent</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($row['name']); ?></td>
              <td class="center">
                <input type="radio" name="attendance[<?php echo $row['id']; ?>]" value="Present" 
                <?php echo $row['status'] === 'Present' ? 'checked' : ''; ?> required>
              </td>
              <td class="center">
                <input type="radio" name="attendance[<?php echo $row['id']; ?>]" value="Absent" 
                <?php echo $row['status'] === 'Absent' ? 'checked' : ''; ?> required>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="3">No users found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>

    <button type="submit">Submit Attendance</button>
  </form>
</body>
</html>
