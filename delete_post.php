<?php
// delete_post.php
require 'db.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: index.php'); exit; }

$s = $conn->prepare("SELECT user_id FROM posts WHERE id=? LIMIT 1");
$s->bind_param('i', $id); $s->execute();
$post = $s->get_result()->fetch_assoc();
if (!$post) { header('Location: index.php'); exit; }
if ($post['user_id'] != $_SESSION['user_id']) { die('Not authorized'); }

$d = $conn->prepare("DELETE FROM posts WHERE id=?");
$d->bind_param('i', $id);
$d->execute();
header('Location: admin.php');
exit;
