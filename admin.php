<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$posts = $conn->prepare("SELECT p.id,p.title,p.created_at,c.name AS category FROM posts p LEFT JOIN categories c ON p.category_id=c.id WHERE p.user_id = ? ORDER BY p.created_at DESC");
$posts->bind_param('i', $_SESSION['user_id']);
$posts->execute();
$posts = $posts->get_result();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Dashboard</title><link rel="stylesheet" href="style.css"></head>
<body>
  <header class="header"><h1>Dashboard</h1></header>
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px">
      <h3>Your Posts</h3>
      <a class="button" href="create_post.php">New Post</a>
    </div>

    <div class="card">
      <?php if ($posts->num_rows==0): ?>
        <p>No posts yet. <a href="create_post.php" class="button">Create Post</a></p>
      <?php else: ?>
        <table style="width:100%;border-collapse:collapse">
          <thead><tr style="text-align:left"><th>ID</th><th>Title</th><th>Date</th><th>Category</th><th>Actions</th></tr></thead>
          <tbody>
            <?php while($r = $posts->fetch_assoc()): ?>
              <tr>
                <td><?php echo $r['id']; ?></td>
                <td><?php echo htmlspecialchars($r['title']); ?></td>
                <td><?php echo $r['created_at']; ?></td>
                <td><?php echo htmlspecialchars($r['category'] ?? 'General'); ?></td>
                <td>
                  <a class="button" href="edit.php?id=<?php echo $r['id']; ?>">Edit</a>
                  <a class="button" href="delete.php?id=<?php echo $r['id']; ?>" onclick="return confirm('Delete this post?')">Delete</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
