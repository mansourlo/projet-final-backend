<?php
require_once __DIR__ . '/config.php';
session_start();

$pageTitle   = 'Accueil';
$pdo         = getDB();
$categorieId = isset($_GET['categorie']) ? (int)$_GET['categorie'] : 0;
$recherche   = trim($_GET['q'] ?? '');
$page        = max(1, (int)($_GET['page'] ?? 1));
$limite      = ARTICLES_PAR_PAGE;
$offset      = ($page - 1) * $limite;

$whereClause = '1=1';
$params      = [];
if ($categorieId > 0) {
    $whereClause .= ' AND a.categorie_id = :cat_id';
    $params[':cat_id'] = $categorieId;
}
if ($recherche !== '') {
    $whereClause .= ' AND (a.titre LIKE :q OR a.description_courte LIKE :q2 OR a.contenu LIKE :q3)';
    $params[':q']  = "%$recherche%";
    $params[':q2'] = "%$recherche%";
    $params[':q3'] = "%$recherche%";
}

$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM articles a WHERE $whereClause");
$stmtCount->execute($params);
$total      = (int)$stmtCount->fetchColumn();
$totalPages = max(1, ceil($total / $limite));

$stmtArticles = $pdo->prepare("
    SELECT a.id, a.titre, a.description_courte, a.date_publication, a.image,
           c.nom AS categorie, c.id AS categorie_id,
           u.nom AS auteur_nom, u.prenom AS auteur_prenom
    FROM articles a
    JOIN categories c ON a.categorie_id = c.id
    JOIN utilisateurs u ON a.auteur_id   = u.id
    WHERE $whereClause
    ORDER BY a.date_publication DESC
    LIMIT :limite OFFSET :offset
");
foreach ($params as $k => $v) $stmtArticles->bindValue($k, $v);
$stmtArticles->bindValue(':limite',  $limite,  PDO::PARAM_INT);
$stmtArticles->bindValue(':offset',  $offset,  PDO::PARAM_INT);
$stmtArticles->execute();
$articles = $stmtArticles->fetchAll();

$categorieNom = '';
if ($categorieId > 0) {
    $s = $pdo->prepare("SELECT nom FROM categories WHERE id = :id");
    $s->execute([':id' => $categorieId]);
    $categorieNom = $s->fetchColumn();
}

function buildUrl(array $extra): string {
    $q = array_filter(array_merge($_GET, $extra), fn($v) => $v !== null && $v !== '');
    return url('accueil.php') . ($q ? '?' . http_build_query($q) : '');
}

include __DIR__ . '/entete.php';
include __DIR__ . '/menu.php';
?>

<div class="page-header">
    <?php if ($categorieNom): ?>
        <div><h1>📂 <?= escape($categorieNom) ?></h1><p><a href="<?= url('accueil.php') ?>">← Toutes les catégories</a></p></div>
    <?php elseif ($recherche): ?>
        <div><h1>🔍 Résultats pour "<?= escape($recherche) ?>"</h1><p><?= $total ?> article(s) trouvé(s)</p></div>
    <?php else: ?>
        <div><h1>Dernières actualités</h1><p>Restez informé des dernières nouvelles</p></div>
    <?php endif; ?>
</div>

<form class="search-bar" action="<?= url('accueil.php') ?>" method="GET" id="searchForm">
    <?php if ($categorieId): ?><input type="hidden" name="categorie" value="<?= $categorieId ?>"><?php endif; ?>
    <input type="text" name="q" value="<?= escape($recherche) ?>" placeholder="Rechercher un article..." class="search-input" id="searchInput">
    <button type="submit" class="btn btn-primary">🔍 Rechercher</button>
    <?php if ($recherche): ?>
        <a href="<?= buildUrl(['q'=>null,'page'=>null]) ?>" class="btn btn-secondary">✕ Effacer</a>
    <?php endif; ?>
</form>

<?php if (empty($articles)): ?>
    <div class="empty-state">
        <p>📭 Aucun article trouvé.</p>
        <?php if ($recherche || $categorieId): ?>
            <a href="<?= url('accueil.php') ?>" class="btn btn-secondary">Voir tous les articles</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="articles-grid">
        <?php foreach ($articles as $article): ?>
        <article class="article-card">
            <?php if ($article['image']): ?>
                <div class="article-image"><img src="<?= escape(url('assets/uploads/' . $article['image'])) ?>" alt="<?= escape($article['titre']) ?>"></div>
            <?php else: ?>
                <div class="article-image article-image--placeholder"><span>📰</span></div>
            <?php endif; ?>
            <div class="article-body">
                <span class="article-category"><?= escape($article['categorie']) ?></span>
                <h2 class="article-title">
                    <a href="<?= url('articles/detail.php') ?>?id=<?= $article['id'] ?>"><?= escape($article['titre']) ?></a>
                </h2>
                <p class="article-excerpt"><?= escape($article['description_courte']) ?></p>
                <div class="article-meta">
                    <span>👤 <?= escape($article['auteur_prenom'] . ' ' . $article['auteur_nom']) ?></span>
                    <span>📅 <?= date('d/m/Y', strtotime($article['date_publication'])) ?></span>
                </div>
                <a href="<?= url('articles/detail.php') ?>?id=<?= $article['id'] ?>" class="btn btn-outline">Lire la suite →</a>
            </div>
        </article>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="<?= buildUrl(['page'=>$page-1]) ?>" class="btn btn-secondary">← Précédent</a>
        <?php else: ?>
            <span class="btn btn-secondary disabled">← Précédent</span>
        <?php endif; ?>
        <div class="pagination-pages">
            <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++): ?>
                <a href="<?= buildUrl(['page'=>$i]) ?>" class="btn <?= $i===$page?'btn-primary':'btn-outline' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </div>
        <?php if ($page < $totalPages): ?>
            <a href="<?= buildUrl(['page'=>$page+1]) ?>" class="btn btn-secondary">Suivant →</a>
        <?php else: ?>
            <span class="btn btn-secondary disabled">Suivant →</span>
        <?php endif; ?>
    </div>
    <p class="pagination-info">Page <?= $page ?> sur <?= $totalPages ?> — <?= $total ?> article(s)</p>
    <?php endif; ?>
<?php endif; ?>

<?php include __DIR__ . '/pied.php'; ?>
