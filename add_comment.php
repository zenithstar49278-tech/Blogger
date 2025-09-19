<?php
// add_comment.php
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$post_id = (int)$_POST['post_id'];
$name = trim($_POST['author_name']);
$email = trim($_POST['author_email']);
$content = trim($_POST['content']);

// basic validation
if (!$post_id || !$name || !$email || !$content) {
    die('All fields required.');
}

$stmt = $conn->prepare("INSERT INTO comments (post_id, author_name, author_email, content, is_approved) VALUES (?, ?, ?, ?, 1)");
$stmt->bind_param('isss', $post_id, $name, $email, $content);

if ($stmt->execute()) {
    header('Location: post.php?slug=' . urlencode($_GET['slug'] ?? ''));
    exit;
} else {
    die('DB error: ' . $stmt->error);
}
