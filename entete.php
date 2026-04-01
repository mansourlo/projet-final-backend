<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/config.php';
$pageTitle = $pageTitle ?? SITE_NAME;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= escape($pageTitle) ?> — <?= SITE_NAME ?></title>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="header-inner">
                <a href="<?= url('accueil.php') ?>" class="logo">
                    <span class="logo-icon">📰</span>
                    <span class="logo-text"><?= SITE_NAME ?></span>
                </a>
                <div class="user-info">
                    <?php if (isLoggedIn()): ?>
                        <div class="user-avatar"><?= strtoupper(substr($_SESSION['prenom'], 0, 1)) ?></div>
                        <span><?= escape($_SESSION['prenom'] . ' ' . $_SESSION['nom']) ?></span>
                        <span class="badge badge-<?= $_SESSION['role'] ?>"><?= escape($_SESSION['role']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
