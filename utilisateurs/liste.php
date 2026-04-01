<?php
require_once __DIR__ . '/../config.php';
session_start();
requireAdmin();

$pageTitle = 'Gestion des utilisateurs';
$pdo = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action']??'') === 'supprimer') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id === $_SESSION['user_id']) {
        redirect('utilisateurs/liste.php', 'Vous ne pouvez pas supprimer votre propre compte.', 'error');
    }
    $pdo->prepare("DELETE FROM utilisateurs WHERE id=:id")->execute([':id'=>$id]);
    redirect('utilisateurs/liste.php', 'Utilisateur supprimé.');
}

$utilisateurs = $pdo->query("SELECT u.*, COUNT(a.id) AS nb_articles FROM utilisateurs u LEFT JOIN articles a ON a.auteur_id=u.id GROUP BY u.id ORDER BY u.nom,u.prenom")->fetchAll();
include __DIR__ . '/../entete.php';
include __DIR__ . '/../menu.php';
?>
<div class="page-header">
    <h1>👥 Gestion des utilisateurs</h1>
    <a href="<?= url('utilisateurs/ajouter.php') ?>" class="btn btn-primary">➕ Nouvel utilisateur</a>
</div>
<div class="table-responsive">
    <table class="data-table">
        <thead><tr><th>#</th><th>Nom complet</th><th>Login</th><th>Rôle</th><th>Articles</th><th>Inscrit le</th><th>Actions</th></tr></thead>
        <tbody>
            <?php foreach ($utilisateurs as $u): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= escape($u['prenom'].' '.$u['nom']) ?></td>
                <td><code><?= escape($u['login']) ?></code></td>
                <td><span class="badge badge-<?= $u['role'] ?>"><?= escape($u['role']) ?></span></td>
                <td><?= $u['nb_articles'] ?></td>
                <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                <td class="actions">
                    <a href="<?= url('utilisateurs/modifier.php') ?>?id=<?= $u['id'] ?>" class="btn btn-sm btn-secondary">✏️</a>
                    <?php if ($u['id'] !== $_SESSION['user_id']): ?>
                    <form method="POST" action="<?= url('utilisateurs/liste.php') ?>" style="display:inline" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                        <input type="hidden" name="action" value="supprimer">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">🗑️</button>
                    </form>
                    <?php else: ?><span class="badge">Vous</span><?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '/../pied.php'; ?>
