<?php
// liste.php

// 1) Connexion PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname=lets_link;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// 2) TRAITEMENTS CRUD

// 2.1) Modifier un partenaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_submit'])) {
    $stmt = $conn->prepare("
        UPDATE partenaire SET
            nomP         = :nomP,
            typeP        = :typeP,
            emailP       = :emailP,
            adresseP     = :adresseP,
            numP         = :numP,
            site_web     = :site_web,
            descriptionP = :descriptionP,
            photoP       = :photoP
        WHERE idP = :idP
    ");
    $stmt->execute([
        ':nomP'        => $_POST['nomP'],
        ':typeP'       => $_POST['typeP'],
        ':emailP'      => $_POST['emailP'],
        ':adresseP'    => $_POST['adresseP'],
        ':numP'        => $_POST['numP'],
        ':site_web'    => $_POST['site_web'],
        ':descriptionP'=> $_POST['descriptionP'],
        ':photoP'      => $_POST['photoP'],
        ':idP'         => intval($_POST['idP'])
    ]);
    header("Location: liste.php");
    exit;
}

// 2.2) Ajouter un partenaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_submit'])) {
    $stmt = $conn->prepare("
        INSERT INTO partenaire 
            (nomP, typeP, emailP, adresseP, numP, site_web, descriptionP, photoP)
        VALUES 
            (:nomP, :typeP, :emailP, :adresseP, :numP, :site_web, :descriptionP, :photoP)
    ");
    $stmt->execute([
        ':nomP'        => $_POST['nomP'],
        ':typeP'       => $_POST['typeP'],
        ':emailP'      => $_POST['emailP'],
        ':adresseP'    => $_POST['adresseP'],
        ':numP'        => $_POST['numP'],
        ':site_web'    => $_POST['site_web'],
        ':descriptionP'=> $_POST['descriptionP'],
        ':photoP'      => $_POST['photoP']
    ]);
    header("Location: liste.php");
    exit;
}

// 2.3) Supprimer un partenaire et ses offres
if (isset($_GET['supprimer'])) {
    $id = intval($_GET['supprimer']);
    $conn->beginTransaction();
    try {
        $conn->prepare("DELETE FROM offres WHERE idP = :id")->execute([':id'=>$id]);
        $conn->prepare("DELETE FROM partenaire WHERE idP = :id")->execute([':id'=>$id]);
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollBack();
        echo "Erreur suppression : ".$e->getMessage();
    }
    header("Location: liste.php");
    exit;
}

// 2.4) Ajouter une offre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_offre_submit'])) {
    $stmt = $conn->prepare("
        INSERT INTO offres 
          (idP, typeOffre, descriptionOffre, discount, dateDebut, dateFin,
           is_flash, start_time, end_time)
        VALUES 
          (:idP, :typeOffre, :descriptionOffre, :discount, :dateDebut, :dateFin,
           :is_flash, :start_time, :end_time)
    ");
    $stmt->execute([
        ':idP'              => intval($_POST['idP']),
        ':typeOffre'        => $_POST['typeOffre'],
        ':descriptionOffre' => $_POST['descriptionOffre'],
        ':discount'         => $_POST['discount'],
        ':dateDebut'        => $_POST['dateDebut'],
        ':dateFin'          => $_POST['dateFin'],
        ':is_flash'         => isset($_POST['is_flash']) ? 1 : 0,
        ':start_time'       => $_POST['start_time'] ?: null,
        ':end_time'         => $_POST['end_time']   ?: null,
    ]);
    header("Location: liste.php");
    exit;
}

// 2.5) Modifier une offre
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['modifier_offre_submit'])) {
    $stmt = $conn->prepare("
        UPDATE offres SET
            typeOffre        = :typeOffre,
            descriptionOffre = :descriptionOffre,
            discount         = :discount,
            dateDebut        = :dateDebut,
            dateFin          = :dateFin,
            is_flash         = :is_flash,
            start_time       = :start_time,
            end_time         = :end_time
        WHERE idOffre = :idOffre
    ");
    $stmt->execute([
        ':typeOffre'        => $_POST['typeOffre'],
        ':descriptionOffre' => $_POST['descriptionOffre'],
        ':discount'         => $_POST['discount'],
        ':dateDebut'        => $_POST['dateDebut'],
        ':dateFin'          => $_POST['dateFin'],
        ':is_flash'         => isset($_POST['is_flash']) ? 1 : 0,
        ':start_time'       => $_POST['start_time'] ?: null,
        ':end_time'         => $_POST['end_time']   ?: null,
        ':idOffre'          => intval($_POST['idOffre'])
    ]);
    header("Location: liste.php");
    exit;
}

// 2.6) Supprimer une offre
if (isset($_GET['supprimer_offre'])) {
    $idO = intval($_GET['supprimer_offre']);
    $conn->prepare("DELETE FROM offres WHERE idOffre = :idOffre")
         ->execute([':idOffre'=>$idO]);
    header("Location: liste.php");
    exit;
}

// 3) Données pour modals offres
$offreAEdit = null;
if (isset($_GET['modifier_offre'])) {
    $stmt = $conn->prepare("SELECT * FROM offres WHERE idOffre = :idOffre");
    $stmt->execute([':idOffre'=>intval($_GET['modifier_offre'])]);
    $offreAEdit = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Gestion des Partenaires</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- SB Admin 2 + Bootstrap -->
  <link href="../vendor/fontawesome-free/css/all.min.css" rel="stylesheet">
  <link href="../css/sb-admin-2.min.css" rel="stylesheet">
  <style>
    .blur { filter: blur(5px); }
  </style>
</head>
<body id="page-top">
<div id="wrapper">
  <!-- Sidebar -->
  <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="../index.php">
      <div class="sidebar-brand-icon"><i class="fas fa-handshake"></i></div>
      <div class="sidebar-brand-text mx-3">Let's Link</div>
    </a>
    <hr class="sidebar-divider my-0">
    <li class="nav-item">
      <a class="nav-link" href="../index.php">
        <i class="fas fa-fw fa-tachometer-alt"></i><span>Tableau de bord</span>
      </a>
    </li>
    <hr class="sidebar-divider">
    <div class="sidebar-heading">Gestion</div>
    <li class="nav-item">
      <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePartenaires"
         aria-expanded="true" aria-controls="collapsePartenaires">
        <i class="fas fa-fw fa-handshake"></i><span>Partenaires</span>
      </a>
      <div id="collapsePartenaires" class="collapse" aria-labelledby="headingPartenaires"
           data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
          <h6 class="collapse-header">Gestion des Partenaires :</h6>
          <a class="collapse-item" href="liste.php">Liste des partenaires</a>
          <a class="collapse-item" href="demandes.php">Demandes</a>
        </div>
      </div>
    </li>
    <hr class="sidebar-divider d-none d-md-block">
    <div class="text-center d-none d-md-inline">
      <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>
  </ul>
  <!-- End of Sidebar -->

  <!-- Content -->
  <div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
      <div class="container-fluid pt-4">
        <h1 class="h3 mb-4 text-gray-800">Gestion des Partenaires</h1>
        <!-- Recherche -->
        <input type="text" id="recherche" class="form-control mb-3" placeholder="Rechercher un partenaire...">
        <!-- Ajouter Partenaire -->
        <button class="btn btn-success mb-3" data-toggle="modal" data-target="#modalAjoutP">
          <i class="fas fa-plus"></i> Ajouter Partenaire
        </button>

        <!-- Tableau -->
        <div id="table-container">
          <table class="table table-bordered">
            <thead class="thead-light">
              <tr>
                <th>Nom</th><th>Téléphone</th><th>Photo</th><th>Site web</th><th>Offres</th><th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $rs = $conn->query("SELECT * FROM partenaire");
              while ($row = $rs->fetch(PDO::FETCH_ASSOC)):
              ?>
              <tr>
                <td><?=htmlspecialchars($row['nomP'])?></td>
                <td><?=htmlspecialchars($row['numP'])?></td>
                <td><img src="<?=htmlspecialchars($row['photoP'])?>" width="50" height="50"></td>
                <td><a href="<?=htmlspecialchars($row['site_web'])?>" target="_blank">
                  <?=htmlspecialchars($row['site_web'])?></a></td>
                <td>
                  <?php
                  $ofs = $conn->prepare("SELECT * FROM offres WHERE idP = ?");
                  $ofs->execute([$row['idP']]);
                  while ($o = $ofs->fetch(PDO::FETCH_ASSOC)):
                  ?>
                  <div class="mb-2">
                    <strong><?=$o['typeOffre']?></strong><br>
                    <?=$o['descriptionOffre']?><br>
                    <?=$o['discount']?>% (<?=$o['dateDebut']?> → <?=$o['dateFin']?>)
                    <?php if ($o['is_flash']): ?>
                      <span class="badge badge-warning">Flash Deal</span>
                      <small>(<?=substr($o['start_time'],0,16)?> → <?=substr($o['end_time'],0,16)?>)</small>
                    <?php endif; ?>
                    <a href="?supprimer_offre=<?=$o['idOffre']?>" class="text-danger ml-2"
                       onclick="return confirm('Supprimer cette offre ?')">&times;</a>
                    <button class="btn btn-sm btn-warning btn-edit-offre ml-2"
                      data-idoffre="<?=$o['idOffre']?>" 
                      data-typeoffre="<?=htmlspecialchars($o['typeOffre'],ENT_QUOTES)?>"
                      data-descoffre="<?=htmlspecialchars($o['descriptionOffre'],ENT_QUOTES)?>"
                      data-discount="<?=$o['discount']?>"
                      data-datedebut="<?=$o['dateDebut']?>"
                      data-datefin="<?=$o['dateFin']?>"
                      data-is_flash="<?=$o['is_flash']?>"
                      data-start_time="<?=$o['start_time']?>"
                      data-end_time="<?=$o['end_time']?>">
                      ✎
                    </button>
                  </div>
                  <?php endwhile; ?>
                  <button class="btn btn-sm btn-info btn-add-offre mt-1" data-idp="<?=$row['idP']?>">
                    + Offre
                  </button>
                </td>
                <td>
                  <button class="btn btn-sm btn-outline-primary btn-edit-partenaire"
                    data-id="<?=$row['idP']?>" 
                    data-nom="<?=htmlspecialchars($row['nomP'],ENT_QUOTES)?>"
                    data-type="<?=htmlspecialchars($row['typeP'],ENT_QUOTES)?>"
                    data-email="<?=htmlspecialchars($row['emailP'],ENT_QUOTES)?>"
                    data-adresse="<?=htmlspecialchars($row['adresseP'],ENT_QUOTES)?>"
                    data-num="<?=htmlspecialchars($row['numP'],ENT_QUOTES)?>"
                    data-site="<?=htmlspecialchars($row['site_web'],ENT_QUOTES)?>"
                    data-desc="<?=htmlspecialchars($row['descriptionP'],ENT_QUOTES)?>"
                    data-photo="<?=htmlspecialchars($row['photoP'],ENT_QUOTES)?>">
                    <i class="fas fa-edit"></i>
                  </button>
                  <a href="?supprimer=<?=$row['idP']?>" class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Supprimer ce partenaire et ses offres ?')">
                    <i class="fas fa-trash"></i>
                  </a>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL Ajouter Partenaire -->
<div class="modal fade" id="modalAjoutP" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ajouter Partenaire</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <?php function champ($name,$label){ 
          printf('<div class="form-group"><label>%s</label><input name="%s" class="form-control"></div>',$label,$name);
        }
        champ('nomP','Nom'); champ('typeP','Type'); champ('emailP','Email');
        champ('adresseP','Adresse'); champ('numP','Téléphone'); champ('site_web','Site Web');
        printf('<div class="form-group"><label>Description</label><textarea name="descriptionP" class="form-control"></textarea></div>');
        champ('photoP','Photo (URL)');
        ?>
        <hr>
        <h6>Offre associée (facultatif)</h6>
        <div class="form-row">
          <div class="col"><input name="typeOffre" class="form-control" placeholder="Type"></div>
          <div class="col"><input name="descriptionOffre" class="form-control" placeholder="Description"></div>
        </div>
        <div class="form-row mt-2">
          <div class="col"><input name="discount" type="number" class="form-control" placeholder="%"></div>
          <div class="col"><input name="dateDebut" type="date" class="form-control"></div>
          <div class="col"><input name="dateFin" type="date" class="form-control"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="ajouter_submit" class="btn btn-success">Ajouter</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL Modifier Partenaire -->
<div class="modal fade" id="modalModifP" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modifier Partenaire</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="idP" id="modif-idP">
        <?php function champModif($name,$label){ 
          printf('<div class="form-group"><label>%s</label><input name="%s" id="modif-%s" class="form-control"></div>',
                 $label,$name,$name);
        }
        champModif('nomP','Nom'); champModif('typeP','Type'); champModif('emailP','Email');
        champModif('adresseP','Adresse'); champModif('numP','Téléphone'); champModif('site_web','Site Web');
        printf('<div class="form-group"><label>Description</label><textarea name="descriptionP" id="modif-descriptionP" class="form-control"></textarea></div>');
        champModif('photoP','Photo (URL)');?>
      </div>
      <div class="modal-footer">
        <button type="submit" name="modifier_submit" class="btn btn-primary">Enregistrer</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL Ajouter Offre -->
<div class="modal fade" id="modalOffreAdd" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Ajouter Offre</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="idP" id="add-offre-idP">
        <div class="form-group"><label>Type d'offre</label><input name="typeOffre" id="add-typeOffre" class="form-control"></div>
        <div class="form-group"><label>Description</label><input name="descriptionOffre" id="add-descriptionOffre" class="form-control"></div>
        <div class="form-group form-check">
          <input type="checkbox" class="form-check-input" id="add-is_flash" name="is_flash" value="1">
          <label class="form-check-label" for="add-is_flash">Flash Deal ?</label>
        </div>
        <div id="add-flash_fields" style="display:none;">
          <div class="form-group">
            <label for="add-start_time">Date & heure début</label>
            <input type="datetime-local" class="form-control" id="add-start_time" name="start_time">
          </div>
          <div class="form-group">
            <label for="add-end_time">Date & heure fin</label>
            <input type="datetime-local" class="form-control" id="add-end_time" name="end_time">
          </div>
        </div>
        <div class="form-row">
          <div class="col"><label>% Réduction</label><input name="discount" id="add-discount" type="number" class="form-control"></div>
          <div class="col"><label>Date début</label><input name="dateDebut" id="add-dateDebut" type="date" class="form-control"></div>
          <div class="col"><label>Date fin</label><input name="dateFin" id="add-dateFin" type="date" class="form-control"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="ajouter_offre_submit" class="btn btn-success">Ajouter Offre</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL Modifier Offre -->
<div class="modal fade" id="modalOffreEdit" tabindex="-1">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Modifier Offre</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="idOffre" id="edit-offre-idOffre">
        <div class="form-group"><label>Type d'offre</label><input name="typeOffre" id="edit-typeOffre" class="form-control"></div>
        <div class="form-group"><label>Description</label><input name="descriptionOffre" id="edit-descriptionOffre" class="form-control"></div>
        <div class="form-group form-check">
          <input type="checkbox" class="form-check-input" id="edit-is_flash" name="is_flash" value="1">
          <label class="form-check-label" for="edit-is_flash">Flash Deal ?</label>
        </div>
        <div id="edit-flash_fields" style="display:none;">
          <div class="form-group">
            <label for="edit-start_time">Date & heure début</label>
            <input type="datetime-local" class="form-control" id="edit-start_time" name="start_time">
          </div>
          <div class="form-group">
            <label for="edit-end_time">Date & heure fin</label>
            <input type="datetime-local" class="form-control" id="edit-end_time" name="end_time">
          </div>
        </div>
        <div class="form-row">
          <div class="col"><label>% Réduction</label><input name="discount" id="edit-discount" type="number" class="form-control"></div>
          <div class="col"><label>Date début</label><input name="dateDebut" id="edit-dateDebut" type="date" class="form-control"></div>
          <div class="col"><label>Date fin</label><input name="dateFin" id="edit-dateFin" type="date" class="form-control"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" name="modifier_offre_submit" class="btn btn-primary">Enregistrer Offre</button>
      </div>
    </form>
  </div>
</div>

<!-- SCRIPTS -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script>
  // Flou du fond lors des modales
  $('#modalAjoutP, #modalModifP, #modalOffreAdd, #modalOffreEdit')
    .on('show.bs.modal', ()=> $('#table-container').addClass('blur'))
    .on('hide.bs.modal', ()=> $('#table-container').removeClass('blur'));

  // Remplissage modif partenaire
  $('.btn-edit-partenaire').click(function(){
    $('#modif-idP').val(this.dataset.id);
    $('#modif-nomP').val(this.dataset.nom);
    $('#modif-typeP').val(this.dataset.type);
    $('#modif-emailP').val(this.dataset.email);
    $('#modif-adresseP').val(this.dataset.adresse);
    $('#modif-numP').val(this.dataset.num);
    $('#modif-site_web').val(this.dataset.site);
    $('#modif-descriptionP').val(this.dataset.desc);
    $('#modif-photoP').val(this.dataset.photo);
    $('#modalModifP').modal('show');
  });

  // Ouverture ajout offre
  $('.btn-add-offre').click(function(){
    $('#add-offre-idP').val(this.dataset.idp);
    $('#add-typeOffre, #add-descriptionOffre, #add-discount, #add-dateDebut, #add-dateFin, #add-start_time, #add-end_time').val('');
    $('#add-is_flash').prop('checked', false);
    $('#add-flash_fields').hide();
    $('#modalOffreAdd').modal('show');
  });

  // Remplissage modif offre
  $('.btn-edit-offre').click(function(){
    $('#edit-offre-idOffre').val(this.dataset.idoffre);
    $('#edit-typeOffre').val(this.dataset.typeoffre);
    $('#edit-descriptionOffre').val(this.dataset.descoffre);
    $('#edit-discount').val(this.dataset.discount);
    $('#edit-dateDebut').val(this.dataset.datedebut);
    $('#edit-dateFin').val(this.dataset.datefin);

    // gestion Flash Deal
    let isFlash = this.dataset.is_flash === '1';
    $('#edit-is_flash').prop('checked', isFlash).trigger('change');
    if (isFlash) {
      $('#edit-start_time').val(this.dataset.start_time);
      $('#edit-end_time').val(this.dataset.end_time);
    }
    $('#modalOffreEdit').modal('show');
  });

  // Toggle champs date/heure pour Ajouter
  $('#add-is_flash').on('change', function(){
    $('#add-flash_fields').toggle(this.checked);
  });
  // Toggle champs date/heure pour Modifier
  $('#edit-is_flash').on('change', function(){
    $('#edit-flash_fields').toggle(this.checked);
  });

  // Recherche live
  $('#recherche').on('input', function(){
    let f = this.value.toLowerCase();
    $('table tbody tr').each(function(){
      $(this).toggle($(this).text().toLowerCase().includes(f));
    });
  });

  // Validation ajout partenaire (au moins 2 champs)
  $('#modalAjoutP form').on('submit', function(e) {
    let filled = 0;
    $(this).find('input[type="text"], textarea').each(function() {
      if ($(this).val().trim() !== '') filled++;
    });
    if (filled < 2) {
      e.preventDefault();
      $('#modalAjoutP .js-error').remove();
      $('#modalAjoutP .modal-body').prepend(`
        <div class="alert alert-danger js-error">
          Veuillez remplir au moins deux champs.
        </div>
      `);
    }
  });
</script>
</body>
</html>
