<?php
$pdo  = getDB();
$cats = $pdo->query("SELECT id, nom FROM categories ORDER BY nom")->fetchAll();
$cur  = basename($_SERVER['PHP_SELF']);
$inDir = function(string $d): bool {
    return str_contains(str_replace('\\','/',$_SERVER['SCRIPT_FILENAME']), '/'.$d.'/');
};
?>
<nav class="main-nav">
    <div class="container">
        <button class="nav-toggle" id="navToggle" aria-label="Menu">☰</button>
        <ul class="nav-list" id="navList">
            <li><a href="<?= url('accueil.php') ?>" class="<?= $cur==='accueil.php'?'active':'' ?>">🏠 Accueil</a></li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle">📂 Catégories ▾</a>
                <ul class="dropdown-menu">
                    <?php foreach ($cats as $cat): ?>
                        <li><a href="<?= url('accueil.php') ?>?categorie=<?= $cat['id'] ?>"><?= escape($cat['nom']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </li>

            <?php if (isEditor()): ?>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle">✏️ Articles ▾</a>
                <ul class="dropdown-menu">
                    <li><a href="<?= url('articles/ajouter.php') ?>">➕ Ajouter un article</a></li>
                    <li><a href="<?= url('articles/liste.php') ?>">📋 Gérer les articles</a></li>
                </ul>
            </li>
            <li><a href="<?= url('categories/liste.php') ?>" class="<?= $inDir('categories')?'active':'' ?>">🏷️ Catégories</a></li>
            <?php endif; ?>

            <?php if (isAdmin()): ?>
            <li><a href="<?= url('utilisateurs/liste.php') ?>" class="<?= $inDir('utilisateurs')?'active':'' ?>">👥 Utilisateurs</a></li>
            <?php endif; ?>

            <?php if (isLoggedIn()): ?>
                <li><a href="<?= url('deconnexion.php') ?>" class="btn-logout">🚪 Déconnexion</a></li>
            <?php else: ?>
                <li><a href="<?= url('connexion.php') ?>" class="btn-login">🔐 Connexion</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
<div class="content-wrapper">
    <div class="container">
        <?= flashMessage() ?>
