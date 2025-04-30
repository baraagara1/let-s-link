<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "covoiturage_db");
    if ($conn->connect_error) die("Échec de la connexion");

    $destination = $_POST['destination'];
    $date = $_POST['date'];
    $places = $_POST['place_dispo'];
    $id_utilisateur = $_POST['id_utilisateur'];

    $sql = "INSERT INTO covoiturage (destination, date, place_dispo, id_utilisateur) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $destination, $date, $places, $id_utilisateur);
    $stmt->execute();

    header("Location: ajouter-covoiturage.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Covoiturage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>

<body>
    <!-- Topbar -->
    <div class="container-fluid bg-dark text-light px-0 py-2">
        <div class="row gx-0 d-none d-lg-flex">
            <div class="col-lg-7 px-5 text-start">
                <div class="h-100 d-inline-flex align-items-center me-4">
                    <i class="fa fa-phone-alt me-2"></i>
                    <span>+126 34 567 289</span>
                </div>
                <div class="h-100 d-inline-flex align-items-center">
                    <i class="far fa-envelope me-2"></i>
                    <span>PureVibe@gmail.com</span>
                </div>
            </div>
            <div class="col-lg-5 px-5 text-end">
                <span>Suivez-nous :</span>
                <a class="btn btn-link text-light" href="#"><i class="fab fa-facebook-f"></i></a>
                <a class="btn btn-link text-light" href="#"><i class="fab fa-twitter"></i></a>
                <a class="btn btn-link text-light" href="#"><i class="fab fa-linkedin-in"></i></a>
                <a class="btn btn-link text-light" href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light sticky-top p-0">
        <a href="index.html" class="navbar-brand d-flex align-items-center px-4 px-lg-5">
            <img src="logo.png" alt="logo" style="width:140px;">
        </a>
        <div class="collapse navbar-collapse" id="navbarCollapse">
            <div class="navbar-nav ms-auto p-4 p-lg-0">
                <a href="index.html" class="nav-item nav-link">Accueil</a>
                <div class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle active" data-bs-toggle="dropdown">Covoiturage</a>
                    <div class="dropdown-menu bg-light m-0">
                        <a href="ajouter-covoiturage.php" class="dropdown-item">Ajouter un covoiturage</a>
                        <a href="lister-covoiturages.php" class="dropdown-item">Consulter les covoiturages</a>
                    </div>
                </div>
                <a href="contact.html" class="nav-item nav-link">Contact</a>
            </div>
            <a href="#" class="btn btn-primary py-4 px-lg-4 rounded-0 d-none d-lg-block" style="background-color: #090068; border: none;">
                Se connecter <i class="fa fa-arrow-right ms-3"></i>
            </a>
        </div>
    </nav>

    <!-- Formulaire -->
    <div class="container py-5">
        <h1 class="text-center mb-4">Ajouter un Covoiturage</h1>
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success text-center">Covoiturage ajouté avec succès !</div>
        <?php endif; ?>
        <form action="ajouter-covoiturage.php" method="POST" class="row g-3">
            <div class="col-md-6">
                <label for="destination" class="form-label">Destination</label>
                <input type="text" name="destination" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="date" class="form-label">Date</label>
                <input type="text" name="date" class="form-control" placeholder="jj/mm/aaaa" required>
            </div>
            <div class="col-md-6">
                <label for="place_dispo" class="form-label">Places Disponibles</label>
                <input type="text" name="place_dispo" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="id_utilisateur" class="form-label">ID Utilisateur</label>
                <input type="text" name="id_utilisateur" class="form-control" required>
            </div>
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary">Ajouter</button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light text-center py-4">
        <div class="container">
            <p class="mb-0">&copy; 2025 Let's Link. Tous droits réservés.</p>
        </div>
    </footer>

    <!-- JS -->
    <script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
