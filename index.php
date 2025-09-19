<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<?php
require 'db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$cat   = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

$cats = $conn->query("SELECT id,name FROM categories ORDER BY name");

$sql = "SELECT p.id,p.title,p.excerpt,p.slug,p.created_at,c.name AS category,u.display_name
        FROM posts p
        LEFT JOIN categories c ON p.category_id=c.id
        LEFT JOIN users u ON p.user_id=u.id
        WHERE 1=1";
$params = [];
$types = '';

if ($cat) {
    $sql .= " AND p.category_id = ?";
    $params[] = $cat; $types .= 'i';
}
if ($search !== '') {
    $sql .= " AND (p.title LIKE ? OR p.content LIKE ?)";
    $params[] = "%$search%"; $params[] = "%$search%"; $types .= 'ss';
}
$sql .= " ORDER BY p.created_at DESC LIMIT 50";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>My Blogger</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="header">
    <h1>My Blogger</h1>
    <div class="nav">
      <a href="index.php">Home</a>
      <?php if(isset($_SESSION['user_id'])): ?>
        <a href="admin.php">Dashboard</a>
        <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
      <?php else: ?>
        <a href="login.php">Admin Login</a>
      <?php endif; ?>
    </div>
  </header>

  <div class="container">
    <div class="grid">
      <main>
        <?php if ($result->num_rows == 0): ?>
          <div class="card">No posts yet. <?php if(isset($_SESSION['user_id'])): ?><a class="button" href="create_post.php">Create the first post</a><?php endif; ?></div>
        <?php else: ?>
          <?php while($row = $result->fetch_assoc()): ?>
            <article class="card post-card">
              <h2><?php echo htmlspecialchars($row['title']); ?></h2>
              <div style="color:#888;font-size:13px;margin-bottom:10px">
                <?php echo htmlspecialchars($row['category'] ?? 'General'); ?> • <?php echo htmlspecialchars($row['display_name'] ?? 'Unknown'); ?> • <?php echo $row['created_at']; ?>
              </div>
              <p><?php echo htmlspecialchars($row['excerpt'] ? $row['excerpt'] : mb_substr(strip_tags($row['content']),0,180,'utf-8') . '...'); ?></p>
              <a class="button" href="post.php?slug=<?php echo urlencode($row['slug']); ?>">Read More</a>
            </article>
          <?php endwhile; ?>
        <?php endif; ?>
      </main>

      <aside class="sidebar">
        <div class="card">
          <h3>Search</h3>
          <form method="get">
            <div class="search-box"><input type="text" name="q" placeholder="Search posts..." value="<?php echo htmlspecialchars($search); ?>"></div>
            <button class="button" type="submit" style="margin-top:8px">Search</button>
          </form>
        </div>

        <div class="card" style="margin-top:12px">
          <h3>Categories</h3>
          <div>
            <a href="index.php" class="list-item">All</a>
            <?php while($c = $cats->fetch_assoc()): ?>
              <div class="list-item"><a href="index.php?cat=<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></a></div>
            <?php endwhile; ?>
          </div>
        </div>
      </aside>
    </div>
  </div>

  <footer class="footer">&copy; <?php echo date('Y'); ?> My Blogger</footer>
</body>
</html>
