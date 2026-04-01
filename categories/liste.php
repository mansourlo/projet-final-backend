<?php
require_once __DIR__ . '/../config.php';
session_start();
requireEditor();

$pageTitle = 'Gestion des catégories';
$pdo       = getDB();
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'ajouter') {
        $nom  = trim($_POST['nom'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if (strlen($nom) < 2) { $errors[] = 'Le nom doit contenir au moins 2 caractères.'; }
        else {
            $pdo->prepare("INSERT INTO categories (nom,description) VALUES (:nom,:desc)")->execute([':nom'=>$nom,':desc'=>$desc]);
            redirect('categories/liste.php', 'Catégorie ajoutée !');
        }
    }

    if ($action === 'modifier') {
        $id   = (int)($_POST['id'] ?? 0);
        $nom  = trim($_POST['nom'] ?? '');
        $desc = trim($_POST['description'] ?? '');
        if ($id > 0 && strlen($nom) >= 2) {
            $pdo->prepare("UPDATE categories SET nom=:nom,description=:desc WHERE id=:id")->execute([':nom'=>$nom,':desc'=>$desc,':id'=>$id]);
            redirect('categories/liste.php', 'Catégorie modifiée !');
        }
    }

    if ($action === 'supprimer') {
        $id    = (int)($_POST['id'] ?? 0);
        $count = $pdo->prepare("SELECT COUNT(*) FROM articles WHERE categorie_id = :id");
        $count->execute([':id'=>$id]);
        if ((int)$count->fetchColumn() > 0) {
            redirect('categories/liste.php', 'Impossible : des articles utilisent cette catégorie.', 'error');
        } else {
            $pdo->prepare("DELETE FROM categories WHERE id=:id")->execute([':id'=>$id]);
            redirect('categories/liste.php', 'Catégorie supprimée.');
        }
    }
}

$categories = $pdo->query("SELECT c.*, COUNT(a.id) AS nb_articles FROM categories c LEFT JOIN articles a ON a.categorie_id=c.id GROUP BY c.id ORDER BY c.nom")->fetchAll();
$editId     = (int)($_GET['edit'] ?? 0);
$editCat    = null;
if ($editId > 0) {
    $s = $pdo->prepare("SELECT * FROM categories WHERE id=:id");
    $s->execute([':id'=>$editId]);
    $editCat = $s->fetch();
}

include __DIR__ . '/../entete.php';
include __DIR__ . '/../menu.php';
?>
<div class="page-header"><h1>🏷️ Gestion des catégories</h1></div>
<?php if (!empty($errors)): ?>
    <div class="alert alert-error"><?php foreach($errors as $e): ?><p>✗ <?= escape($e) ?></p><?php endforeach; ?></div>
<?php endif; ?>

<div class="two-col">
    <div class="form-card">
        <h2><?= $editCat ? '✏️ Modifier' : '➕ Nouvelle catégorie' ?></h2>
        <form method="POST" action="<?= url('categories/liste.php') ?>" id="formCategorie" novalidate>
            <input type="hidden" name="action" value="<?= $editCat ? 'modifier' : 'ajouter' ?>">
            <?php if ($editCat): ?><input type="hidden" name="id" value="<?= $editCat['id'] ?>"><?php endif; ?>
            <div class="form-group">
                <label for="nom">Nom <span class="required">*</span></label>
                <input type="text" id="nom" name="nom" class="form-control" value="<?= escape($editCat['nom'] ?? '') ?>" placeholder="Ex: Technologie">
                <span class="field-error" id="errorNom"></span>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="3"><?= escape($editCat['description'] ?? '') ?></textarea>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary"><?= $editCat ? '💾 Modifier' : '✅ Ajouter' ?></button>
                <?php if ($editCat): ?><a href="<?= url('categories/liste.php') ?>" class="btn btn-secondary">Annuler</a><?php endif; ?>
            </div>
        </form>
    </div>

    <div>
        <h2>Liste des catégories</h2>
        <div class="table-responsive">
            <table class="data-table">
                <thead><tr><th>Nom</th><th>Articles</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                    <tr class="<?= $editId==$cat['id']?'row-editing':'' ?>">
                        <td><strong><?= escape($cat['nom']) ?></strong><?php if($cat['description']): ?><br><small><?= escape($cat['description']) ?></small><?php endif; ?></td>
                        <td><a href="<?= url('accueil.php') ?>?categorie=<?= $cat['id'] ?>"><?= $cat['nb_articles'] ?> article(s)</a></td>
                        <td class="actions">
                            <a href="<?= url('categories/liste.php') ?>?edit=<?= $cat['id'] ?>" class="btn btn-sm btn-secondary">✏️</a>
                            <form method="POST" action="<?= url('categories/liste.php') ?>" style="display:inline" onsubmit="return confirm('Supprimer ?');">
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="id" value="<?= $cat['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger" <?= $cat['nb_articles']>0?'disabled':'' ?>>🗑️</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="<?= url('assets/js/validation.js') ?>"></script>
<?php include __DIR__ . '/../pied.php'; ?>
