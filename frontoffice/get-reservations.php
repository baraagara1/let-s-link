<?php
$pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_GET['id_cov'])) {
    exit("ID covoiturage manquant.");
}

$id_cov = intval($_GET['id_cov']);

// Récupération de la date du covoiturage
$stmtDate = $pdo->prepare("SELECT date FROM covoiturage WHERE id_cov = ?");
$stmtDate->execute([$id_cov]);
$dateCov = $stmtDate->fetchColumn();

if (!$dateCov) {
    exit("Date de covoiturage introuvable.");
}

$dateCovObj = new DateTime($dateCov . ' 00:00:00');
$now = new DateTime();
$diffHours = ($dateCovObj->getTimestamp() - $now->getTimestamp()) / 3600;

// Récupération des réservations
$stmt = $pdo->prepare("SELECT id_res, utilisateur_id, nb_place_res, moyen_paiement, status FROM reservations WHERE covoiturage_id = ?");
$stmt->execute([$id_cov]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($reservations)) {
    echo '<div class="alert alert-warning text-center">Aucune réservation pour ce covoiturage.</div>';
    exit;
}
?>

<?php foreach ($reservations as $r): ?>
<div class="d-flex justify-content-between align-items-center border rounded p-3 mb-2 shadow-sm bg-white">
  <div>
    <i class="fas fa-user me-1 text-dark"></i>
    Utilisateur : <strong><?= htmlspecialchars($r['utilisateur_id']) ?></strong> - 
    <span><?= htmlspecialchars($r['nb_place_res']) ?> place(s)</span> - 
    <span><?= htmlspecialchars($r['moyen_paiement']) ?></span>
  </div>
  <div class="d-flex align-items-center">
    <span class="badge bg-info text-dark me-2"><?= htmlspecialchars($r['status']) ?></span>

    <?php if ($diffHours >= 24): ?>
      <!-- Supprimer normalement -->
      <a href="supprimer-reservation.php?id=<?= intval($r['id_res']) ?>" class="btn btn-sm btn-outline-danger" title="Supprimer" onclick="return confirm('Supprimer cette réservation ?');">
        <i class="fas fa-trash-alt"></i>
      </a>
    <?php else: ?>
      <!-- Bouton Notifier Admin -->
      <form method="POST" action="notifier-admin.php" class="d-inline">
        <input type="hidden" name="reservation_id" value="<?= intval($r['id_res']) ?>">
        <input type="hidden" name="utilisateur_id" value="<?= intval($r['utilisateur_id']) ?>">
        <button type="submit" class="btn btn-sm btn-outline-danger" title="Notifier Admin">
          <i class="fas fa-bell"></i> Notifier Admin
        </button>
      </form>
    <?php endif; ?>
  </div>
</div>
<?php endforeach; ?>
