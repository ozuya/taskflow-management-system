<?php
$conn = new mysqli("localhost", "root", "", "taskflow_db");

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// CREATE
if (isset($_POST['add'])) {
  $title = $_POST['title'];
  $description = $_POST['description'];
  $stmt = $conn->prepare("INSERT INTO tasks (title, description) VALUES (?, ?)");
  $stmt->bind_param("ss", $title, $description);
  $stmt->execute();
  header("Location: index.php");
}

// UPDATE
if (isset($_POST['update'])) {
  $id = $_POST['id'];
  $title = $_POST['title'];
  $description = $_POST['description'];
  $stmt = $conn->prepare("UPDATE tasks SET title=?, description=? WHERE id=?");
  $stmt->bind_param("ssi", $title, $description, $id);
  $stmt->execute();
  header("Location: index.php");
}

// DELETE
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $stmt = $conn->prepare("DELETE FROM tasks WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  header("Location: index.php");
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>TaskFlow Management System</title>
  <style>
    body { font-family: Arial; background: #f0f0f0; padding: 20px; }
    .container { background: white; padding: 20px; max-width: 600px; margin: auto; border-radius: 10px; box-shadow: 0 0 10px #ccc; }
    input, textarea, button { width: 100%; padding: 10px; margin: 5px 0; }
    table { width: 100%; margin-top: 20px; border-collapse: collapse; }
    th, td { padding: 10px; border: 1px solid #ddd; }
    .actions { display: flex; gap: 5px; }
    .edit-form { background: #f9f9f9; padding: 10px; margin-top: 10px; }
  </style>
</head>
<body>

<div class="container">
  <h2>TaskFlow Management System</h2>

  <!-- Task Form -->
  <?php if (isset($_GET['edit'])):
    $id = $_GET['edit'];
    $result = $conn->query("SELECT * FROM tasks WHERE id=$id");
    $task = $result->fetch_assoc();
  ?>
    <form method="POST">
      <input type="hidden" name="id" value="<?= $task['id'] ?>">
      <input type="text" name="title" value="<?= $task['title'] ?>" required>
      <textarea name="description"><?= $task['description'] ?></textarea>
      <button type="submit" name="update">Update Task</button>
    </form>
  <?php else: ?>
    <form method="POST">
      <input type="text" name="title" placeholder="Enter task title" required>
      <textarea name="description" placeholder="Enter description"></textarea>
      <button type="submit" name="add">Add Task</button>
    </form>
  <?php endif; ?>

  <!-- Task Table -->
  <table>
    <thead>
      <tr>
        <th>Title</th>
        <th>Description</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $result = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");
    while ($row = $result->fetch_assoc()):
    ?>
      <tr>
        <td><?= htmlspecialchars($row['title']) ?></td>
        <td><?= htmlspecialchars($row['description']) ?></td>
        <td><?= $row['created_at'] ?></td>
        <td class="actions">
          <a href="?edit=<?= $row['id'] ?>">Edit</a>
          <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Delete this task?')">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

</body>
</html>
