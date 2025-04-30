<?php
$conn = new mysqli("localhost", "root", "", "lets_link");
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomP = $_POST['nomP'];
    $typeP = $_POST['typeP'];
    $emailP = $_POST['emailP'];
    $adresseP = $_POST['adresseP'];
    $numP = $_POST['numP'];
    $site_web = $_POST['site_web'];
    $descriptionP = $_POST['descriptionP'];

    // Gestion de l'image
    $photoP = "";
    $dossier = "uploads/";

    if (isset($_FILES['photoP']) && $_FILES['photoP']['error'] == 0) {
        $tmp_name = $_FILES['photoP']['tmp_name'];
        $filename = time() . "_" . basename($_FILES['photoP']['name']);
        if (!file_exists($dossier)) {
            mkdir($dossier, 0777, true);
        }
        $target_path = $dossier . $filename;
        if (move_uploaded_file($tmp_name, $target_path)) {
            $photoP = $target_path;
        } else {
            $message = "❌ Erreur lors du téléchargement de l'image.";
        }
    }

    if ($photoP !== "") {
        $sql = "INSERT INTO partenaire (nomP, typeP, photoP, emailP, adresseP, numP, site_web, descriptionP)
                VALUES ('$nomP', '$typeP', '$photoP', '$emailP', '$adresseP', '$numP', '$site_web', '$descriptionP')";

        if ($conn->query($sql) === TRUE) {
            $message = "✅ Partenaire ajouté avec succès !";
        } else {
            $message = "❌ Erreur SQL : " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Partenaire</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f2f2f2;
            padding: 40px;
        }

        .container {
            width: 90%;
            max-width: 600px;
            margin: auto;
            background-color: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            color: #4e73df;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button {
            background-color: #4e73df;
            color: white;
            padding: 12px 20px;
            margin-top: 20px;
            border: none;
            width: 100%;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #2e59d9;
        }

        .message {
            text-align: center;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 8px;
            color: white;
            background-color: #28a745;
        }

        .message.error {
            background-color: #dc3545;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Ajouter un Partenaire</h2>

    <?php if (!empty($message)): ?>
        <div class="message <?= str_contains($message, '❌') ? 'error' : '' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="nomP">Nom :</label>
        <input type="text" name="nomP" required>

        <label for="typeP">Type :</label>
        <input type="text" name="typeP">

        <label for="photoP">Photo :</label>
        <input type="file" name="photoP" accept="image/*" required>

        <label for="emailP">Email :</label>
        <input type="email" name="emailP">

        <label for="adresseP">Adresse :</label>
        <input type="text" name="adresseP">

        <label for="numP">Numéro de téléphone :</label>
        <input type="text" name="numP">

        <label for="site_web">Site Web :</label>
        <input type="text" name="site_web">

        <label for="descriptionP">Description :</label>
        <textarea name="descriptionP" rows="4"></textarea>

        <button type="submit">Ajouter le partenaire</button>
    </form>
</div>

</body>
</html>
