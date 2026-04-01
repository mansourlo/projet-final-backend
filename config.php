<?php
// ============================================
// Configuration de la base de données
// ============================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'actualites_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8');

define('SITE_NAME', 'ActuDynamic');
define('ARTICLES_PAR_PAGE', 5);

// ── Détection automatique du chemin de base ──
// Fonctionne avec : php -S localhost:8080, XAMPP, Laragon, Apache...
(function () {
    $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME']);
    // Remonte jusqu'à la racine du projet (là où se trouve config.php)
    $configDir = str_replace('\\', '/', dirname(__DIR__ . '/config.php'));
    $docRoot   = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
    $base      = rtrim(str_replace($docRoot, '', $configDir), '/');
    // Pour php -S, DOCUMENT_ROOT pointe déjà sur le dossier du projet
    if ($base === '' || $docRoot === $configDir) {
        $base = '';
    }
    define('BASE_URL', $base);
})();

define('UPLOAD_DIR', __DIR__ . '/assets/uploads/');
define('UPLOAD_URL', BASE_URL . '/assets/uploads/');

// Raccourci pour construire une URL interne
function url(string $path): string {
    return BASE_URL . '/' . ltrim($path, '/');
}

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('<div style="padding:20px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:5px;font-family:sans-serif">
                 <strong>Erreur de connexion à la base de données.</strong><br>
                 Vérifiez DB_HOST, DB_NAME, DB_USER, DB_PASS dans config.php.<br>
                 Détail : ' . htmlspecialchars($e->getMessage()) . '
                 </div>');
        }
    }
    return $pdo;
}

function escape(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isEditor(): bool {
    return isset($_SESSION['role']) && in_array($_SESSION['role'], ['editeur', 'administrateur']);
}

function isAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'administrateur';
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        redirect('connexion.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    }
}

function requireEditor(): void {
    requireLogin();
    if (!isEditor()) {
        redirect('accueil.php?erreur=acces_refuse');
    }
}

function requireAdmin(): void {
    requireLogin();
    if (!isAdmin()) {
        redirect('accueil.php?erreur=acces_refuse');
    }
}

function redirect(string $path, string $message = '', string $type = 'success'): void {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type']    = $type;
    }
    $url = (str_starts_with($path, 'http') || str_starts_with($path, '/'))
        ? $path
        : url($path);
    header('Location: ' . $url);
    exit;
}

function flashMessage(): string {
    if (isset($_SESSION['flash_message'])) {
        $msg  = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'success';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        $icon = $type === 'success' ? '✓' : '✗';
        return "<div class='alert alert-{$type}'>{$icon} " . escape($msg) . "</div>";
    }
    return '';
}
