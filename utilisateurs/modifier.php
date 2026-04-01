<?php
require_once __DIR__ . '/../config.php';
session_start();
requireAdmin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) redirect('utilisateurs/liste.php', 'Utilisateur introuvable.', 'error');

$pdo  = getDB();
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id=:id");
$stmt->execute([':id'=>$id]);
$user = $stmt->fetch();
if (!$user) redirect('utilisateurs/liste.php', 'Utilisateur introuvable.', 'error');

$pageTitle = 'Modifier l\'utilisateur';
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom    = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $login  = trim($_POST['login'] ?? '');
    $role   = $_POST['role'] ?? '';
    $mdp    = $_POST['mot_de_passe'] ?? '';
    $mdp2   = $_POST['mot_de_passe2'] ?? '';

    if (strlen($nom) < 2)    $errors[] = 'Nom trop court.';
    if (strlen($prenom) < 2) $errors[] = 'Prénom trop court.';
    if (strlen($login) < 3)  $errors[] = 'Login trop court.';
    if (!in_array($role, ['editeur','administrateur'])) $errors[] = 'Rôle invalide.';
    if ($mdp !== '' && strlen($mdp) < 8) $errors[] = 'Nouveau mot de passe trop court (min 8).';
    if ($mdp !== '' && $mdp !== $mdp2)   $errors[] = 'Les mots de passe ne correspondent pas.';

    if (empty($errors)) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE login=:login AND id!=:id");
        $check->execute([':login'=>$login,':id'=>$id]);
        if ((int)$check->fetchColumn() > 0) $errors[] = 'Ce login est déjà utilisé.';
    }

    if (empty($errors)) {
        if ($mdp !== '') {
            $hash = password_hash($mdp, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE utilisateurs SET nom=:nom,prenom=:prenom,login=:login,role=:role,mot_de_passe=:mdp WHERE id=:id")
                ->execute([':nom'=>$nom,':prenom'=>$prenom,':login'=>$login,':role'=>$role,':mdp'=>$hash,':id'=>$id]);
        } else {
            $pdo->prepare("UPDATE utilisateurs SET nom=:nom,prenom=:prenom,login=:login,role=:role WHERE id=:id")
                ->execute([':nom'=>$nom,':prenom'=>$prenom,':login'=>$login,':role'=>$role,':id'=>$id]);
        }
        redirect('utilisateurs/liste.php', 'Utilisateur modifié !');
    }
}

include __DIR__ . '/../entete.php';
include __DIR__ . '/../menu.php';
?>
<div class="page-header"><h1>✏️ Modifier l'utilisateur</h1></div>
<?php if (!empty($errors)): ?>
    <div class="alert alert-error"><?php foreach($errors as $e): ?><p>✗ <?= escape($e) ?></p><?php endforeach; ?></div>
<?php endif; ?>
<form method="POST" action="<?= url('utilisateurs/modifier.php') ?>?id=<?= $id ?>" id="formUtilisateur" novalidate class="form-card">
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="form-row">
        <div class="form-group">
            <label for="prenom">Prénom <span class="required">*</span></label>
            <input type="text" id="prenom" name="prenom" class="form-control" value="<?= escape($_POST['prenom']??$user['prenom']) ?>">
        </div>
        <div class="form-group">
            <label for="nom">Nom <span class="required">*</span></label>
            <input type="text" id="nom" name="nom" class="form-control" value="<?= escape($_POST['nom']??$user['nom']) ?>">
        </div>
    </div>
    <div class="form-group">
        <label for="login">Login <span class="required">*</span></label>
        <input type="text" id="login" name="login" class="form-control" value="<?= escape($_POST['login']??$user['login']) ?>">
    </div>
    <div class="form-group">
        <label for="role">Rôle <span class="required">*</span></label>
        <select id="role" name="role" class="form-control" <?= $id===$_SESSION['user_id']?'disabled':'' ?>>
            <option value="editeur" <?= $user['role']==='editeur'?'selected':'' ?>>Éditeur</option>
            <option value="administrateur" <?= $user['role']==='administrateur'?'selected':'' ?>>Administrateur</option>
        </select>
        <?php if ($id===$_SESSION['user_id']): ?>
            <input type="hidden" name="role" value="<?= escape($user['role']) ?>">
            <small>Vous ne pouvez pas modifier votre propre rôle.</small>
        <?php endif; ?>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label for="mot_de_passe">Nouveau mot de passe <span class="optional">(laisser vide pour ne pas changer)</span></label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control">
            <span class="field-error" id="errorMdp"></span>
        </div>
        <div class="form-group">
            <label for="mot_de_passe2">Confirmer</label>
            <input type="password" id="mot_de_passe2" name="mot_de_passe2" class="form-control">
            <span class="field-error" id="errorMdp2"></span>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
        <a href="<?= url('utilisateurs/liste.php') ?>" class="btn btn-secondary">Annuler</a>
    </div>
</form>
<script src="<?= url('assets/js/validation.js') ?>"></script>
<?php include __DIR__ . '/../pied.php'; ?>
