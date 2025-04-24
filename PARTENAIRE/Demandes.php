<?php
// Connexion à la base de données avec PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname=lets_link", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Traitement des actions (Accepter, Refuser, Attendre)
if (isset($_GET['action']) && isset($_GET['id_demande'])) {
    $id_demande = intval($_GET['id_demande']);
    $action = $_GET['action'];

    if ($action == 'accepter') {
        $conn->query("UPDATE demandes SET statut='Acceptée' WHERE idDemande=$id_demande");
    } elseif ($action == 'refuser') {
        $conn->query("UPDATE demandes SET statut='Refusée' WHERE idDemande=$id_demande");
    } elseif ($action == 'attente') {
        $conn->query("UPDATE demandes SET statut='En attente' WHERE idDemande=$id_demande");
    }
    header("Location: Demandes.php");
    exit;
}

// Recherche dynamique (si une recherche est effectuée)
$search = "";
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

// Requête SQL pour récupérer toutes les demandes
$sql = "SELECT idDemande, nom, type, offre, statut, date_demande 
        FROM demandes
        WHERE nom LIKE :search 
        ORDER BY date_demande DESC";

// Préparation et exécution de la requête
$stmt = $conn->prepare($sql);
$stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
$stmt->execute();

// Récupérer les résultats
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Demandes</title>
    <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        /* Style pour la recherche */
        .search-box {
            margin-bottom: 20px;
        }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: center; }
        th { background-color: #f8f9fc; color: #333; }
    </style>
</head>
<body id="page-top">

<div id="wrapper">
    <!-- Sidebar -->
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
            <a class="nav-link" href="Demandes.php">
                <i class="fas fa-fw fa-handshake"></i>
                <span>Nos Demandes</span>
            </a>
        </li>
    </ul>

    <!-- Content -->
    <div class="main">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Liste des Demandes</h1>

            <!-- Recherche Dynamique -->
            <input type="text" id="search" class="form-control mb-3" placeholder="Rechercher un partenaire..." value="<?php echo $search; ?>">

            <!-- Tableau des demandes -->
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Partenaire</th>
                        <th>Offre</th>
                        <th>Statut</th>
                        <th>Date Demande</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($results) > 0) {
                        foreach ($results as $row) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['nom']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['type']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['statut']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['date_demande']) . "</td>";
                            echo "<td>
                                    <a href='?action=accepter&id_demande=" . $row['idDemande'] . "' class='btn btn-success btn-sm'>Accepter</a>
                                    <a href='?action=refuser&id_demande=" . $row['idDemande'] . "' class='btn btn-danger btn-sm'>Refuser</a>
                                    <a href='?action=attente&id_demande=" . $row['idDemande'] . "' class='btn btn-warning btn-sm'>En Attente</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>Aucune demande trouvée.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>

<script>
    // Recherche dynamique
    document.getElementById("search").addEventListener("input", function() {
        const searchValue = this.value;
        window.location.href = "Demandes.php?search=" + searchValue; // Met à jour l'URL avec la nouvelle recherche
    });
</script>

</body>
</html>
