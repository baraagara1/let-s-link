<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Modifier un Covoiturage</title>
  <link rel="stylesheet" href="css/bootstrap.min.css">
</head>
<body class="container py-5">

  <h1 class="mb-4">✏️ Modifier un covoiturage</h1>

  <?php if (isset($covoiturage)): ?>
    <form method="POST" action="index.php?action=mettreAJour">
      <input type="hidden" name="id_cov" value="<?= $covoiturage->getId() ?>">

      <div class="mb-3">
        <label class="form-label">Lieu de départ</label>
        <input type="text" name="lieu_depart" class="form-control" value="<?= htmlspecialchars($covoiturage->getLieuDepart()) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Destination</label>
        <input type="text" name="destination" class="form-control" value="<?= htmlspecialchars($covoiturage->getDestination()) ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Date</label>
        <input type="date" name="date" class="form-control" value="<?= $covoiturage->getDate() ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Heure de départ</label>
        <input type="time" name="heure_depart" class="form-control" value="<?= $covoiturage->getHeureDepart() ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Places disponibles</label>
        <input type="number" name="place_dispo" class="form-control" value="<?= $covoiturage->getPlaceDispo() ?>" min="1" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Prix</label>
        <input type="number" name="prix_c" class="form-control" step="0.01" value="<?= $covoiturage->getPrix() ?>" required>
      </div>

      <div class="mb-3">
        <label class="form-label">ID utilisateur</label>
        <input type="text" name="user_id" class="form-control" value="<?= $covoiturage->getIdUtilisateur() ?>" required>
      </div>

      <button type="submit" class="btn btn-primary">💾 Enregistrer les modifications</button>
      <a href="index.php?action=liste" class="btn btn-secondary">🔙 Retour</a>
    </form>
  <?php else: ?>
    <div class="alert alert-danger">❌ Covoiturage non trouvé.</div>
  <?php endif; ?>

</body>
</html>
