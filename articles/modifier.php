<?php
require_once __DIR__ . '/../config.php';
session_start();
requireEditor();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) redirect('articles/liste.php', 'Article introuvable.', 'error');

$pdo        = getDB();
$categories = $pdo->query("SELECT id, nom FROM categories ORDER BY nom")->fetchAll();
$stmt       = $pdo->prepare("SELECT * FROM articles WHERE id = :id");
$stmt->execute([':id' => $id]);
$article = $stmt->fetch();
if (!$article) redirect('articles/liste.php', 'Article introuvable.', 'error');

$pageTitle = 'Modifier l\'article';
$errors    = [];

function handleUpload(array $file, array &$errors): ?string {
    if (empty($file['name'])) return null;
    $allowed = ['image/jpeg','image/jpg','image/png','image/gif','image/webp'];
    if (!in_array($file['type'], $allowed)) { $errors[] = 'Format image non autorisé.'; return null; }
    if ($file['size'] > 2*1024*1024) { $errors[] = 'Image trop lourde (max 2 Mo).'; return null; }
    if (!is_dir(UPLOAD_DIR)) @mkdir(UPLOAD_DIR, 0777, true);
    if (!is_writable(UPLOAD_DIR)) { $errors[] = 'Dossier uploads non accessible. Ouvrez install.php.'; return null; }
    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $name = uniqid('img_', true) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $name)) { $errors[] = 'Échec upload.'; return null; }
    return $name;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre        = trim($_POST['titre'] ?? '');
    $description  = trim($_POST['description_courte'] ?? '');
    $contenu      = trim($_POST['contenu'] ?? '');
    $categorie_id = (int)($_POST['categorie_id'] ?? 0);
    $imageName    = $article['image'];

    if (strlen($titre) < 5)        $errors[] = 'Le titre doit contenir au moins 5 caractères.';
    if (strlen($description) < 10) $errors[] = 'La description doit contenir au moins 10 caractères.';
    if (strlen($contenu) < 20)     $errors[] = 'Le contenu doit contenir au moins 20 caractères.';
    if ($categorie_id <= 0)        $errors[] = 'Veuillez choisir une catégorie.';

    $newImg = handleUpload($_FILES['image'] ?? [], $errors);
    if ($newImg) {
        if ($imageName && file_exists(UPLOAD_DIR . $imageName)) unlink(UPLOAD_DIR . $imageName);
        $imageName = $newImg;
    }

    if (empty($errors)) {
        $pdo->prepare("UPDATE articles SET titre=:titre,description_courte=:desc,contenu=:contenu,categorie_id=:cat,image=:img WHERE id=:id")
            ->execute([':titre'=>$titre,':desc'=>$description,':contenu'=>$contenu,':cat'=>$categorie_id,':img'=>$imageName,':id'=>$id]);
        redirect('articles/liste.php', 'Article modifié avec succès !');
    }
    $article = array_merge($article, ['titre'=>$titre,'description_courte'=>$description,'contenu'=>$contenu,'categorie_id'=>$categorie_id]);
}

include __DIR__ . '/../entete.php';
include __DIR__ . '/../menu.php';
?>
<div class="page-header">
    <h1>✏️ Modifier l'article</h1>
    <a href="<?= url('articles/liste.php') ?>" class="btn btn-secondary">← Retour</a>
</div>
<?php if (!empty($errors)): ?>
    <div class="alert alert-error"><?php foreach($errors as $e): ?><p>✗ <?= escape($e) ?></p><?php endforeach; ?></div>
<?php endif; ?>
<form method="POST" enctype="multipart/form-data" id="formArticle" novalidate class="form-card">
    <input type="hidden" name="id" value="<?= $id ?>">
    <div class="form-group">
        <label for="titre">Titre <span class="required">*</span></label>
        <input type="text" id="titre" name="titre" value="<?= escape($article['titre']) ?>" class="form-control" maxlength="255">
        <span class="field-error" id="errorTitre"></span>
    </div>
    <div class="form-group">
        <label for="categorie_id">Catégorie <span class="required">*</span></label>
        <select id="categorie_id" name="categorie_id" class="form-control">
            <option value="">— Choisir —</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= $cat['id']==$article['categorie_id']?'selected':'' ?>><?= escape($cat['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <span class="field-error" id="errorCategorie"></span>
    </div>
    <div class="form-group">
        <label for="description_courte">Description courte <span class="required">*</span></label>
        <textarea id="description_courte" name="description_courte" class="form-control" rows="3"><?= escape($article['description_courte']) ?></textarea>
        <span class="field-error" id="errorDescription"></span>
    </div>
    <div class="form-group">
        <label for="contenu">Contenu <span class="required">*</span></label>
        <textarea id="contenu" name="contenu" class="form-control" rows="14"><?= escape($article['contenu']) ?></textarea>
        <span class="field-error" id="errorContenu"></span>
    </div>
    <div class="form-group">
        <label>Image de couverture</label>
        <?php if ($article['image']): ?>
            <div class="current-image-box">
                <img src="<?= escape(url('assets/uploads/'.$article['image'])) ?>" style="max-height:120px;border-radius:8px">
                <p style="font-size:12px;color:var(--gray-400);margin-top:6px">Image actuelle — choisissez-en une nouvelle pour la remplacer</p>
            </div>
        <?php endif; ?>
        <input type="file" id="image" name="image" class="form-control" accept="image/*">
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-lg">💾 Enregistrer les modifications</button>
        <a href="<?= url('articles/liste.php') ?>" class="btn btn-secondary">Annuler</a>
    </div>
</form>
<script src="<?= url('assets/js/validation.js') ?>"></script>
<?php include __DIR__ . '/../pied.php'; ?>
