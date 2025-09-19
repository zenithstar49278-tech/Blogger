<?php
// edit_post.php (same logic as earlier edit.php but name matches create_post)
require 'db.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: index.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $id); $stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
if (!$post) { echo "Post not found"; exit; }
if ($post['user_id'] != $_SESSION['user_id']) { echo "Not allowed"; exit; }

$cats = $conn->query("SELECT id,name FROM categories ORDER BY name");
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    if (!$title || !$content) $error = 'Title and content required.';
    else {
        $stmt = $conn->prepare("UPDATE posts SET title=?, excerpt=?, content=?, category_id=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param('ssiii', $title, $excerpt, $content, $category_id, $id);
        if ($stmt->execute()) {
            header('Location: post.php?slug=' . urlencode($post['slug']));
            exit;
        } else $error = 'DB error: ' . $stmt->error;
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Edit Post</title><link rel="stylesheet" href="style.css"></head>
<body>
  <header class="header"><h1>Edit Post</h1></header>
  <div class="container">
    <div class="card">
      <?php if ($error) echo "<div style='color:#b00020;margin-bottom:10px'>$error</div>"; ?>
      <form method="post">
        <input class="form-input" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        <select class="form-input" name="category_id">
          <option value="">Select Category (optional)</option>
          <?php while($c = $cats->fetch_assoc()): ?>
            <option value="<?php echo $c['id']; ?>" <?php if($post['category_id'] == $c['id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['name']); ?></option>
          <?php endwhile; ?>
        </select>
        <input class="form-input" name="excerpt" value="<?php echo htmlspecialchars($post['excerpt']); ?>">
        <textarea class="form-input" name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        <button class="button" type="submit">Save Changes</button>
      </form>
    </div>
  </div>
</body>
</html>
