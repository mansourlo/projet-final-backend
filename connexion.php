<?php
require_once __DIR__ . '/config.php';
session_start();
if (isLoggedIn()) redirect('accueil.php');

$pageTitle = 'Connexion';
$errors    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $mdp   = $_POST['mot_de_passe'] ?? '';
    if (empty($login)) $errors[] = 'Le login est obligatoire.';
    if (empty($mdp))   $errors[] = 'Le mot de passe est obligatoire.';

    if (empty($errors)) {
        $pdo  = getDB();
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE login = :login LIMIT 1");
        $stmt->execute([':login' => $login]);
        $user = $stmt->fetch();
        if ($user && password_verify($mdp, $user['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['login']   = $user['login'];
            $_SESSION['prenom']  = $user['prenom'];
            $_SESSION['nom']     = $user['nom'];
            $_SESSION['role']    = $user['role'];
            $redirect = $_GET['redirect'] ?? url('accueil.php');
            redirect($redirect, 'Bienvenue, ' . $user['prenom'] . ' !');
        } else {
            $errors[] = 'Login ou mot de passe incorrect.';
        }
    }
}

include __DIR__ . '/entete.php';
include __DIR__ . '/menu.php';
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo-wrap">📰</div>
            <h1>Connexion</h1>
            <p>Accédez à votre espace de gestion</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <?php foreach ($errors as $e): ?><p>✗ <?= escape($e) ?></p><?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= url('connexion.php') ?><?= isset($_GET['redirect']) ? '?redirect='.urlencode($_GET['redirect']) : '' ?>"
              method="POST" id="formConnexion" novalidate>
            <div class="form-group">
                <label for="login">Identifiant</label>
                <input type="text" id="login" name="login"
                       value="<?= escape($_POST['login'] ?? '') ?>"
                       placeholder="Votre login" class="form-control" autocomplete="username">
                <span class="field-error" id="errorLogin"></span>
            </div>
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe</label>
                <div class="input-group">
                    <input type="password" id="mot_de_passe" name="mot_de_passe"
                           placeholder="••••••••" class="form-control" autocomplete="current-password">
                    <button type="button" class="toggle-pwd" onclick="togglePassword('mot_de_passe', this)">👁</button>
                </div>
                <span class="field-error" id="errorMdp"></span>
            </div>
            <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:8px">
                Se connecter →
            </button>
        </form>

        <div class="auth-footer">
            <p><strong>Comptes de démonstration :</strong></p>
            <p>Admin : <code>admin</code> / <code>password</code></p>
            <p>Éditeur : <code>editeur1</code> / <code>password</code></p>
        </div>
    </div>
</div>

<script src="<?= url('assets/js/validation.js') ?>"></script>
<?php include __DIR__ . '/pied.php'; ?>
