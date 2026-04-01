<?php
require_once __DIR__ . '/../config.php';
session_start();
requireAdmin();

$pageTitle = 'Ajouter un utilisateur';
$pdo       = getDB();
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom    = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $login  = trim($_POST['login'] ?? '');
    $mdp    = $_POST['mot_de_passe'] ?? '';
    $mdp2   = $_POST['mot_de_passe2'] ?? '';
    $role   = $_POST['role'] ?? '';

    if (strlen($nom) < 2)    $errors[] = 'Nom trop court (min 2 caractères).';
    if (strlen($prenom) < 2) $errors[] = 'Prénom trop court (min 2 caractères).';
    if (strlen($login) < 3)  $errors[] = 'Login trop court (min 3 caractères).';
    if (strlen($mdp) < 8)    $errors[] = 'Mot de passe trop court (min 8 caractères).';
    if ($mdp !== $mdp2)      $errors[] = 'Les mots de passe ne correspondent pas.';
    if (!in_array($role, ['editeur','administrateur'])) $errors[] = 'Rôle invalide.';

    if (empty($errors)) {
        $check = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE login=:login");
        $check->execute([':login'=>$login]);
        if ((int)$check->fetchColumn() > 0) $errors[] = 'Ce login est déjà utilisé.';
    }

    if (empty($errors)) {
        $hash = password_hash($mdp, PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO utilisateurs (nom,prenom,login,mot_de_passe,role) VALUES (:nom,:prenom,:login,:mdp,:role)")
            ->execute([':nom'=>$nom,':prenom'=>$prenom,':login'=>$login,':mdp'=>$hash,':role'=>$role]);
        redirect('utilisateurs/liste.php', 'Utilisateur ajouté !');
    }
}

include __DIR__ . '/../entete.php';
include __DIR__ . '/../menu.php';
?>
<div class="page-header"><h1>➕ Ajouter un utilisateur</h1></div>
<?php if (!empty($errors)): ?>
    <div class="alert alert-error"><?php foreach($errors as $e): ?><p>✗ <?= escape($e) ?></p><?php endforeach; ?></div>
<?php endif; ?>
<form method="POST" action="<?= url('utilisateurs/ajouter.php') ?>" id="formUtilisateur" novalidate class="form-card">
    <div class="form-row">
        <div class="form-group">
            <label for="prenom">Prénom <span class="required">*</span></label>
            <input type="text" id="prenom" name="prenom" class="form-control" value="<?= escape($_POST['prenom']??'') ?>">
            <span class="field-error" id="errorPrenom"></span>
        </div>
        <div class="form-group">
            <label for="nom">Nom <span class="required">*</span></label>
            <input type="text" id="nom" name="nom" class="form-control" value="<?= escape($_POST['nom']??'') ?>">
            <span class="field-error" id="errorNom"></span>
        </div>
    </div>
    <div class="form-group">
        <label for="login">Login <span class="required">*</span></label>
        <input type="text" id="login" name="login" class="form-control" value="<?= escape($_POST['login']??'') ?>" autocomplete="new-password">
        <span class="field-error" id="errorLogin"></span>
    </div>
    <div class="form-group">
        <label for="role">Rôle <span class="required">*</span></label>
        <select id="role" name="role" class="form-control">
            <option value="">— Choisir —</option>
            <option value="editeur" <?= ($_POST['role']??'')==='editeur'?'selected':'' ?>>Éditeur</option>
            <option value="administrateur" <?= ($_POST['role']??'')==='administrateur'?'selected':'' ?>>Administrateur</option>
        </select>
        <span class="field-error" id="errorRole"></span>
    </div>
    <div class="form-row">
        <div class="form-group">
            <label for="mot_de_passe">Mot de passe <span class="required">*</span></label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" autocomplete="new-password">
            <span class="field-error" id="errorMdp"></span>
        </div>
        <div class="form-group">
            <label for="mot_de_passe2">Confirmer <span class="required">*</span></label>
            <input type="password" id="mot_de_passe2" name="mot_de_passe2" class="form-control">
            <span class="field-error" id="errorMdp2"></span>
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">✅ Créer</button>
        <a href="<?= url('utilisateurs/liste.php') ?>" class="btn btn-secondary">Annuler</a>
    </div>
</form>
<script src="<?= url('assets/js/validation.js') ?>"></script>
<?php include __DIR__ . '/../pied.php'; ?>
