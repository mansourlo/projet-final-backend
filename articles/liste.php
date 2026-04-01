<?php
require_once __DIR__ . '/../config.php';
session_start();
requireEditor();

$pageTitle = 'Gestion des articles';
$pdo = getDB();
$articles = $pdo->query("
    SELECT a.id, a.titre, a.date_publication, c.nom AS categorie,
           u.prenom, u.nom AS auteur_nom
    FROM articles a
    JOIN categories c ON a.categorie_id = c.id
    JOIN utilisateurs u ON a.auteur_id   = u.id
    ORDER BY a.date_publication DESC
")->fetchAll();

include __DIR__ . '/../entete.php';
include __DIR__ . '/../menu.php';
?>
<div class="page-header">
    <h1>📋 Gestion des articles</h1>
    <a href="<?= url('articles/ajouter.php') ?>" class="btn btn-primary">➕ Nouvel article</a>
</div>

<?php if (empty($articles)): ?>
    <div class="empty-state"><p>Aucun article. <a href="<?= url('articles/ajouter.php') ?>">Créer le premier article</a></p></div>
<?php else: ?>
<div class="table-responsive">
    <table class="data-table">
        <thead><tr><th>#</th><th>Titre</th><th>Catégorie</th><th>Auteur</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($articles as $a): ?>
            <tr>
                <td><?= $a['id'] ?></td>
                <td><a href="<?= url('articles/detail.php') ?>?id=<?= $a['id'] ?>"><?= escape($a['titre']) ?></a></td>
                <td><span class="article-category"><?= escape($a['categorie']) ?></span></td>
                <td><?= escape($a['prenom'] . ' ' . $a['auteur_nom']) ?></td>
                <td><?= date('d/m/Y', strtotime($a['date_publication'])) ?></td>
                <td class="actions">
                    <a href="<?= url('articles/modifier.php') ?>?id=<?= $a['id'] ?>" class="btn btn-sm btn-secondary">✏️</a>
                    <form method="POST" action="<?= url('articles/supprimer.php') ?>" style="display:inline"
                          onsubmit="return confirm('Supprimer cet article définitivement ?');">
                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<?php include __DIR__ . '/../pied.php'; ?>
