
<?php
$conn = new mysqli("localhost", "root", "", "lets_link");
if ($conn->connect_error) {
  die("Erreur de connexion : " . $conn->connect_error);
}

// Traitement formulaire ajout d’offre
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["ajouter_offre"])) {
  $idP = intval($_POST["idP"]);
  $type = $_POST["typeOffre"];
  $description = $_POST["descriptionOffre"];
  $discount = $_POST["discount"];
  $conn->query("INSERT INTO offres (idP, typeOffre, descriptionOffre, discount) VALUES ('$idP', '$type', '$description', '$discount')");
  echo "<script>alert('Offre ajoutée avec succès !');</script>";
}

// Récupération des partenaires et offres
$sql = "SELECT p.idP, p.nomP, p.photoP, p.descriptionP, o.typeOffre, o.descriptionOffre, o.discount
        FROM partenaire p
        LEFT JOIN offres o ON p.idP = o.idP";
$res = $conn->query($sql);

$partenaires = [];
while ($row = $res->fetch_assoc()) {
  $id = $row["idP"];
  if (!isset($partenaires[$id])) {
    $partenaires[$id] = [
      "nom" => $row["nomP"],
      "photo" => $row["photoP"],
      "description" => $row["descriptionP"],
      "offres" => []
    ];
  }
  if ($row["typeOffre"]) {
    $partenaires[$id]["offres"][] = [
      "type" => $row["typeOffre"],
      "desc" => $row["descriptionOffre"],
      "discount" => $row["discount"]
    ];
  }
}

// Récupérer les demandes acceptées
$demandes = [];
$demandeSQL = $conn->query("SELECT * FROM demandes WHERE statut = 'Acceptée'");
while ($d = $demandeSQL->fetch_assoc()) {
  $demandes[] = $d;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="utf-8">
  <title>Nos Partenaires - Let's Link</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <meta content="Découvrez nos partenaires privilégiés et leurs offres exclusives." name="description">
  <meta content="partenaires, offres, exclusives, restaurant, spa, escape game" name="keywords">

  <!-- Google Web Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500;600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">

  <!-- Icon Font Stylesheet -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root {
      --primary: #e99e13;
      --secondary: #525368;
      --light: #ffffff;
      --dark: #000000;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Open Sans', sans-serif;
      background-color: #f8f9fc;
      color: var(--dark);
      line-height: 1.6;
    }

    h1, h2, h3, h4, h5, h6 {
      font-family: 'Jost', sans-serif;
      font-weight: 600;
    }

    .back-to-top {
      position: fixed;
      right: 30px;
      bottom: 30px;
      z-index: 99;
      background-color: var(--primary);
      color: white;
      border-radius: 50%;
      width: 48px;
      height: 48px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      display: none;
    }

    .topbar {
      background-color: #1f1462;
      color: #fff;
      font-size: 0.9rem;
    }

    .navbar {
      background-color: #fff;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      padding: 1rem;
    }

    .navbar-brand img {
      height: 50px;
    }

    .nav-link.active {
      color: var(--primary) !important;
    }

    .btn-primary {
      background-color: var(--primary);
      border: none;
    }

    .btn-primary:hover {
      background-color: #cc880f;
    }

    .partner-card {
      transition: all 0.3s ease;
      border: none;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      background-color: #fff;
      animation: fadeInUp 1s ease both;
    }

    @keyframes fadeInUp {
      0% {opacity: 0; transform: translateY(20px);}
      100% {opacity: 1; transform: translateY(0);}
    }

    .partner-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
    }

    .partner-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
      transition: transform 0.4s ease;
    }

    .partner-card:hover img {
      transform: scale(1.05);
    }

    .partner-card .card-body {
      padding: 1.25rem;
    }

    .partner-card .card-title {
      color: var(--primary);
      margin-bottom: 0.5rem;
      font-weight: 600;
    }

    .partner-card .alert {
      background-color: #f0f0f0;
      border: 0;
      margin-top: 1rem;
      font-size: 0.9rem;
    }

    table {
      width: 100%;
      background: white;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
      border-collapse: collapse;
      margin-top: 2rem;
    }

    th, td {
      padding: 12px;
      border: 1px solid #dee2e6;
      vertical-align: middle;
    }

    .badge {
      font-size: 0.9em;
      padding: 0.4em 0.7em;
    }

    .footer {
      background-color: #000000;
      color: white;
      padding: 2rem 0;
    }

    .footer a {
      color: #fff;
      text-decoration: none;
    }

    .footer a:hover {
      color: var(--primary);
    }
  </style>
</head>
<body>
  <!-- Topbar Start -->
  <div class="container-fluid topbar px-0 py-2">
    <div class="row gx-0 d-none d-lg-flex px-5">
      <div class="col-lg-7 text-start">
        <div class="h-100 d-inline-flex align-items-center me-4">
          <i class="fa fa-phone-alt me-2"></i>
          <span>+126 34 567 289</span>
        </div>
        <div class="h-100 d-inline-flex align-items-center">
          <i class="far fa-envelope me-2"></i>
          <span>LetsLink@gmail.com</span>
        </div>
      </div>
      <div class="col-lg-5 text-end">
        <div class="h-100 d-inline-flex align-items-center mx-n2">
          <span class="me-2">Follow Us:</span>
          <a class="btn btn-link text-light" href="#"><i class="fab fa-facebook-f"></i></a>
          <a class="btn btn-link text-light" href="#"><i class="fab fa-twitter"></i></a>
          <a class="btn btn-link text-light" href="#"><i class="fab fa-linkedin-in"></i></a>
          <a class="btn btn-link text-light" href="#"><i class="fab fa-instagram"></i></a>
        </div>
      </div>
    </div>
  </div>
  <!-- Topbar End -->

  <!-- Navbar Start -->
  <nav class="navbar navbar-expand-lg navbar-light sticky-top">
    <a href="../index.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
      <img src="../logo.png" alt="Logo">
    </a>
    <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarCollapse">
      <div class="navbar-nav ms-auto p-4 p-lg-0">
        <a href="../index.html" class="nav-item nav-link">Accueil</a>
        <a href="../about.html" class="nav-item nav-link">A propos</a>
        <a href="../service.html" class="nav-item nav-link">Conseils</a>
        <a href="../View/frontoffice/acceuil.php" class="nav-item nav-link">Recettes</a>
        <div class="nav-item dropdown">
          <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Boutique</a>
          <div class="dropdown-menu bg-light m-0">
            <a href="../feature.html" class="dropdown-item">Produits Alimentaire</a>
            <a href="../quote.html" class="dropdown-item">Box Nouriture</a>
            <a href="../team.html" class="dropdown-item">Produit En Gros</a>
          </div>
        </div>
        <a href="#" class="nav-item nav-link active">Partenaires</a>
      </div>
      <a href="../login.html" class="btn btn-primary py-2 px-lg-4 rounded-0 d-none d-lg-block">Se connecter <i class="fa fa-arrow-right ms-2"></i></a>
    </div>
  </nav>
  <!-- Fin Navbar -->

  <!-- Section "Héros" (Optionnelle) -->
  <div class="container-fluid p-0 wow fadeIn" data-wow-delay="0.1s">
    <div style="background: url('img/hero-bg.jpg') center center no-repeat; background-size: cover;">
      <div class="container py-5">
        <div class="row justify-content-center py-5">
          <div class="col-lg-8 text-center text-white">
            <h1 class="display-3 animated slideInDown">Nos Partenaires</h1>
            <p class="animated slideInDown mb-4">Découvrez les collaborations qui enrichissent votre expérience</p>
          </div>
        </div>
      </div>
    </div>
  </div>
<body>
  

  <div class="container mt-4">
    
    <div class="row">
      <?php foreach ($partenaires as $id => $p): ?>
        <div class="col-md-4">
          <div class="partner-card animate__animated animate__fadeInUp">
            <img src="<?= htmlspecialchars($p['photo']) ?>" alt="<?= htmlspecialchars($p['nom']) ?>">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($p['nom']) ?></h5>
              <p><?= htmlspecialchars($p['description']) ?></p>
              <?php foreach ($p['offres'] as $offre): ?>
                <div class="offer-box">
                  <strong><?= htmlspecialchars($offre['type']) ?>:</strong>
                  <?= htmlspecialchars($offre['desc']) ?> - <b><?= htmlspecialchars($offre['discount']) ?></b>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <?php if (!empty($demandes)): ?>
      <div class="form-container">
        <h3>Ajouter une Offre pour une demande acceptée</h3>
        <?php foreach ($demandes as $d): ?>
          <form method="POST">
            <input type="hidden" name="idP" value="<?= $d['idDemande'] ?>">
            <div class="mb-2"><strong><?= htmlspecialchars($d['nom']) ?> (<?= htmlspecialchars($d['type']) ?>)</strong></div>
            <input type="text" name="typeOffre" placeholder="Type d'offre" class="form-control">
            <textarea name="descriptionOffre" placeholder="Description" class="form-control"></textarea>
            <input type="text" name="discount" placeholder="Réduction (%)" class="form-control">
            <button type="submit" name="ajouter_offre" class="btn btn-warning mt-2">Ajouter l'offre</button>
          </form>
          <hr>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
