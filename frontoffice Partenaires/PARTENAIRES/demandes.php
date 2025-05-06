<?php
// PARTENAIRES/demandes.php

// 1) Connexion PDO
try {
    $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Échec de connexion : " . $e->getMessage());
}

$error = "";

// 2) Envoi d'une nouvelle demande (vérifie que le partenaire existe)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['envoyer_demande'])) {
    $nomRaw = trim($_POST['nom']);
    $stmtP  = $pdo->prepare("SELECT idP FROM partenaire WHERE nomP = ?");
    $stmtP->execute([$nomRaw]);
    if ($stmtP->rowCount() === 0) {
        $error = "Le partenaire « " . htmlspecialchars($nomRaw) . " » n’existe pas.";
    } else {
        $nom   = $pdo->quote($nomRaw);
        $type  = $pdo->quote($_POST['typeDemande']);
        $offre = $pdo->quote($_POST['offreDemande']);
        $pdo->exec("
          INSERT INTO demandes (nom,type,offre,statut)
          VALUES ($nom,$type,$offre,'En attente')
        ");
        header("Location: demandes.php");
        exit;
    }
}

// 3) Transformer une demande Acceptée en offre (avec Flash Deal)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_offre'])) {
    // Validation des champs obligatoires
    if (empty($_POST['typeOffre']) || empty($_POST['descriptionOffre'])
        || empty($_POST['discount']) || empty($_POST['dateDebut'])
        || empty($_POST['dateFin'])
    ) {
        $error = "Tous les champs de l’offre doivent être remplis.";
    } elseif (!empty($_POST['is_flash'])
              && (empty($_POST['flash_start']) || empty($_POST['flash_end']))
    ) {
        $error = "Pour un Flash Deal, les dates de début et de fin sont obligatoires.";
    } else {
        $idD   = (int)$_POST['idDemande'];
        $type  = $pdo->quote($_POST['typeOffre']);
        $desc  = $pdo->quote($_POST['descriptionOffre']);
        $disc  = $pdo->quote($_POST['discount']);
        $sd    = $pdo->quote($_POST['dateDebut']);
        $ed    = $pdo->quote($_POST['dateFin']);

        // Récupération des champs Flash Deal
        $isFlash = isset($_POST['is_flash']) ? 1 : 0;
        $fs      = $isFlash ? $pdo->quote($_POST['flash_start']) : 'NULL';
        $fe      = $isFlash ? $pdo->quote($_POST['flash_end'])   : 'NULL';

        // Recherche de l'idP pour la demande acceptée
        $stmt = $pdo->prepare("
          SELECT p.idP
          FROM demandes d
          JOIN partenaire p ON p.nomP = d.nom
          WHERE d.idDemande = ? AND d.statut = 'Acceptée'
        ");
        $stmt->execute([$idD]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $idP = (int)$row['idP'];
            // Insertion dans offres avec colonnes Flash Deal
            $pdo->exec("
              INSERT INTO offres
                (idP,typeOffre,descriptionOffre,discount,dateDebut,dateFin,is_flash,start_time,end_time)
              VALUES
                ($idP,$type,$desc,$disc,$sd,$ed,$isFlash,$fs,$fe)
            ");
            // Suppression de la demande
            $pdo->exec("DELETE FROM demandes WHERE idDemande = $idD");
        }
        header("Location: demandes.php");
        exit;
    }
}

// 4) Chargement des demandes
$demandes = $pdo
    ->query("SELECT * FROM demandes ORDER BY idDemande DESC")
    ->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Demandes d’offres — Let’s Link</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- CSS / Fonts -->
  <link href="../css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="../lib/animate/animate.min.css" rel="stylesheet">
  <link href="../css/style.css" rel="stylesheet">
</head>
<body>
  <!-- Topbar -->
  <div class="container-fluid bg-dark text-light px-0 py-2">
    <div class="row gx-0 d-none d-lg-flex">
      <div class="col-lg-7 px-5 d-flex align-items-center">
        <i class="fa fa-phone-alt me-2"></i>+216 20 123 456
        <span class="ms-4"><i class="far fa-envelope me-2"></i>letslink@gmail.com</span>
      </div>
      <div class="col-lg-5 px-5 text-end">
        <a class="btn btn-link text-light" href="#"><i class="fab fa-facebook-f"></i></a>
        <a class="btn btn-link text-light" href="#"><i class="fab fa-twitter"></i></a>
        <a class="btn btn-link text-light" href="#"><i class="fab fa-linkedin-in"></i></a>
        <a class="btn btn-link text-light" href="#"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top py-3">
    <div class="container">
      <a href="../index.php" class="navbar-brand"><img src="../logo.png" height="50" alt="Logo"></a>
      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="nav">
        <div class="navbar-nav ms-auto">
          <a href="../index.php" class="nav-link">Accueil</a>
          <a href="../about.html" class="nav-link">À propos</a>
          <a href="../service.html" class="nav-link">Conseils</a>
          <a href="../View/frontoffice/acceuil.php" class="nav-link">Recettes</a>
          <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Boutique</a>
            <ul class="dropdown-menu bg-light m-0">
              <li><a class="dropdown-item" href="../feature.html">Produits Alimentaires</a></li>
              <li><a class="dropdown-item" href="../quote.html">Box Nourriture</a></li>
              <li><a class="dropdown-item" href="../team.html">Produit En Gros</a></li>
            </ul>
          </div>
          <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle active" data-bs-toggle="dropdown">Partenaires</a>
            <ul class="dropdown-menu bg-light m-0">
              <li><a class="dropdown-item" href="Partenaires.php">Partenaires & Offres</a></li>
              <li><a class="dropdown-item active" href="demandes.php">Demandes</a></li>
            </ul>
          </div>
        </div>
        <a href="../login.html" class="btn btn-primary ms-3">Se connecter</a>
      </div>
    </div>
  </nav>

  <!-- Contenu -->
  <div class="container-xxl py-5">
    <div class="container">
      <h1 class="display-5 text-center mb-5">Demandes d’offres</h1>

      <!-- Affichage d'erreur -->
      <?php if ($error): ?>
        <div class="alert alert-danger animate__animated animate__fadeInUp"><?= $error ?></div>
      <?php endif; ?>

      <!-- Formulaire d'envoi -->
      <div class="row justify-content-center mb-5">
        <div class="col-lg-8">
          <div class="bg-white p-4 rounded shadow animate__animated animate__fadeInUp">
            <form method="POST" class="row g-3">
              <div class="col-md-4">
                <input name="nom" type="text" class="form-control" placeholder="Nom du partenaire">
              </div>
              <div class="col-md-4">
                <input name="typeDemande" type="text" class="form-control" placeholder="Type de demande">
              </div>
              <div class="col-md-4">
                <input name="offreDemande" type="text" class="form-control" placeholder="Offre souhaitée">
              </div>
              <div class="col-12 text-end">
                <button name="envoyer_demande" class="btn btn-primary">Envoyer</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Tableau des demandes -->
      <div class="table-responsive bg-light p-3 rounded shadow">
        <table class="table table-bordered text-center mb-0">
          <thead class="table-white">
            <tr><th>Nom</th><th>Type</th><th>Demande</th><th>Statut</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php foreach ($demandes as $d): ?>
              <tr>
                <td><?= htmlspecialchars($d['nom']) ?></td>
                <td><?= htmlspecialchars($d['type']) ?></td>
                <td><?= htmlspecialchars($d['offre']) ?></td>
                <td>
                  <?php if ($d['statut'] === 'En attente'): ?>
                    <span class="badge bg-warning text-dark">En attente</span>
                  <?php elseif ($d['statut'] === 'Acceptée'): ?>
                    <span class="badge bg-success">Acceptée</span>
                  <?php else: ?>
                    <span class="badge bg-danger">Refusée</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($d['statut'] === 'Acceptée'): ?>
                    <button class="btn btn-sm btn-outline-primary"
                            data-bs-toggle="collapse"
                            data-bs-target="#form<?= $d['idDemande'] ?>">
                      Ajouter offre
                    </button>
                  <?php else: ?>—<?php endif; ?>
                </td>
              </tr>
              <?php if ($d['statut'] === 'Acceptée'): ?>
                <tr class="collapse" id="form<?= $d['idDemande'] ?>">
                  <td colspan="5">
                    <div class="bg-white p-3 rounded">
                      <form method="POST" class="row g-3">
                        <input type="hidden" name="idDemande" value="<?= $d['idDemande'] ?>">
                        <div class="col-md-3">
                          <input name="typeOffre" type="text" class="form-control" placeholder="Type d’offre" required>
                        </div>
                        <div class="col-md-3">
                          <input name="descriptionOffre" type="text" class="form-control" placeholder="Description" required>
                        </div>
                        <div class="col-md-2">
                          <input name="discount" type="number" class="form-control" placeholder="Réduc (%)" required>
                        </div>
                        <div class="col-md-2">
                          <input name="dateDebut" type="date" class="form-control" required>
                        </div>
                        <div class="col-md-2">
                          <input name="dateFin" type="date" class="form-control" required>
                        </div>
                        <!-- Flash Deal -->
                        <div class="col-md-2 form-check">
                          <input class="form-check-input" type="checkbox"
                                 name="is_flash" id="flash_<?= $d['idDemande'] ?>">
                          <label class="form-check-label"
                                 for="flash_<?= $d['idDemande'] ?>">Flash Deal</label>
                        </div>
                        <div class="col-md-3 flash-fields d-none">
                          <input name="flash_start" type="datetime-local" class="form-control" placeholder="Début du Flash">
                        </div>
                        <div class="col-md-3 flash-fields d-none">
                          <input name="flash_end" type="datetime-local" class="form-control" placeholder="Fin du Flash">
                        </div>
                        <div class="col-12 text-end">
                          <button name="ajouter_offre" class="btn btn-warning">Valider</button>
                        </div>
                      </form>
                    </div>
                  </td>
                </tr>
              <?php endif; ?>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </div>

  <!-- Footer -->
  <div class="container-fluid bg-dark text-light py-4">
    <div class="container text-center">&copy; 2025 Let’s Link — Tous droits réservés.</div>
  </div>

  <!-- Scripts JS -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="../lib/wow/wow.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>new WOW().init();</script>
  <script>
    // Affiche/masque les champs Flash Deal
    document.querySelectorAll('input[name="is_flash"]').forEach(cb => {
      cb.addEventListener('change', function() {
        this.closest('form')
            .querySelectorAll('.flash-fields')
            .forEach(el => el.classList.toggle('d-none', !this.checked));
      });
    });
  </script>
</body>
</html>
