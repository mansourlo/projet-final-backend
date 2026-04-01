<?php
require_once __DIR__ . '/../config.php';
session_start();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) redirect('accueil.php', 'Article introuvable.', 'error');

$pdo  = getDB();
$stmt = $pdo->prepare("
    SELECT a.*, c.nom AS categorie, c.id AS categorie_id,
           u.nom AS auteur_nom, u.prenom AS auteur_prenom
    FROM articles a
    JOIN categories c ON a.categorie_id = c.id
    JOIN utilisateurs u ON a.auteur_id   = u.id
    WHERE a.id = :id
");
$stmt->execute([':id' => $id]);
$article = $stmt->fetch();

if (!$article) redirect('accueil.php', 'Article introuvable.', 'error');

$pageTitle = $article['titre'];
include __DIR__ . '/../entete.php';
include __DIR__ . '/../menu.php';
?>

<div class="article-detail">
    <div class="breadcrumb">
        <a href="<?= url('accueil.php') ?>">Accueil</a> ›
        <a href="<?= url('accueil.php') ?>?categorie=<?= $article['categorie_id'] ?>"><?= escape($article['categorie']) ?></a> ›
        <span><?= escape($article['titre']) ?></span>
    </div>

    <article class="article-full">
        <?php if ($article['image']): ?>
            <div class="article-full-image">
                <img src="<?= escape(url('assets/uploads/' . $article['image'])) ?>" alt="<?= escape($article['titre']) ?>">
            </div>
        <?php endif; ?>
        <div class="article-full-header">
            <span class="article-category"><?= escape($article['categorie']) ?></span>
            <h1><?= escape($article['titre']) ?></h1>
            <div class="article-meta">
                <span>👤 <?= escape($article['auteur_prenom'] . ' ' . $article['auteur_nom']) ?></span>
                <span>📅 <?= date('d/m/Y à H:i', strtotime($article['date_publication'])) ?></span>
            </div>
        </div>
        <div class="article-description"><p><em><?= escape($article['description_courte']) ?></em></p></div>
        <div class="article-content"><?= nl2br(escape($article['contenu'])) ?></div>

        <?php if (isEditor()): ?>
        <div class="article-actions">
            <a href="<?= url('articles/modifier.php') ?>?id=<?= $article['id'] ?>" class="btn btn-secondary">✏️ Modifier</a>
            <form method="POST" action="<?= url('articles/supprimer.php') ?>" style="display:inline"
                  onsubmit="return confirm('Supprimer cet article ?');">
                <input type="hidden" name="id" value="<?= $article['id'] ?>">
                <button type="submit" class="btn btn-danger">🗑️ Supprimer</button>
            </form>
        </div>
        <?php endif; ?>
    </article>
    <div class="back-link" style="margin-top:1.5rem">
        <a href="<?= url('accueil.php') ?>" class="btn btn-outline">← Retour aux articles</a>
    </div>
</div>

<?php include __DIR__ . '/../pied.php'; ?>
