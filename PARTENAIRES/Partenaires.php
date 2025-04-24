<?php
// Connexion PDO
try {
    $pdo = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Échec de connexion : " . $e->getMessage());
}

// 1) Suppression d'une offre
if (isset($_GET['supprimer_offre'])) {
    $idO = intval($_GET['supprimer_offre']);
    $pdo->query("DELETE FROM offres WHERE idOffre=$idO");
    header("Location: partenaires.php");
    exit;
}

// 2) Modification d'une offre
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['modifier_offre'])) {
    $idO  = intval($_POST['idOffre']);
    $type = $pdo->quote($_POST['typeOffre']);
    $desc = $pdo->quote($_POST['descriptionOffre']);
    $disc = $pdo->quote($_POST['discount']);
    $sd   = $pdo->quote($_POST['dateDebut']);
    $ed   = $pdo->quote($_POST['dateFin']);
    $pdo->query("
      UPDATE offres SET
        typeOffre        = $type,
        descriptionOffre = $desc,
        discount         = $disc,
        dateDebut        = $sd,
        dateFin          = $ed
      WHERE idOffre = $idO
    ");
    header("Location: partenaires.php");
    exit;
}

// 3) Transformer une demande acceptée en offre
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['ajouter_offre'])) {
    $nomDem = $pdo->quote($_POST['nomDemande']);
    $r = $pdo->query("
      SELECT d.idDemande, p.idP
      FROM demandes d
      JOIN partenaire p ON p.nomP = d.nom
      WHERE d.nom=$nomDem AND d.statut='Acceptée'
    ");
    if ($r->rowCount()) {
        $rd  = $r->fetch(PDO::FETCH_ASSOC);
        $idP = intval($rd['idP']);
        $idD = intval($rd['idDemande']);
        $type = $pdo->quote($_POST['typeOffre']);
        $desc = $pdo->quote($_POST['descriptionOffre']);
        $disc = $pdo->quote($_POST['discount']);
        $sd   = $pdo->quote($_POST['dateDebut']);
        $ed   = $pdo->quote($_POST['dateFin']);
        $pdo->query("
          INSERT INTO offres (idP,typeOffre,descriptionOffre,discount,dateDebut,dateFin)
          VALUES ($idP,$type,$desc,$disc,$sd,$ed)
        ");
        $pdo->query("DELETE FROM demandes WHERE idDemande=$idD");
    }
    header("Location: partenaires.php");
    exit;
}

// 4) Envoyer une nouvelle demande
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['envoyer_demande'])) {
    $nom   = $pdo->quote($_POST['nom']);
    $typeD = $pdo->quote($_POST['typeDemande']);
    $offre = $pdo->quote($_POST['offreDemande']);
    $v = $pdo->query("SELECT idP FROM partenaire WHERE nomP=$nom");
    if ($v->rowCount()) {
        $pdo->query("INSERT INTO demandes(nom,type,offre,statut) VALUES($nom,$typeD,$offre,'En attente')");
    }
    header("Location: partenaires.php");
    exit;
}

// Chargement des demandes
$demandes = $pdo
    ->query("SELECT * FROM demandes ORDER BY idDemande DESC")
    ->fetchAll(PDO::FETCH_ASSOC);

// Chargement des partenaires + offres
$partenaires = [];
$rs = $pdo->query("
    SELECT p.idP,p.nomP,p.photoP,p.descriptionP,
           o.idOffre,o.typeOffre,o.descriptionOffre,o.discount,o.dateDebut,o.dateFin
    FROM partenaire p
    LEFT JOIN offres o ON p.idP=o.idP
    ORDER BY p.nomP,o.idOffre
");
while ($r = $rs->fetch(PDO::FETCH_ASSOC)) {
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
            'id'       => $r['idOffre'],
            'type'     => $r['typeOffre'],
            'desc'     => $r['descriptionOffre'],
            'discount' => $r['discount'],
            'start'    => $r['dateDebut'],
            'end'      => $r['dateFin']
        ];
    }
}
// Séparation en 2 listes
$avecOffres = array_filter($partenaires, fn($p)=>!empty($p['offres']));
$sansOffres = array_filter($partenaires, fn($p)=> empty($p['offres']));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Nos Partenaires & Offres — Let’s Link</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Google Fonts + FontAwesome + Bootstrap + Animate.css -->
  <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600;700&family=Open+Sans:wght@400;500&display=swap"
        rel="stylesheet">
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
        rel="stylesheet">

  <style>
    :root { --primary:#e99e13; --dark:#000; --light:#fff; }
    *,*::before,*::after{ margin:0; padding:0; box-sizing:border-box; }
    body{ font-family:'Open Sans',sans-serif;
          background: linear-gradient(135deg,#f1f2f6,#dff9fb);
          color:var(--dark); line-height:1.6; }
    h1,h2,h3,h4,h5,h6{ font-family:'Jost',sans-serif; }

    /* Topbar */
    .topbar { background:#1f1462; color:#fff; padding:.5rem 0; font-size:.9rem; }
    .topbar a { color:#fff; }

    /* Navbar */
    .navbar { box-shadow:0 2px 4px rgba(0,0,0,.05); }
    .navbar .btn-primary { background:var(--primary); border:none; }
    .navbar .btn-primary:hover { background:#cc880f; }

    /* Cartes partenaires */
    .partner-card {
      border-radius:15px; overflow:hidden;
      background:#fff; box-shadow:0 4px 12px rgba(0,0,0,.1);
      transition:.3s;
    }
    .partner-card:hover {
      transform:translateY(-8px);
      box-shadow:0 8px 24px rgba(0,0,0,.2);
    }
    .partner-card img {
      width:100%; height:180px; object-fit:cover;
      transition:.4s;
    }
    .partner-card:hover img { transform:scale(1.05); }

    /* Offre */
    .offer-box {
      background:#f0f0f0; padding:.75rem; margin-top:1rem;
      border-radius:5px; font-size:.9rem;
    }

    /* Formulaire inline */
    .form-container {
      background:#fff; padding:1.5rem; border-radius:10px;
      box-shadow:0 0 10px rgba(0,0,0,.05); margin:2rem 0;
      animation:fadeInUp .8s ease-in-out;
    }
    .form-container h6 { margin-bottom:1rem; font-weight:600; }
    .form-container .form-control { border-radius:6px; box-shadow:none; }

    /* Animation cascade */
    .card-animate {
      opacity:0; transform:translateY(40px);
      animation:fadeInUpCard .6s ease forwards;
    }
    @keyframes fadeInUpCard {
      to { opacity:1; transform:translateY(0); }
    }
    @keyframes fadeInUp {
      from { opacity:0; transform:translateY(40px); }
      to   { opacity:1; transform:translateY(0); }
    }

    /* Badges dynamiques */
    .badge { font-size:.9rem; padding:.5em; }

    /* Footer */
    .footer { background:#1f1462; color:#fff; padding:2rem 0; }
    .footer a { color:#fff; text-decoration:none; }
    .footer a:hover { color:var(--primary); }
  </style>
</head>
<body>

  <!-- Topbar -->
  <div class="container-fluid topbar">
    <div class="row gx-0 px-5 d-none d-lg-flex">
      <div class="col-lg-7 d-flex align-items-center">
        <i class="fa fa-phone-alt me-2"></i>+216 20 123 456
        <span class="ms-4"><i class="far fa-envelope me-2"></i>letslink@gmail.com</span>
      </div>
      <div class="col-lg-5 text-end">
        <a class="btn btn-link" href="#"><i class="fab fa-facebook-f"></i></a>
        <a class="btn btn-link" href="#"><i class="fab fa-twitter"></i></a>
        <a class="btn btn-link" href="#"><i class="fab fa-linkedin-in"></i></a>
        <a class="btn btn-link" href="#"><i class="fab fa-instagram"></i></a>
      </div>
    </div>
  </div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top py-3">
    <div class="container">
      <a class="navbar-brand" href="../index.html">
        <img src="../logo.png" height="50" alt="Logo">
      </a>
      <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="nav">
        <div class="navbar-nav ms-auto">
          <a class="nav-link" href="../index.html">Accueil</a>
          <a class="nav-link" href="../about.html">À propos</a>
          <a class="nav-link" href="../service.html">Conseils</a>
          <a class="nav-link" href="../View/frontoffice/acceuil.php">Recettes</a>
          <div class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="#">Boutique</a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="../feature.html">Produits Alimentaire</a></li>
              <li><a class="dropdown-item" href="../quote.html">Box Nourriture</a></li>
              <li><a class="dropdown-item" href="../team.html">Produit En Gros</a></li>
            </ul>
          </div>
          <a class="nav-link active" href="partenaires.php">Partenaires</a>
        </div>
        <a class="btn btn-primary ms-3" href="../login.html">Se connecter</a>
      </div>
    </div>
  </nav>

  <!-- Titre -->
  <div class="container text-center my-5">
    <h1 class="display-5">Nos Partenaires & Offres</h1>
  </div>

  <div class="container mb-5">

    <!-- Partenaires avec offres -->
    <?php if ($avecOffres): ?>
      <h2 class="mb-4">Partenaires avec offres</h2>
      <div class="row">
        <?php foreach (array_values($avecOffres) as $i => $p):
          $delay = 0.1 + ($i%3)*0.2;
        ?>
          <div class="col-md-4 mb-4 wow card-animate" data-wow-delay="<?=$delay?>s">
            <div class="partner-card">
              <img src="<?=htmlspecialchars($p['photo'])?>" alt="<?=htmlspecialchars($p['nom'])?>">
              <div class="card-body">
                <h5 class="card-title"><?=htmlspecialchars($p['nom'])?></h5>
                <p><?=htmlspecialchars($p['description'])?></p>
                <?php foreach ($p['offres'] as $of): ?>
                  <div class="offer-box">
                    <strong><?=htmlspecialchars($of['type'])?> :</strong>
                    <?=htmlspecialchars($of['desc'])?> — <b><?=htmlspecialchars($of['discount'])?>%</b><br>
                    <small>du <?=htmlspecialchars($of['start'])?> au <?=htmlspecialchars($of['end'])?></small>
                    <div class="mt-2 text-end">
                      <a href="?modifier_offre=<?=$of['id']?>" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                      </a>
                      <a href="?supprimer_offre=<?=$of['id']?>"
                         onclick="return confirm('Supprimer ?')"
                         class="btn btn-sm btn-danger">
                        <i class="fas fa-trash-alt"></i>
                      </a>
                    </div>
                  </div>
                  <?php if (isset($_GET['modifier_offre']) && $_GET['modifier_offre']==$of['id']): ?>
                    <div class="form-container">
                      <h6>Modifier offre #<?=$of['id']?></h6>
                      <form method="POST" class="row g-3">
                        <input type="hidden" name="idOffre" value="<?=$of['id']?>">
                        <div class="col-md-3">
                          <input name="typeOffre" value="<?=$of['type']?>" class="form-control">
                        </div>
                        <div class="col-md-3">
                          <input name="descriptionOffre" value="<?=$of['desc']?>" class="form-control">
                        </div>
                        <div class="col-md-2">
                          <input name="discount" value="<?=$of['discount']?>" class="form-control">
                        </div>
                        <div class="col-md-2">
                          <input name="dateDebut" type="date" value="<?=$of['start']?>" class="form-control">
                        </div>
                        <div class="col-md-2">
                          <input name="dateFin" type="date" value="<?=$of['end']?>" class="form-control">
                        </div>
                        <div class="col-12 text-end">
                          <button name="modifier_offre" class="btn btn-sm btn-success">
                            Enregistrer
                          </button>
                        </div>
                      </form>
                    </div>
                  <?php endif;?>
                <?php endforeach;?>
              </div>
            </div>
          </div>
        <?php endforeach;?>
      </div>
    <?php endif;?>

    <!-- Autres partenaires -->
    <?php if ($sansOffres): ?>
      <h2 class="mt-5 mb-4">Autres partenaires</h2>
      <div class="row">
        <?php foreach (array_values($sansOffres) as $i => $p):
          $delay = 0.1 + ($i%3)*0.2;
        ?>
          <div class="col-md-4 mb-4 wow card-animate" data-wow-delay="<?=$delay?>s">
            <div class="partner-card">
              <img src="<?=htmlspecialchars($p['photo'])?>" alt="<?=htmlspecialchars($p['nom'])?>">
              <div class="card-body">
                <h5 class="card-title"><?=htmlspecialchars($p['nom'])?></h5>
                <p><?=htmlspecialchars($p['description'])?></p>
                <p class="text-muted fst-italic">Pas encore d’offres</p>
              </div>
            </div>
          </div>
        <?php endforeach;?>
      </div>
    <?php endif;?>

  </div>

  <!-- Formulaire de demande -->
  <div class="container mb-5">
    <div class="form-container">
      <h3>Demander une offre</h3>
      <form method="POST" class="row g-3">
        <div class="col-md-4">
          <label>Nom du partenaire</label>
          <input name="nom" class="form-control">
        </div>
        <div class="col-md-4">
          <label>Type</label>
          <select name="typeDemande" class="form-control">
            <option>Restaurant</option>
            <option>Cinéma</option>
            <option>Loisir</option>
          </select>
        </div>
        <div class="col-md-4">
          <label>Demande</label>
          <input name="offreDemande" class="form-control">
        </div>
        <div class="col-12 text-end">
          <button name="envoyer_demande" class="btn btn-primary">Envoyer</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Suivi des demandes -->
  <div class="container mb-5">
    <h3>Suivi de vos demandes</h3>
    <table class="table table-bordered text-center">
      <thead class="table-light">
        <tr>
          <th>Nom</th><th>Type</th><th>Demande</th><th>Statut</th><th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($demandes as $d): ?>
          <tr>
            <td><?=$d['nom']?></td>
            <td><?=$d['type']?></td>
            <td><?=$d['offre']?></td>
            <td>
              <?php if ($d['statut']=='En attente'): ?>
                <span class="badge bg-warning text-dark">En attente</span>
              <?php elseif ($d['statut']=='Acceptée'): ?>
                <span class="badge bg-success">Acceptée</span>
              <?php else: ?>
                <span class="badge bg-danger">Refusée</span>
              <?php endif;?>
            </td>
            <td>
              <?php if ($d['statut']=='Acceptée'): ?>
                <a href="?ajout_offre=<?=$d['idDemande']?>" class="btn btn-sm btn-outline-primary">
                  Ajouter offre
                </a>
              <?php else: ?>
                —
              <?php endif;?>
            </td>
          </tr>
          <?php if (isset($_GET['ajout_offre']) && $_GET['ajout_offre']==$d['idDemande']): ?>
            <tr><td colspan="5">
              <div class="form-container">
                <h4>Ajouter offre pour « <?=$d['nom']?> »</h4>
                <form method="POST" class="row g-3">
                  <input type="hidden" name="nomDemande" value="<?=$d['nom']?>">
                  <div class="col-md-3"><input name="typeOffre" placeholder="Type" class="form-control"></div>
                  <div class="col-md-3"><input name="descriptionOffre" placeholder="Desc" class="form-control"></div>
                  <div class="col-md-2"><input name="discount" placeholder="Réduc(%)" class="form-control"></div>
                  <div class="col-md-2"><input name="dateDebut" type="date" class="form-control"></div>
                  <div class="col-md-2"><input name="dateFin" type="date" class="form-control"></div>
                  <div class="col-12 text-end">
                    <button name="ajouter_offre" class="btn btn-warning">Valider</button>
                  </div>
                </form>
              </div>
            </td></tr>
          <?php endif;?>
        <?php endforeach;?>
      </tbody>
    </table>
  </div>

  <!-- Footer -->
  <div class="container-fluid footer">
    <div class="container">
      <div class="row">
        <div class="col-md-6 text-md-start">&copy; Let’s Link — Tous droits réservés.</div>
        <div class="col-md-6 text-md-end">Designed by HTML Codex</div>
      </div>
    </div>
  </div>

  <!-- JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/wow/1.1.2/wow.min.js"></script>
  <script>new WOW().init();</script>
</body>
</html>
