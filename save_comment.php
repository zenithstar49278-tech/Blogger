<?php
require 'db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$post_id = (int)($_POST['post_id'] ?? 0);
$name = trim($_POST['author_name'] ?? '');
$email = trim($_POST['author_email'] ?? '');
$content = trim($_POST['content'] ?? '');

if (!$post_id || !$name || !$email || !$content) {
    die('All fields required.');
}

$stmt = $conn->prepare("INSERT INTO comments (post_id, author_name, author_email, content, is_approved) VALUES (?, ?, ?, ?, 1)");
$stmt->bind_param('isss', $post_id, $name, $email, $content);
if ($stmt->execute()) {
    $s = $conn->prepare("SELECT slug FROM posts WHERE id = ? LIMIT 1");
    $s->bind_param('i', $post_id); $s->execute();
    $r = $s->get_result()->fetch_assoc();
    $slug = $r['slug'] ?? '';
    header('Location: post.php?slug=' . urlencode($slug));
    exit;
} else {
    die('Database error: ' . $stmt->error);
}
