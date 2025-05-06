<?php
session_start();

try {
    $pdo = new PDO("mysql:host=localhost;dbname=covoiturage_db;charset=utf8", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connexion échouée : " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    // Requête pour chercher l'utilisateur
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = ?");
    $stmt->execute([$email]);
    $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($utilisateur && $motDePasse === $utilisateur['mot_de_passe'])

    {
        $_SESSION['utilisateur_id'] = $utilisateur['id_utilisateur'];
        header("Location: lister-covoiturages.php");
        exit;
    } else {
        $erreur = "❌ Identifiants incorrects.";
    }
}
?>

<!-- Formulaire HTML -->
<form method="POST">
    <input type="text" name="email" placeholder="Email">
    <input type="password" name="mot_de_passe" placeholder="Mot de passe">
    <input type="submit" value="Se connecter">
</form>

<?php if (isset($erreur)) : ?>
  <p style="color: red; font-weight: bold;"><i class="fas fa-times"></i> <?= $erreur ?></p>
<?php endif; ?>
