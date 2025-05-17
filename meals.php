<?php
// meals.php

// DB connection settings
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'mess_maintenance';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $meal_date = $_POST['meal_date'] ?? '';
  $meal_type = $_POST['meal_type'] ?? '';
  $menu_items = $_POST['menu_items'] ?? '';

  // Basic validation
  if ($meal_date && $meal_type && $menu_items) {
    $stmt = $conn->prepare("INSERT INTO meals (meal_date, meal_type, menu_items) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $meal_date, $meal_type, $menu_items);

    if ($stmt->execute()) {
      $message = "Meal added successfully!";
    } else {
      $message = "Error: " . $stmt->error;
    }
    $stmt->close();
  } else {
    $message = "Please fill all fields.";
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Add Meal - Mess Maintenance</title>
  <link rel="stylesheet" href="cssstyle.css" />
  <style>
    form {
      max-width: 500px;
      margin: 2rem auto;
      background: #fff;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    label {
      display: block;
      margin-bottom: 0.4rem;
      font-weight: 600;
    }
    input[type="date"],
    select,
    textarea {
      width: 100%;
      padding: 0.5rem;
      margin-bottom: 1rem;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 1rem;
      font-family: inherit;
    }
    textarea {
      resize: vertical;
      height: 80px;
    }
    button {
      background-color: #007BFF;
      color: white;
      border: none;
      padding: 0.8rem 1.5rem;
      border-radius: 8px;
      cursor: pointer;
      font-size: 1.1rem;
      font-weight: 600;
      transition: background-color 0.3s;
    }
    button:hover {
      background-color: #0056b3;
    }
    .message {
      max-width: 500px;
      margin: 1rem auto;
      font-weight: 600;
      color: green;
      text-align: center;
    }
  </style>
</head>
<body>
  <header>
    <h1>Add Meal</h1>
  </header>

  <?php if ($message): ?>
    <div class="message"><?php echo htmlspecialchars($message); ?></div>
  <?php endif; ?>

  <form method="POST" action="meals.php">
    <label for="meal_date">Date</label>
    <input type="date" id="meal_date" name="meal_date" required />

    <label for="meal_type">Meal Type</label>
    <select id="meal_type" name="meal_type" required>
      <option value="">-- Select Meal --</option>
      <option value="Breakfast">Breakfast</option>
      <option value="Lunch">Lunch</option>
      <option value="Dinner">Dinner</option>
    </select>

    <label for="menu_items">Menu Items</label>
    <textarea id="menu_items" name="menu_items" placeholder="E.g., Rice, Dal, Salad..." required></textarea>

    <button type="submit">Add Meal</button>
  </form>
</body>
</html>
