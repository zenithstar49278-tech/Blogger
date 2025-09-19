<?php
require 'db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!$username || !$password) $error = 'Enter username and password.';
    else {
        $stmt = $conn->prepare("SELECT id, username, password, display_name FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param('s', $username); $stmt->execute();
        $u = $stmt->get_result()->fetch_assoc();
        if ($u && password_verify($password, $u['password'])) {
            $_SESSION['user_id'] = $u['id'];
            $_SESSION['username'] = $u['username'];
            $_SESSION['display_name'] = $u['display_name'];
            header('Location: admin.php'); exit;
        } else $error = 'Invalid credentials.';
    }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Admin Login</title><link rel="stylesheet" href="style.css"></head>
<body>
  <header class="header"><h1>Admin Login</h1></header>
  <div class="container">
    <div class="card" style="max-width:420px;margin:auto">
      <?php if($error) echo "<div style='color:#b00020;margin-bottom:10px'>$error</div>"; ?>
      <form method="post">
        <input class="form-input" name="username" placeholder="Username" required>
        <input class="form-input" name="password" placeholder="Password" type="password" required>
        <button class="button" type="submit">Login</button>
      </form>
    </div>
  </div>
</body>
</html>
