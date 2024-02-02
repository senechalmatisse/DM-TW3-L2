<!DOCTYPE html>
<html lang="fr">
<head>
  <title><?php echo $titrePage;?></title>
  <meta charset="UTF-8">
  <meta name="author" content="22005394" />
  <link rel="stylesheet" href="style/style.css"/>
  <link rel="stylesheet" href="style/<?php echo $feuilleStylePage;?>.css"/>
</head>
<body>
  <main>
    <h1 class="accueil"><?php echo $titrePage ?></h1>
    <hr>
    <table id="table">
      <tr>
      <td id="affichage"><?php echo $zonePrincipale;?></td>
      <td id="liens">
        <ul id="accueil">
          <li><a href="index.php?action=inserer">Prendre votre réservation</a></li>
          <li><a href="index.php?action=liste">Liste des réservations</a></li>
          <li class="retour"><a href="index.php">Revenir à l'accueil</a></li>
          <li class="details"><a href="index.php?action=details">À propos</a></li>
        </ul>
      </td>
    </tr>
    </table>
  </main>
</body>
</html>
