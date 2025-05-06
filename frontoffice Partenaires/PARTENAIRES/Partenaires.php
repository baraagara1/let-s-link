<?php
// PARTENAIRES/Partenaires.php

// 0) Charger l’autoloader de Composer
$autoload = realpath(__DIR__ . '/../vendor/autoload.php');
if (! $autoload) {
    die("❌ Impossible de trouver vendor/autoload.php. Avez-vous fait 'composer install' ?\n");
}
require_once $autoload;

// 1) Importer la classe Client de Twilio
use Twilio\Rest\Client;

// 2) Vos constantes Twilio
const TWILIO_SID   = 'AC637027dcd605e7150c5d1c58a3edc95a';
const TWILIO_TOKEN = 'e82ff711a6fa2a36fb51e3431e331c10';
const TWILIO_FROM  = '+18564167092';  // Votre numéro Twilio validé

// 3) Connexion PDO
try {
    $pdo = new PDO('mysql:host=localhost;dbname=lets_link;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Échec de connexion : " . $e->getMessage());
}

// 1a) Purge automatique des flash deals expirés
$pdo->exec("DELETE FROM offres WHERE is_flash = 1 AND end_time < NOW()");

// 2) Suppression d'une offre
if (isset($_GET['supprimer_offre'])) {
    $idO = (int)$_GET['supprimer_offre'];
    $pdo->exec("DELETE FROM offres WHERE idOffre = $idO");
    header('Location: Partenaires.php');
    exit;
}

// 3) Modification d'une offre et envoi SMS si Flash Deal activé
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_offre'])) {
    // a) Mise à jour de l’offre
    $stmt = $pdo->prepare("
        UPDATE offres SET
            typeOffre        = :typeOffre,
            descriptionOffre = :descriptionOffre,
            discount         = :discount,
            dateDebut        = :dateDebut,
            dateFin          = :dateFin,
            is_flash         = :is_flash,
            start_time       = :flash_start,
            end_time         = :flash_end
        WHERE idOffre = :idOffre
    ");
    $isFlash = isset($_POST['is_flash']) ? 1 : 0;
    $stmt->execute([
        ':typeOffre'        => $_POST['typeOffre'],
        ':descriptionOffre' => $_POST['descriptionOffre'],
        ':discount'         => $_POST['discount'],
        ':dateDebut'        => $_POST['dateDebut'],
        ':dateFin'          => $_POST['dateFin'],
        ':is_flash'         => $isFlash,
        ':flash_start'      => $_POST['flash_start'] ?: null,
        ':flash_end'        => $_POST['flash_end']   ?: null,
        ':idOffre'          => (int)$_POST['idOffre'],
    ]);

    // b) Si Flash Deal coché, envoi d’un SMS
    if ($isFlash) {
        // Récupérer le nom du partenaire
        $stmtP = $pdo->prepare("
            SELECT p.nomP
              FROM partenaire p
              JOIN offres o ON o.idP = p.idP
             WHERE o.idOffre = ?
        ");
        $stmtP->execute([(int)$_POST['idOffre']]);
        $nomP = $stmtP->fetchColumn() ?: 'Notre partenaire';

        // Message à envoyer
        $body = sprintf(
            "🔥 FLASH DEAL ! %s — %s à %s%% de réduction. Profitez-en vite !",
            $nomP,
            $_POST['typeOffre'],
            $_POST['discount']
        );

        // Instancier Twilio
        $twilio = new Client(TWILIO_SID, TWILIO_TOKEN);

        // Récupérer tous les numéros utilisateurs
        $toList = $pdo
            ->query("SELECT telephone FROM usser WHERE telephone IS NOT NULL")
            ->fetchAll(PDO::FETCH_COLUMN);

        foreach ($toList as $to) {
            try {
                $twilio->messages->create($to, [
                    'from' => TWILIO_FROM,
                    'body' => $body
                ]);
            } catch (Exception $e) {
                error_log("❌ Erreur SMS à $to : " . $e->getMessage());
            }
        }

        // Marquer l'offre comme notifiée
        $pdo->prepare("UPDATE offres SET sms_notified = 1 WHERE idOffre = ?")
            ->execute([(int)$_POST['idOffre']]);
    }

    // c) Redirection
    header('Location: Partenaires.php');
    exit;
}

/// 4) Chargement des partenaires + offres
$partenaires = [];
$stmt = $pdo->query(
    "SELECT p.idP, p.nomP, p.photoP, p.descriptionP,
            o.idOffre, o.typeOffre, o.descriptionOffre, o.discount, o.dateDebut, o.dateFin,
            o.is_flash, o.start_time, o.end_time
       FROM partenaire p
  LEFT JOIN offres o ON p.idP = o.idP
   ORDER BY p.nomP, o.idOffre"
);

while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $r['idP'];
    if (!isset($partenaires[$id])) {
        $partenaires[$id] = [
            'nom'         => $r['nomP'],
            'photo'       => $r['photoP'],
            'description' => $r['descriptionP'],
            'offres'      => []
        ];
    }
    if ($r['idOffre']) {
        $partenaires[$id]['offres'][] = [
            'id'          => $r['idOffre'],
            'type'        => $r['typeOffre'],
            'desc'        => $r['descriptionOffre'],
            'discount'    => $r['discount'],
            'start'       => $r['dateDebut'],
            'end'         => $r['dateFin'],
            'is_flash'    => $r['is_flash'],
            'flash_start' => $r['start_time'],
            'flash_end'   => $r['end_time'],
        ];
    }
}

$avecOffres = array_filter(
    $partenaires,
    fn($p) => !empty($p['offres'])
);

$sansOffres = array_filter(
    $partenaires,
    fn($p) => empty($p['offres'])
);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Partenaires & Offres — Let’s Link</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Favicon & Google Fonts -->
  <link href="../img/favicon.ico" rel="icon">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">

  <!-- Icon Font Stylesheet -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Libraries Stylesheet -->
  <link href="../lib/animate/animate.min.css" rel="stylesheet">
  <link href="../lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
  <link href="../lib/lightbox/css/lightbox.min.css" rel="stylesheet">

  <!-- Customized Bootstrap & Template CSS -->
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">

  <!-- Inline overrides for Flash Deal -->
  <style>
    .offer-box.flash-soon { background: #fff3cd; border-color: #ffeeba; }
    .badge-soon { background: #ffc107; color: #212529; font-weight: bold; }
    .badge-expired { background: #6c757d; }
  </style>
</head>
<body>
  <!-- Spinner -->
  <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
  </div>

  <!-- Topbar -->
  <div class="container-fluid bg-dark text-light px-0 py-2">
    <div class="row gx-0 d-none d-lg-flex">
      <div class="col-lg-7 px-5 text-start">
        <div class="h-100 d-inline-flex align-items-center me-4">
          <span class="fa fa-phone-alt me-2"></span>+216 20 123 456
        </div>
        <div class="h-100 d-inline-flex align-items-center">
          <span class="far fa-envelope me-2"></span>letslink@gmail.com
        </div>
      </div>
      <div class="col-lg-5 px-5 text-end">
        <div class="h-100 d-inline-flex align-items-center mx-n2">
          <a class="btn btn-link text-light" href="#"><i class="fab fa-facebook-f"></i></a>
          <a class="btn btn-link text-light" href="#"><i class="fab fa-twitter"></i></a>
          <a class="btn btn-link text-light" href="#"><i class="fab fa-linkedin-in"></i></a>
          <a class="btn btn-link text-light" href="#"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
    </div>
  </div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0">
    <div class="container">
      <a href="../index.php" class="navbar-brand px-4 px-lg-5">
        <img src="../logo.png" alt="Logo" style="width:140px;">
      </a>
      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
          <a href="../index.php" class="nav-item nav-link">Accueil</a>
          <a href="../about.html" class="nav-item nav-link">À propos</a>
          <a href="../service.html" class="nav-item nav-link">Conseils</a>
          <a href="../View/frontoffice/acceuil.php" class="nav-item nav-link">Recettes</a>

          <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Boutique</a>
            <div class="dropdown-menu bg-light m-0">
              <a href="../feature.html" class="dropdown-item">Produits Alimentaires</a>
              <a href="../quote.html" class="dropdown-item">Box Nourriture</a>
              <a href="../team.html" class="dropdown-item">Produit En Gros</a>
            </div>
          </div>

          <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle active" id="partenairesDropdown" data-bs-toggle="dropdown">Partenaires</a>
            <ul class="dropdown-menu bg-light m-0" aria-labelledby="partenairesDropdown">
              <li><a href="Partenaires.php" class="dropdown-item active">Partenaires & Offres</a></li>
              <li><a href="demandes.php" class="dropdown-item">Demandes</a></li>
            </ul>
          </div>
        </div>
        <a href="../login.html" class="btn btn-primary py-4 px-lg-4 rounded-0 d-none d-lg-block">Se connecter <i class="fa fa-arrow-right ms-3"></i></a>
      </div>
    </div>
  </nav>

  <!-- Partenaires & Offres -->
  <div class="container-xxl py-5">
    <div class="container">
      <h1 class="display-5 text-center mb-5">Nos Partenaires & Offres</h1>
      <div class="row g-4">
        <?php foreach ($avecOffres as $p): ?>
        <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
          <div class="service-item rounded d-flex h-100">
            <div class="service-img rounded">
              <img src="<?=htmlspecialchars($p['photo'])?>" alt="<?=htmlspecialchars($p['nom'])?>" class="img-fluid">
            </div>
            <div class="service-text rounded p-4">
              <h5 class="mb-3"><?=htmlspecialchars($p['nom'])?></h5>
              <p><?=htmlspecialchars($p['description'])?></p>
              <?php foreach ($p['offres'] as $of): ?>
              <div class="offer-box mt-3 p-3 bg-light rounded">
                <strong><?=htmlspecialchars($of['type'])?> :</strong> <?=htmlspecialchars($of['desc'])?> — <b><?=htmlspecialchars($of['discount'])?>%</b><br>
                <small>du <?=htmlspecialchars($of['start'])?> au <?=htmlspecialchars($of['end'])?></small>
                <?php if ($of['is_flash']): ?>
                <span class="badge bg-danger position-absolute top-0 end-0 m-2">Flash Deal</span>
                <div class="mt-2"><span id="cd-<?= $of['id'] ?>"></span></div>
                <?php endif; ?>
                <div class="mt-2 text-end">
                  <button class="btn btn-sm btn-warning" data-bs-toggle="collapse" data-bs-target="#edit<?= $of['id'] ?>"><i class="fas fa-edit"></i></button>
                  <a href="?supprimer_offre=<?= $of['id'] ?>" onclick="return confirm('Supprimer ?')" class="btn btn-sm btn-danger"><i class="fas fa-trash-alt"></i></a>
                </div>
                <div class="collapse mt-3" id="edit<?= $of['id'] ?>">
                  <form method="POST" class="row g-2">
                    <input type="hidden" name="idOffre" value="<?= $of['id'] ?>">
                    <div class="col-6"><input name="typeOffre" class="form-control" value="<?=htmlspecialchars($of['type'])?>" placeholder="Type" required></div>
                    <div class="col-6"><input name="descriptionOffre" class="form-control" value="<?=htmlspecialchars($of['desc'])?>" placeholder="Description" required></div>
                    <div class="col-4"><input name="discount" class="form-control" value="<?=htmlspecialchars($of['discount'])?>" placeholder="Réduc (%)" required></div>
                    <div class="col-4"><input name="dateDebut" type="date" class="form-control" value="<?= $of['start'] ?>" required></div>
                    <div class="col-4"><input name="dateFin" type="date" class="form-control" value="<?= $of['end'] ?>" required></div>
                    <div class="col-12 position-relative">
                      <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_flash" id="flash_<?= $of['id'] ?>" <?= $of['is_flash'] ? 'checked' : '' ?> >
                        <label class="form-check-label" for="flash_<?= $of['id'] ?>">Flash Deal actif</label>
                      </div>
                      <div class="row flash-fields <?= $of['is_flash'] ? '' : 'd-none' ?>">
                        <div class="col-6"><input name="flash_start" type="datetime-local" class="form-control my-2" value="<?= htmlspecialchars($of['flash_start']) ?>"></div>
                        <div class="col-6"><input name="flash_end" type="datetime-local" class="form-control my-2" value="<?= htmlspecialchars($of['flash_end']) ?>"></div>
                      </div>
                      <button name="modifier_offre" class="btn btn-sm btn-success position-absolute end-0">Enregistrer</button>
                    </div>
                  </form>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <h2 class="display-6 text-center mb-4 mt-5">Autres Partenaires</h2>
        <?php foreach ($sansOffres as $p): ?>
        <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
          <div class="service-item rounded d-flex h-100">
            <div class="service-img rounded opacity-50"><img src="<?=htmlspecialchars($p['photo'])?>" alt="<?=htmlspecialchars($p['nom'])?>" class="img-fluid"></div>
            <div class="service-text rounded p-4"><h5 class="mb-3"><?=htmlspecialchars($p['nom'])?></h5><p><?=htmlspecialchars($p['description'])?></p><span class="text-muted fst-italic">Aucune offre pour le moment.</span></div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <!-- Footer -->
  <div class="container-fluid bg-dark text-light footer pt-5">…</div>
  <!-- Back to Top & JS -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="../lib/wow/wow.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../lib/easing/easing.min.js"></script>
  <script src="../lib/waypoints/waypoints.min.js"></script>
  <script src="../lib/owlcarousel/owl.carousel.min.js"></script>
  <script src="../lib/counterup/counterup.min.js"></script>
  <script src="../lib/parallax/parallax.min.js"></script>
  <script src="../lib/isotope/isotope.pkgd.min.js"></script>
  <script src="../lib/lightbox/js/lightbox.min.js"></script>
  <script src="../js/main.js"></script>
  <script>
    document.querySelectorAll('input[name="is_flash"]').forEach(cb => {
      cb.addEventListener('change', function() {
        this.closest('.collapse').querySelectorAll('.flash-fields').forEach(el => el.classList.toggle('d-none', !this.checked));
      });
    });
  </script>
  <script>
// Timer dynamique pour Flash Deal
document.addEventListener('DOMContentLoaded', function() {
  <?php foreach ($avecOffres as $p): foreach ($p['offres'] as $of): if ($of['is_flash']): ?>
  (function(){
    const start = new Date('<?= $of['flash_start'] ?>');
    const end   = new Date('<?= $of['flash_end'] ?>');
    const el    = document.getElementById('cd-<?= $of['id'] ?>');
    if (!el) return;
    const timer = setInterval(() => {
      const now = new Date();
      let diff, h, m, s, text;
      if (now < start) {
        // Avant le début
        diff = start - now;
        h = Math.floor(diff/3600000);
        m = Math.floor((diff%3600000)/60000);
        s = Math.floor((diff%60000)/1000);
        text = `Va commencer dans : ${h}h ${m}m ${s}s`;
      } else if (now <= end) {
        // En cours
        diff = end - now;
        h = Math.floor(diff/3600000);
        m = Math.floor((diff%3600000)/60000);
        s = Math.floor((diff%60000)/1000);
        text = `Expire dans : ${h}h ${m}m ${s}s`;
        // Sablier si < 1h
        if (diff <= 3600000) text = `⏳ ${text}`;
      } else {
        // Expiré
        el.innerText = 'Expiré';
        clearInterval(timer);
        return;
      }
      el.innerText = text;
    }, 1000);
  })();
  <?php endif; endforeach; endforeach; ?>
});
</script>
</body>
</html>
