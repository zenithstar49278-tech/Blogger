<?php
require 'db.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$cats = $conn->query("SELECT id,name FROM categories ORDER BY name");
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

    if (!$title || !$content) {
        $error = 'Title and content are required.';
    } else {
        $slug = preg_replace('/[^a-z0-9]+/i','-', strtolower($title)) . '-' . time();
        $stmt = $conn->prepare("INSERT INTO posts (user_id, category_id, title, slug, excerpt, content, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param('iissss', $_SESSION['user_id'], $category_id, $title, $slug, $excerpt, $content);
        if ($stmt->execute()) {
            header('Location: post.php?slug=' . urlencode($slug));
            exit;
        } else {
            $error = 'DB error: ' . $stmt->error;
        }
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Create Post</title><link rel="stylesheet" href="style.css"></head>
<body>
  <header class="header"><h1>Create Post</h1></header>
  <div class="container">
    <div class="card">
      <?php if ($error) echo "<div style='color:#b00020;margin-bottom:10px'>$error</div>"; ?>
      <form method="post">
        <input class="form-input" name="title" placeholder="Title" required>
        <select class="form-input" name="category_id">
          <option value="">Select Category (optional)</option>
          <?php while($c = $cats->fetch_assoc()): ?>
            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
          <?php endwhile; ?>
        </select>
        <input class="form-input" name="excerpt" placeholder="Short excerpt (optional)">
        <textarea class="form-input" name="content" rows="10" placeholder="Write your post..." required></textarea>
        <button class="button" type="submit">Publish</button>
      </form>
    </div>
  </div>
</body>
</html>
