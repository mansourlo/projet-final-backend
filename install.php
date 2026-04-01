<?php
// install.php — À exécuter UNE FOIS après installation
// Ouvrir : http://localhost/projet/install.php

$uploadDir = __DIR__ . '/assets/uploads/';
$ok = true;

echo '<style>body{font-family:sans-serif;max-width:600px;margin:40px auto;padding:20px}
.ok{color:#16a34a;background:#dcfce7;padding:8px 14px;border-radius:6px;margin:6px 0}
.err{color:#991b1b;background:#fee2e2;padding:8px 14px;border-radius:6px;margin:6px 0}
h1{color:#1e293b}pre{background:#f1f5f9;padding:12px;border-radius:6px;font-size:13px}
</style>';
echo '<h1>🔧 Installation ActuDynamic</h1>';

// Créer le dossier uploads s'il n'existe pas
if (!is_dir($uploadDir)) {
    if (mkdir($uploadDir, 0777, true)) {
        echo '<p class="ok">✓ Dossier assets/uploads/ créé.</p>';
    } else {
        echo '<p class="err">✗ Impossible de créer assets/uploads/ — créez-le manuellement.</p>';
        $ok = false;
    }
} else {
    echo '<p class="ok">✓ Dossier assets/uploads/ existe.</p>';
}

// Fixer les permissions
if (is_dir($uploadDir)) {
    if (chmod($uploadDir, 0777)) {
        echo '<p class="ok">✓ Permissions 0777 appliquées sur assets/uploads/.</p>';
    } else {
        echo '<p class="err">✗ Impossible de changer les permissions — exécutez manuellement :<br>
        <pre>chmod 777 assets/uploads/</pre>
        Ou sous XAMPP Linux :<pre>sudo chmod 777 /opt/lampp/htdocs/votre_dossier/projet/assets/uploads/</pre></p>';
        $ok = false;
    }
}

// Test d'écriture
$testFile = $uploadDir . 'test_write_' . time() . '.tmp';
if (file_put_contents($testFile, 'test')) {
    unlink($testFile);
    echo '<p class="ok">✓ Écriture dans uploads/ fonctionnelle.</p>';
} else {
    echo '<p class="err">✗ Impossible d\'écrire dans uploads/ — vérifiez les permissions.</p>';
    $ok = false;
}

// Test connexion BDD
echo '<hr>';
require_once __DIR__ . '/config.php';
try {
    $pdo = getDB();
    $count = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
    echo '<p class="ok">✓ Base de données connectée — ' . $count . ' article(s).</p>';
} catch (Exception $e) {
    echo '<p class="err">✗ Erreur BDD : ' . htmlspecialchars($e->getMessage()) . '</p>';
    $ok = false;
}

echo '<hr>';
if ($ok) {
    echo '<p class="ok" style="font-size:16px;font-weight:bold">✅ Tout est OK ! 
    <a href="' . url('accueil.php') . '">→ Aller sur le site</a></p>';
} else {
    echo '<p class="err" style="font-size:15px">⚠️ Des problèmes ont été détectés. Corrigez-les puis rechargez cette page.</p>';
}

echo '<p style="color:#64748b;font-size:12px;margin-top:20px">Supprimez ce fichier install.php après installation.</p>';
