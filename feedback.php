<?php
// feedback.php

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mess_maintenance';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = trim($_POST['name']);
  $type = $_POST['type'];
  $messageText = trim($_POST['message']);

  if ($name && $type && $messageText) {
    $stmt = $conn->prepare("INSERT INTO feedback (name, type, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $type, $messageText);
    if ($stmt->execute()) {
      $message = "Thank you for your feedback!";
    } else {
      $message = "Failed to submit feedback.";
    }
    $stmt->close();
  } else {
    $message = "All fields are required.";
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
 
  <title>Feedback</title>
  <link rel="stylesheet" href="cssstyle.css" />
  <style>
    body {
      padding: 2rem;
      font-family: Arial, sans-serif;
      background: #f4f4f4;
    }
    .feedback-container {
      max-width: 500px;
      margin: 0 auto;
      background: white;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      margin-bottom: 1rem;
    }
    label {
      display: block;
      margin-top: 1rem;
      font-weight: bold;
    }
    input, select, textarea {
      width: 100%;
      padding: 0.7rem;
      margin-top: 0.3rem;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      margin-top: 1.5rem;
      width: 100%;
      padding: 0.75rem;
      background-color: #007BFF;
      border: none;
      color: white;
      font-size: 1rem;
      font-weight: bold;
      border-radius: 5px;
      cursor: pointer;
    }
    .message {
      margin-top: 1rem;
      text-align: center;
      color: green;
    }
  </style>
</head>
<body>
  <div class="feedback-container">
    <h2>Submit Feedback</h2>

    <?php if ($message): ?>
      <p class="message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST" action="feedback.php">
      <label for="name">Your Name:</label>
      <input type="text" name="name" id="name" required />

      <label for="type">Type:</label>
      <select name="type" id="type" required>
        <option value="Complaint">Complaint</option>
        <option value="Suggestion">Suggestion</option>
        <option value="Other">Other</option>
      </select>

      <label for="message">Message:</label>
      <textarea name="message" id="message" rows="5" required></textarea>

      <button type="submit">Submit Feedback</button>
    </form>
  </div>
</body>
</html>
