<?php
// Connexion à la base de données avec PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname=lets_link", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Suppression d'un partenaire et ses offres
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $conn->beginTransaction();
    try {
        // Suppression des offres du partenaire
        $stmt1 = $conn->prepare("DELETE FROM offres WHERE idP = :id");
        $stmt1->execute([':id' => $id]);

        // Suppression du partenaire
        $stmt2 = $conn->prepare("DELETE FROM partenaire WHERE idP = :id");
        $stmt2->execute([':id' => $id]);

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Échec de la suppression : " . $e->getMessage();
    }
    header("Location: liste.php");
    exit;
}

// Suppression d'une offre
if (isset($_GET['supprimer_offre'])) {
    $id = intval($_GET['supprimer_offre']);
    $stmt = $conn->prepare("DELETE FROM offres WHERE idOffre = :idOffre");
    $stmt->execute([':idOffre' => $id]);
    header("Location: liste.php");
    exit;
}

// Modification d'une offre
if (isset($_POST['modifier_offre_submit'])) {
    $idOffre = intval($_POST['idOffre']);
    $typeOffre = $_POST['typeOffre'];
    $descriptionOffre = $_POST['descriptionOffre'];
    $discount = $_POST['discount'];
    $dateDebut = $_POST['dateDebut'];
    $dateFin = $_POST['dateFin'];

    $stmt = $conn->prepare("UPDATE offres SET typeOffre = :typeOffre, descriptionOffre = :descriptionOffre, discount = :discount, dateDebut = :dateDebut, dateFin = :dateFin WHERE idOffre = :idOffre");
    $stmt->execute([
        ':idOffre' => $idOffre,
        ':typeOffre' => $typeOffre,
        ':descriptionOffre' => $descriptionOffre,
        ':discount' => $discount,
        ':dateDebut' => $dateDebut,
        ':dateFin' => $dateFin
    ]);
    header("Location: liste.php");
    exit;
}

// Ajouter une offre pour un partenaire spécifique
if (isset($_POST['ajouter_offre_submit'])) {
    $idP = intval($_POST['idP']);
    $typeOffre = $_POST['typeOffre'];
    $descriptionOffre = $_POST['descriptionOffre'];
    $discount = $_POST['discount'];
    $dateDebut = $_POST['dateDebut'];
    $dateFin = $_POST['dateFin'];

    $stmt = $conn->prepare("INSERT INTO offres (idP, typeOffre, descriptionOffre, discount, dateDebut, dateFin) VALUES (:idP, :typeOffre, :descriptionOffre, :discount, :dateDebut, :dateFin)");
    $stmt->execute([
        ':idP' => $idP,
        ':typeOffre' => $typeOffre,
        ':descriptionOffre' => $descriptionOffre,
        ':discount' => $discount,
        ':dateDebut' => $dateDebut,
        ':dateFin' => $dateFin
    ]);
    header("Location: liste.php");
    exit;
}

// Récupérer les données de l'offre à modifier
$offreModif = null;
if (isset($_GET['modifier_offre'])) {
    $idOffre = intval($_GET['modifier_offre']);
    $stmt = $conn->prepare("SELECT * FROM offres WHERE idOffre = :idOffre");
    $stmt->execute([':idOffre' => $idOffre]);
    $offreModif = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Récupérer les données du partenaire à modifier
$modif = null;
if (isset($_GET['modifier'])) {
    $id_modif = intval($_GET['modifier']);
    $stmt = $conn->prepare("SELECT * FROM partenaire WHERE idP = :idP");
    $stmt->execute([':idP' => $id_modif]);
    $modif = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Liste des partenaires</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    body { font-family: 'Nunito', sans-serif; }
    .btn-success { background-color: #1cc88a; color: white; padding: 8px 12px; margin: 20px 0; border-radius: 5px; }
    .btn-success:hover { background-color: #17a673; }
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
    th { background-color: #f8f9fc; color: #333; }
    .container-fluid { padding: 30px; }
    input, textarea, select { width: 100%; padding: 8px; margin: 5px 0; border-radius: 4px; border: 1px solid #ccc; }
    .form-panel { margin-top: 30px; padding: 20px; background: #f8f9fc; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
    .btn-outline-primary:hover {
  background-color: #4e73df;
  color: white;
}

.btn-outline-danger:hover {
  background-color: #e74a3b;
  color: white;
}

  </style>
</head>
<body id="page-top">
<div id="wrapper">
  <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../index.php">
      <img src="../img/logo.png" alt="logo" width="150" height="60">
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item active">
      <a class="nav-link" href="../index.php">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Tableau De Bord</span>
      </a>
    </li>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Interface</div>
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePartenaires" aria-expanded="true" aria-controls="collapsePartenaires">
        <i class="fas fa-fw fa-handshake"></i>
        <span>Nos Partenaires</span>
      </a>
      <div id="collapsePartenaires" class="collapse show" aria-labelledby="headingPartenaires" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Gestion des Partenaires:</h6>
          <a class="collapse-item active" href="liste.php">Liste des Partenaires</a>
          <a class="collapse-item" href="Demandes.php">Les demandes</a>
        </div>
      </div>
    </li>
  </ul>

  <div class="main">
    <div class="container-fluid">
      <h1 class="h3 mb-4 text-gray-800">Liste des partenaires</h1>

      <input type="text" id="recherche" class="form-control mb-3" placeholder="Rechercher un partenaire...">

      <table class="table table-bordered">
        <thead>
          <tr>
            <th>Nom</th>
            <th>Numéro</th>
            <th>Photo</th>
            <th>Site Web</th>
            <th>Offres</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = $conn->query("SELECT * FROM partenaire");
          if ($result->rowCount() > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
              echo "<tr>";
              echo "<td>" . htmlspecialchars($row['nomP']) . "</td>";
              echo "<td>" . htmlspecialchars($row['numP']) . "</td>";
              echo "<td><img src='" . htmlspecialchars($row['photoP']) . "' width='50' height='50'></td>";
              echo "<td><a href='" . htmlspecialchars($row['site_web']) . "' target='_blank'>" . htmlspecialchars($row['site_web']) . "</a></td>";
              echo "<td>";
              $offres = $conn->query("SELECT * FROM offres WHERE idP = {$row['idP']}");
              while ($offre = $offres->fetch(PDO::FETCH_ASSOC)) {
                echo "<div><strong>{$offre['typeOffre']}</strong><br>{$offre['descriptionOffre']}<br>Du {$offre['dateDebut']} au {$offre['dateFin']} - {$offre['discount']}% 
                <a href='?supprimer_offre={$offre['idOffre']}' class='text-danger'>&times;</a> 
                <a href='?modifier_offre={$offre['idOffre']}' class='text-warning'>Modifier</a></div>";
                
                if (isset($_GET['modifier_offre']) && $_GET['modifier_offre'] == $offre['idOffre']) {
                    echo "<form method='POST' class='mt-2'>
                            <input type='hidden' name='idOffre' value='{$offre['idOffre']}' />
                            <input name='typeOffre' value='{$offre['typeOffre']}' placeholder='Type' />
                            <input name='descriptionOffre' value='{$offre['descriptionOffre']}' placeholder='Description' />
                            <input name='discount' value='{$offre['discount']}' placeholder='Réduction' />
                            <input name='dateDebut' value='{$offre['dateDebut']}' type='date' />
                            <input name='dateFin' value='{$offre['dateFin']}' type='date' />
                            <button name='modifier_offre_submit' class='btn btn-sm btn-primary mt-1'>Enregistrer les modifications</button>
                          </form><hr>";
                } else {
                    echo "<hr>";
                }
                              }
              echo "<button class='btn btn-sm btn-info' onclick='toggleForm({$row['idP']})'>Ajouter une offre</button>";
              echo "<form id='form_{$row['idP']}' method='POST' enctype='multipart/form-data' style='display:none;'>
                      <input type='hidden' name='idP' value='{$row['idP']}' />
                      <input name='typeOffre' placeholder='Type' />
                      <input name='descriptionOffre' placeholder='Description' />
                      <input name='discount' placeholder='Réduction' />
                      <input name='dateDebut' placeholder='Date Début' type='date' />
                      <input name='dateFin' placeholder='Date Fin' type='date' />
                      <button name='ajouter_offre_submit' class='btn btn-sm btn-success'>Ajouter une offre</button>
                    </form>";
              echo "</td>";
              echo "<td>
              <a href='liste.php?modifier={$row['idP']}' class='btn btn-sm btn-outline-primary me-1' style='border-radius: 30px; transition: 0.3s;'>
                <i class='fas fa-edit'></i> Modifier
              </a>
              <a href='liste.php?supprimer={$row['idP']}' class='btn btn-sm btn-outline-danger' style='border-radius: 30px; transition: 0.3s;' onclick=\"return confirm('Supprimer ce partenaire et ses offres ?');\">
                <i class='fas fa-trash-alt'></i> Supprimer
              </a>
            </td>";
            
            }
          } else {
            echo "<tr><td colspan='6'>Aucun partenaire trouvé.</td></tr>";
          }
          ?>
        </tbody>
      </table>

      <button class="btn btn-success" onclick="window.location.href='liste.php?ajouter=true'">Ajouter un partenaire</button>

      <div class="form-panel" style="display: block;">
        <?php if ($modif): ?>
          <h4 class="text-primary">Modifier un partenaire</h4>
          <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="idP" value="<?= $modif['idP'] ?>">
            <input type="text" name="nomP" value="<?= $modif['nomP'] ?>" required />
            <input type="text" name="typeP" value="<?= $modif['typeP'] ?>" />
            <input type="email" name="emailP" value="<?= $modif['emailP'] ?>" />
            <input type="text" name="adresseP" value="<?= $modif['adresseP'] ?>" />
            <input type="text" name="numP" value="<?= $modif['numP'] ?>" />
            <input type="text" name="site_web" value="<?= $modif['site_web'] ?>" />
            <textarea name="descriptionP"><?= $modif['descriptionP'] ?></textarea>
            <button type="submit" name="modifier_submit" class="btn btn-primary mt-2">Enregistrer</button>
          </form>
        <?php elseif (isset($_GET['ajouter'])): ?>
          <h4 class="text-success">Ajouter un partenaire</h4>
          <form method="POST" enctype="multipart/form-data">
            <input type="text" name="nomP" placeholder="Nom" required />
            <input type="text" name="typeP" placeholder="Type" />
            <input type="email" name="emailP" placeholder="Email" />
            <input type="text" name="adresseP" placeholder="Adresse" />
            <input type="text" name="numP" placeholder="Téléphone" />
            <input type="text" name="site_web" placeholder="Site Web" />
            <textarea name="descriptionP" placeholder="Description"></textarea>
            <input type="text" name="photoP" placeholder="Lien de l'image" />
            <h5>Ajouter une offre (facultatif)</h5>
            <input type="text" name="typeOffre" placeholder="Type d'offre" />
            <input type="text" name="descriptionOffre" placeholder="Description" />
            <input type="text" name="discount" placeholder="Réduction (%)" />
            <input type="date" name="dateDebut" placeholder="Date début" />
            <input type="date" name="dateFin" placeholder="Date fin" />
            <button type="submit" name="ajouter_submit" class="btn btn-success mt-2">Ajouter</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
  document.getElementById("recherche").addEventListener("input", function () {
    const filtre = this.value.toLowerCase();
    document.querySelectorAll("table tbody tr").forEach(row => {
      row.style.display = row.innerText.toLowerCase().includes(filtre) ? "" : "none";
    });
  });

  function toggleForm(id) {
    var form = document.getElementById("form_" + id);
    form.style.display = (form.style.display === "none" || form.style.display === "") ? "block" : "none";
  }
</script>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>
</body>
</html>
