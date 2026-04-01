<?php
require_once __DIR__ . '/../config.php';
session_start();
requireEditor();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('articles/liste.php');

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) redirect('articles/liste.php', 'Article introuvable.', 'error');

$pdo  = getDB();
$stmt = $pdo->prepare("SELECT image FROM articles WHERE id = :id");
$stmt->execute([':id' => $id]);
$article = $stmt->fetch();
if (!$article) redirect('articles/liste.php', 'Article introuvable.', 'error');

if ($article['image'] && file_exists(UPLOAD_DIR . $article['image'])) unlink(UPLOAD_DIR . $article['image']);

$pdo->prepare("DELETE FROM articles WHERE id = :id")->execute([':id' => $id]);
redirect('articles/liste.php', 'Article supprimé avec succès.');
