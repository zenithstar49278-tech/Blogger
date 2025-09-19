<?php
require 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
if (!$slug) { header('Location: index.php'); exit; }

$stmt = $conn->prepare("SELECT p.*, u.display_name, c.name AS category FROM posts p LEFT JOIN users u ON p.user_id=u.id LEFT JOIN categories c ON p.category_id=c.id WHERE p.slug = ? LIMIT 1");
$stmt->bind_param('s',$slug);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();
if (!$post) { echo "Post not found"; exit; }

$cm = $conn->prepare("SELECT author_name, content, created_at FROM comments WHERE post_id = ? AND is_approved=1 ORDER BY created_at ASC");
$cm->bind_param('i', $post['id']); $cm->execute();
$comments = $cm->get_result();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($post['title']); ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="header"><h1>My Blogger</h1></header>
  <div class="container">
    <div class="card">
      <h2><?php echo htmlspecialchars($post['title']); ?></h2>
      <div style="color:#888;margin-bottom:12px"><?php echo htmlspecialchars($post['category'] ?? 'General'); ?> • <?php echo htmlspecialchars($post['display_name'] ?? 'Unknown'); ?> • <?php echo $post['created_at']; ?></div>
      <div style="line-height:1.7;color:#333"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>

      <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
        <div style="margin-top:12px">
          <a class="button" href="edit.php?id=<?php echo $post['id']; ?>">Edit</a>
          <a class="button" href="delete.php?id=<?php echo $post['id']; ?>" onclick="return confirm('Delete this post?')">Delete</a>
        </div>
      <?php endif; ?>
    </div>

    <div class="card" style="margin-top:18px">
      <h3>Comments</h3>
      <?php while($c = $comments->fetch_assoc()): ?>
        <div style="padding:8px;background:#fff7f2;border-radius:8px;margin-bottom:8px">
          <strong><?php echo htmlspecialchars($c['author_name']); ?></strong> <small style="color:#777"><?php echo $c['created_at']; ?></small>
          <div><?php echo nl2br(htmlspecialchars($c['content'])); ?></div>
        </div>
      <?php endwhile; ?>

      <h4>Leave a Comment</h4>
      <form method="post" action="save_comment.php">
        <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
        <input class="form-input" name="author_name" placeholder="Your name" required>
        <input class="form-input" name="author_email" placeholder="Your email" required type="email">
        <textarea class="form-input" name="content" rows="4" placeholder="Your comment" required></textarea>
        <button class="button" type="submit">Post Comment</button>
      </form>
    </div>
  </div>
  <footer class="footer">&copy; <?php echo date('Y'); ?> My Blogger</footer>
</body>
</html>
