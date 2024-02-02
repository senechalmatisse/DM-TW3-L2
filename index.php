<?php
require_once("config/Lib.php");
$action = key_exists('action', $_GET)? trim($_GET['action']): null;
$corps = "";
$titrePage = "Accueil";
$feuilleStylePage = "screen";
$tabDesVisiteurs = array();
switch ($action) {
	// Partie insère un nouveau visiteur
	case "inserer":
		$feuilleStylePage = "form";
		$cible = 'inserer';
		if (!isset($_POST["NomV"]) || !isset($_POST["PrenomV"]) || !isset($_POST["DateN"]) || !isset($_POST["TelephoneV"]) || !isset($_POST["ExpositionV"]))
			include("squel/fragment/formulaireVisiteur.html");
		else {
			$nom = key_exists('NomV', $_POST)? trim($_POST['NomV']): null;
			$prenom = key_exists('PrenomV', $_POST)? trim($_POST['PrenomV']): null;
			$dateN = key_exists('DateN', $_POST)? trim($_POST['DateN']): null;
			$telephone = key_exists('TelephoneV', $_POST)? trim($_POST['TelephoneV']): null;
			$exposition = key_exists('ExpositionV', $_POST)? trim($_POST['ExpositionV']): null;
			if (($nom == null) || (!nomPrenomValide($nom)))
				$erreur["nom"] = "Le nom n'est pas valide";
			if (($prenom == null) || (!nomPrenomValide($prenom)))
				$erreur["prenom"] = "Le prenom n'est pas valide";
			if (($dateN == null) || (!controlerDate($dateN)) || (!anneeValide($dateN)))
				$erreur["dateN"] = "La date de naissance n'est pas valide";
			if (($telephone == null) || (!controlerTel($telephone)))
				$erreur["telephone"] = "Le numéro de téléphone n'est pas valide";
			if($exposition == null)
				$erreur["expostion"] = "Cette exposition n'existe pas";
			$compteur_erreur = count($erreur);
			foreach ($erreur as $cle => $valeur) {
				if ($valeur == null)
					$compteur_erreur -= 1;
			}
			if ($compteur_erreur == 0) {
				$connection = connecter();
				//Ajout à la base de données
				$rqAjoutVisiteur = "INSERT INTO `VISITEUR` (`NomV`,`PrenomV`,`DateN`,`TelephoneV`,`ExpositionV`) VALUES (:nom, :prenom, :dateN, :telephone, :exposition)";
				$stmtAjoutVisiteur = $connection->prepare($rqAjoutVisiteur);
				/*** remplissage des paramètres ***/
				$donnees = array(
					':nom' => $nom,
					':prenom' => $prenom,
					':dateN' => changeFormatDate($dateN),
					':telephone' => $telephone,
					':exposition' => $exposition
				);
				/*** exécution du statement ***/
				$stmtAjoutVisiteur->execute($donnees);
				$rqRecupVisiteur = "SELECT IdV FROM `VISITEUR` WHERE NomV=:nom AND PrenomV=:prenom AND DateN=:dateN AND TelephoneV=:telephone AND ExpositionV=:exposition";
				$stmtRecupVisiteur = $connection->prepare($rqRecupVisiteur);
				/*** exécution du statement ***/
				$stmtRecupVisiteur->execute($donnees);
				$stmtRecupVisiteur->setFetchMode(PDO::FETCH_OBJ);
				$resultat = $stmtRecupVisiteur->fetch();

				$visiteur = new Visiteur($resultat->IdV, $nom, $prenom, $dateN, $telephone, $exposition);
				$corps = $visiteur->ajoutVisiteur();
				ajouterVisiteur($tabDesVisiteurs, $visiteur->getIdV());
				$connection = null;				
			}
			else
				include("squel/fragment/formulaireVisiteur.html");
		}
		$zonePrincipale = $corps;
		$titrePage = "Réservations";
		break;
	// Partie met à jour les informations d'un visiteur
	case "update":
		$cible = 'update';
		$IdV = key_exists('IdV', $_GET)? trim($_GET['IdV']): null;
		if($IdV == null || !is_numeric($IdV)) {
			$titrePage = "ERREUR";
		} else {
			$titrePage = "Modification visiteur";
			$connection = connecter();
			$rq = "SELECT * FROM VISITEUR WHERE IdV = :idV";
			$stmt = $connection->prepare($rq);
			$data = array(":idV" => $IdV);
			$stmt->execute($data);
			$stmt->setFetchMode(PDO::FETCH_OBJ);
			$result = $stmt->fetch();

        	$nom = $result->NomV;
			$prenom = $result->PrenomV;
        	$dateN = changeFormatDate($result->DateN);
			$telephone = $result->TelephoneV;
			$exposition = $result->ExpositionV;
			$connection = null;
			if (!isset($_POST["NomV"]) || !isset($_POST["PrenomV"]) || !isset($_POST["DateN"]) || !isset($_POST["TelephoneV"]) || !isset($_POST["ExpositionV"])) {
				$feuilleStylePage = "form";
				$cible = "update&IdV=$IdV";
				include("squel/fragment/formulaireVisiteur.html");
			} else {
				$nom = key_exists('NomV', $_POST)? trim($_POST['NomV']): null;
				$prenom = key_exists('PrenomV', $_POST)? trim($_POST['PrenomV']): null;
				$dateN = key_exists('DateN', $_POST)? trim($_POST['DateN']): null;
				$telephone = key_exists('TelephoneV', $_POST)? trim($_POST['TelephoneV']): null;
				$exposition = key_exists('ExpositionV', $_POST)? trim($_POST['ExpositionV']): null;
				if (($nom == null) || (!nomPrenomValide($nom)))
					$erreur["nom"] = "Le nom n'est pas valide";
				if (($prenom == null) || (!nomPrenomValide($prenom)))
					$erreur["prenom"] = "Le prenom n'est pas valide";
				if (($dateN == null) || (!controlerDate($dateN)) || (!anneeValide($dateN)))
					$erreur["dateN"] = "La date de naissance n'est pas valide";
				if (($telephone == null) || (!controlerTel($telephone)))
					$erreur["telephone"] = "Le numéro de téléphone n'est pas valide";
				if ($exposition == null)
					$erreur["expostion"] = "Cette exposition n'existe pas";
				$compteur_erreur = count($erreur);
				foreach ($erreur as $cle => $valeur) {
					if ($valeur == null)
						$compteur_erreur -= 1;
				}
				if($compteur_erreur == 0) {
					$sql = "UPDATE VISITEUR SET NomV=:nom, PrenomV=:prenom, DateN=:dateN, TelephoneV=:telephone, ExpositionV=:exposition WHERE IdV=:idV";
					include("squel/fragment/formulaireModification.html");
					$feuilleStylePage = "suppression";
				}
				else {
					$cible = "update&IdV=$IdV";
					$feuilleStylePage = "form";
					include("squel/fragment/formulaireVisiteur.html");
				}
			}
		}
		$zonePrincipale = $corps;
		break;
	// Partie récupère les informations de tous les visiteurs
	case "liste":
		$titrePage = "Liste des réservations";
		$cible = 'liste';
		$connection = connecter();

		$corps = <<<EOT
		<article>
			<div id="references">
				<h4>
					<span class='c1'>
						<b><u>IdV</u></b>
					</span>
					<span class='c1'>
						<a href="index.php?action=groupe&groupe=nom"><b>NomV</b></a>
					</span>
					<span class='c1'>
						<b>PrenomV</b>
					</span>
					<span class='c1'>
						<b>DateN</b>
					</span>
					<span class='c1'>
						<b>TelephoneV</b>
					</span>
					<span class='c1'>
						<a href="index.php?action=groupe&groupe=exposition"><b>ExpositionV</b></a>
					</span>
					<span class='c1'>
						<b>Actions</b>
					</span>
				</h4>
			</div>
		EOT;

		// Récupère le nombre total de visiteurs de la table `VISITEUR`.
		$rqNbVisiteur = "SELECT COUNT(*) AS nb_pages FROM `VISITEUR`";
		$stmtNbVisiteur = $connection->query($rqNbVisiteur);
		$stmtNbVisiteur->setFetchMode(PDO::FETCH_OBJ);
		
		// Récupère le nombre total d'expositions que l'on veut de la table `VISITEUR`.
		$nbPages = $stmtNbVisiteur->fetch()->nb_pages;
		// Vérifie si le numéro de la page est spécifié et vérifie si c'est un nombre,
		// si ce n'est pas le cas on retourne le numéro de la page par défaut qui est 1.
		$page = key_exists('page', $_GET) && is_numeric($_GET['page']) ? trim($_GET['page']): 1;
		// Nombre de pages que l'on veut afficher pour chaque page.
		$nbIndex = 5;
		if ($stmtRecupInfos = $connection->prepare('SELECT * FROM `VISITEUR` ORDER BY IdV LIMIT ?, ?')) {
			// Calcule la page à récupérer pour avoir les résultats dont on a besoin.
			$pageARecup = ($page - 1) * $nbIndex;
			$stmtRecupInfos->bindParam(1, $pageARecup, PDO::PARAM_INT);
			$stmtRecupInfos->bindParam(2, $nbIndex, PDO::PARAM_INT);
			$stmtRecupInfos->execute(); 
			// Récupère les resultats
			$stmtRecupInfos->setFetchMode(PDO::FETCH_OBJ);
			while ($enregistrement = $stmtRecupInfos->fetch()) {
				$date = changeFormatDate($enregistrement->DateN);
				$corps .= <<<EOT
				<div class="infos">
					<span class="c1">
						<b><u>$enregistrement->IdV</u></b>
					</span>
					<span class="c1">$enregistrement->NomV</span>
					<span class="c1">$enregistrement->PrenomV</span>
					<span class="c1">$date</span>
					<span class="c1">$enregistrement->TelephoneV</span>
					<span class="c1">$enregistrement->ExpositionV</span>
					<span>
						<a href="index.php?action=select&IdV=$enregistrement->IdV"><img src="style/images/icones/oeil.png" alt="https://www.flaticon.com/fr/icones-gratuites/london-eye"></a>
					</span>
					<span>
						<a href="index.php?action=update&IdV=$enregistrement->IdV"><img src="style/images/icones/crayon.png" alt="https://www.flaticon.com/fr/icones-gratuites/crayon"></a>
					</span>
					<span>
						<a href="index.php?action=supprimer&IdV=$enregistrement->IdV"><img src="style/images/icones/supprimer.png" alt="https://www.flaticon.com/fr/icones-gratuites/effacer"></a>
					</span>
					<br>
				</div>
				EOT;
			}
			if (ceil($nbPages / $nbIndex) > 0)
				$corps .= renvoiePages($nbPages, $nbIndex, $page, "liste");
			$corps .= "</article>";
		}
		$feuilleStylePage = "liste";
		$zonePrincipale = $corps;
		$query = null;
		$connection = null;
		break;
	// Partie regroupe les visiteurs par exposition
	case "groupe":
		$groupe = key_exists('groupe', $_GET)? trim($_GET['groupe']): null;
		if($groupe == null) {
			$titrePage = "ERREUR";
		} else {
			$connection = connecter();
			switch($groupe) {
				case 'exposition':
					$cible = 'liste';
					$titrePage = "Liste des réservations";
					$regroupement1 = "groupe&groupe=nom";
					$regroupement2 = "liste";
					$corps = <<<EOT
					<article>
						<div id="references">
							<h4>
								<span class='c1'>
									<b><u>IdV</u></b>
								</span>
								<span class='c1'>
									<a href="index.php?action=$regroupement1"><b>NomV</b></a>
								</span>
								<span class='c1'>
									<b>PrenomV</b>
								</span>
								<span class='c1'>
									<b>DateN</b>
								</span>
								<span class='c1'>
									<b>TelephoneV</b>
								</span>
								<span class='c1'>
									<a href="index.php?action=$regroupement2"><b>ExpositionV</b></a>
								</span>
								<span class='c1'>
									<b>Actions</b>
								</span>
							</h4>
						</div>
					EOT;
					// Récupère le nombre total de visiteurs de la table `VISITEUR`.
					$rqNbVisiteur = "SELECT COUNT(*) AS nb_pages FROM `VISITEUR`";
					$stmtNbVisiteur = $connection->query($rqNbVisiteur);
					$stmtNbVisiteur->setFetchMode(PDO::FETCH_OBJ);
					// Récupère le nombre total d'expositions que l'on veut de la table `VISITEUR`.
					$nbPages = $stmtNbVisiteur->fetch()->nb_pages;
					// Vérifie si le numéro de la page est spécifié et vérifie si c'est un nombre,
					// si ce n'est pas le cas on retourne le numéro de la page par défaut qui est 1.
					$page = key_exists('page', $_GET) && is_numeric($_GET['page']) ? trim($_GET['page']): 1;
					// Nombre de pages que l'on veut afficher pour chaque page.
					$nbIndex = 5;
					$stmtRecupInfos = $connection->prepare('SELECT * FROM `VISITEUR` ORDER BY ExpositionV LIMIT ?, ?');
					// Calcule la page à récupérer pour avoir les résultats dont on a besoin
					$pageARecup = ($page - 1) * $nbIndex;
					$stmtRecupInfos->bindParam(1, $pageARecup, PDO::PARAM_INT);
					$stmtRecupInfos->bindParam(2, $nbIndex, PDO::PARAM_INT);
					$stmtRecupInfos->execute(); 
					// Récupère les resultats
					$stmtRecupInfos->setFetchMode(PDO::FETCH_OBJ);
					while ($enregistrement = $stmtRecupInfos->fetch()) {
						$date = changeFormatDate($enregistrement->DateN);
						$corps .= <<<EOT
						<div class="infos">
							<span class="c1">
								<b><u>$enregistrement->IdV</u></b>
							</span>
							<span class="c1">$enregistrement->NomV</span>
							<span class="c1">$enregistrement->PrenomV</span>
							<span class="c1">$date</span>
							<span class="c1">$enregistrement->TelephoneV</span>
							<span class="c1">$enregistrement->ExpositionV</span>
							<span>
								<a href="index.php?action=select&IdV=$enregistrement->IdV"><img src="style/images/icones/oeil.png" alt="https://www.flaticon.com/fr/icones-gratuites/london-eye"></a>
							</span>
							<span>
								<a href="index.php?action=update&IdV=$enregistrement->IdV"><img src="style/images/icones/crayon.png" alt="https://www.flaticon.com/fr/icones-gratuites/crayon"></a>
							</span>
							<span>
								<a href="index.php?action=supprimer&IdV=$enregistrement->IdV"><img src="style/images/icones/supprimer.png" alt="https://www.flaticon.com/fr/icones-gratuites/effacer"></a>
							</span>
							<br>
						</div>
						EOT;
					}
					if (ceil($nbPages / $nbIndex) > 0)
						$corps .= renvoiePages($nbPages, $nbIndex, $page);
					$corps .= "</article>";
					break;
				case 'nom':
					$cible = 'liste';
					$titrePage = "Liste des réservations";
					$regroupement1 = "liste";
					$regroupement2 = "groupe&groupe=exposition";
					$corps = <<<EOT
					<article>
						<div id="references">
							<h4>
								<span class='c1'>
									<b><u>IdV</u></b>
								</span>
								<span class='c1'>
									<a href="index.php?action=$regroupement1"><b>NomV</b></a>
								</span>
								<span class='c1'>
									<b>PrenomV</b>
								</span>
								<span class='c1'>
									<b>DateN</b>
								</span>
								<span class='c1'>
									<b>TelephoneV</b>
								</span>
								<span class='c1'>
									<a href="index.php?action=$regroupement2"><b>ExpositionV</b></a>
								</span>
								<span class='c1'>
									<b>Actions</b>
								</span>
							</h4>
						</div>
					EOT;
					// Récupère le nombre total de visiteurs de la table `VISITEUR`.
					$rqNbVisiteur = "SELECT COUNT(*) AS nb_pages FROM `VISITEUR`";
					$stmtNbVisiteur = $connection->query($rqNbVisiteur);
					$stmtNbVisiteur->setFetchMode(PDO::FETCH_OBJ);
					// Récupère le nombre total d'expositions que l'on veut de la table `VISITEUR`.
					$nbPages = $stmtNbVisiteur->fetch()->nb_pages;
					// Vérifie si le numéro de la page est spécifié et vérifie si c'est un nombre,
					// si ce n'est pas le cas on retourne le numéro de la page par défaut qui est 1.
					$page = key_exists('page', $_GET) && is_numeric($_GET['page']) ? trim($_GET['page']): 1;
					// Nombre de pages que l'on veut afficher pour chaque page.
					$nbIndex = 5;
					$stmtRecupInfos = $connection->prepare('SELECT * FROM `VISITEUR` ORDER BY NomV, PrenomV LIMIT ?, ?');
					// Calcule la page à récupérer pour avoir les résultats dont on a besoin
					$pageARecup = ($page - 1) * $nbIndex;
					$stmtRecupInfos->bindParam(1, $pageARecup, PDO::PARAM_INT);
					$stmtRecupInfos->bindParam(2, $nbIndex, PDO::PARAM_INT);
					$stmtRecupInfos->execute(); 
					// Récupère les resultats
					$stmtRecupInfos->setFetchMode(PDO::FETCH_OBJ);
					while ($enregistrement = $stmtRecupInfos->fetch()) {
						$date = changeFormatDate($enregistrement->DateN);
						$corps .= <<<EOT
						<div class="infos">
							<span class="c1">
								<b><u>$enregistrement->IdV</u></b>
							</span>
							<span class="c1">$enregistrement->NomV</span>
							<span class="c1">$enregistrement->PrenomV</span>
							<span class="c1">$date</span>
							<span class="c1">$enregistrement->TelephoneV</span>
							<span class="c1">$enregistrement->ExpositionV</span>
							<span>
								<a href="index.php?action=select&IdV=$enregistrement->IdV"><img src="style/images/icones/oeil.png" alt="https://www.flaticon.com/fr/icones-gratuites/london-eye"></a>
							</span>
							<span>
								<a href="index.php?action=update&IdV=$enregistrement->IdV"><img src="style/images/icones/crayon.png" alt="https://www.flaticon.com/fr/icones-gratuites/crayon"></a>
							</span>
							<span>
								<a href="index.php?action=supprimer&IdV=$enregistrement->IdV"><img src="style/images/icones/supprimer.png" alt="https://www.flaticon.com/fr/icones-gratuites/effacer"></a>
							</span>
							<br>
						</div>
						EOT;
					}
					if (ceil($nbPages / $nbIndex) > 0) {
						$param = $action . "&groupe=" . $groupe;
						$corps .= renvoiePages($nbPages, $nbIndex, $page, $param);
					}
					$corps .= "</article>";
					break;
				default:
					$titrePage = "ERREUR";
					break;
			}	
			$feuilleStylePage = "liste";
			$query = null;
			$connection = null;
		}
		$zonePrincipale = $corps;
		break;
	// Partie récupère les informations d'un visiteur
	case "select":
		$cible = 'select';
		$IdV = key_exists('IdV', $_GET)? trim($_GET['IdV']): null;
		if($IdV !== null && is_numeric($IdV) == true) {
			$connection = connecter();
			$titrePage = "Informations sur le visiteur";

			/*** préparation ***/
			$rqRecupVisiteur = "SELECT * FROM VISITEUR WHERE IdV = :IdV";
			$stmtRecupVisiteur = $connection->prepare($rqRecupVisiteur);
			/*** remplissage des paramètres ***/
			$data = array(":IdV" => $IdV);
			/*** exécution du statement ***/
			$stmtRecupVisiteur->execute($data);
			/*** récupération du résultat ***/
			$stmtRecupVisiteur->setFetchMode(PDO::FETCH_OBJ);
			$resultat = $stmtRecupVisiteur->fetch();

			$date = changeFormatDate($resultat->DateN);
			$corps .= <<<EOT
			<div class="infos">
				<span class="c2">
					<h2><b><u>$resultat->IdV</u></b></h2>
				</span>
				<span class="c2">
					<h2>$resultat->NomV</h2>
				</span>
				<span class="c2">
					<h2>$resultat->PrenomV</h2>
				</span>
				<span class="c2">
					<h2>$date</h2>
				</span>
				<span class="c2">
					<h2>$resultat->TelephoneV</h2>
				</span>
				<span class="c2">
					<h2>$resultat->ExpositionV</h2>
				</span>
			</div>
			EOT;
			$connection = null;
		}
		$feuilleStylePage = "selection";
		$zonePrincipale=$corps;
		break;
	// Partie supprime un visiteur
	case "supprimer":
		$cible = 'supprimer';
		$IdV = key_exists('IdV', $_GET)? trim($_GET['IdV']): null;
		if($IdV != null && is_numeric($IdV) == true) {
			$sql = "DELETE FROM VISITEUR WHERE IdV = :IdV;
					ALTER TABLE VISITEUR AUTO_INCREMENT = 1;";
			include("squel/fragment/formulaireSuppression.html");
			$feuilleStylePage = "suppression";
			$titrePage = "Annulation visite";
		}
		else
			$titrePage = "ERREUR";
		break;
	// Partie confime la suppression/modification d'un visiteur
	case "sauvegarde":
		$cible = 'sauvegarde';
		if (isset($_POST["type"]) && isset($_POST["IdV"]) && isset($_POST["sql"])) {
			$type = key_exists('type', $_POST)? trim($_POST['type']): null;
			$IdV = key_exists('IdV', $_POST)? convertirGet(idValide(trim($_POST['IdV']))): null;
			$sql = key_exists('sql', $_POST)? trim($_POST['sql']): null;

			if($type == "confirmdelete") {
				$titrePage = "Annulation de la visite";
				$feuilleStylePage = "suppression";
				$connection = connecter();
				$stmtSupprimeVisiteur = $connection->prepare($sql);
				$donnees = array(":IdV" => $IdV);
				$stmtSupprimeVisiteur->execute($donnees);
				$corps .= <<<EOT
				<h2>Suppression du visiteur n°$IdV</h2>
				EOT;
				supprimerVisiteur($tabDesVisiteurs, $IdV);
			} else if($type == "confirmupdate") {
				$titrePage = "Modication visiteur";
				$feuilleStylePage = "suppression";
				$nom = key_exists('NomV', $_POST)? trim($_POST['NomV']): null;
				$prenom = key_exists('PrenomV', $_POST)? trim($_POST['PrenomV']): null;
				$dateN = key_exists('DateN', $_POST)? trim($_POST['DateN']): null;
				$telephone = key_exists('TelephoneV', $_POST)? trim($_POST['TelephoneV']): null;
				$exposition = key_exists('ExpositionV', $_POST)? trim($_POST['ExpositionV']): null;

				$connection = connecter();
				$stmtModifieVisiteur = $connection->prepare($sql);
				$donnees = array(
					":idV" => $IdV,
					":nom" => $nom,
					":prenom" => $prenom,
					":dateN" => changeFormatDate($dateN),
					":telephone" => $telephone,
					":exposition" => $exposition
				);
				$stmtModifieVisiteur->execute($donnees);
				$corps .= <<<EOT
				<h2>Modification du visiteur n°$IdV</h2>
				EOT;
				$visiteurModifie = new Visiteur($IdV, $nom, $prenom, $dateN, $telephone, $exposition);
				modifierVisiteur($tabDesVisiteurs, $IdV, $visiteurModifie);
			} else {
				$titrePage = "ERREUR";
			}
			$connection = null;
			$zonePrincipale = $corps;
		}
		break;
	// Partie affiche les informations, oevres d'un musée
	case "musee":
		$musee = key_exists("nomMusee", $_GET)? trim($_GET["nomMusee"]): null;
		if($musee !== null && verifieNomMusee($musee) == true) {
			$titrePage = "Exposition ". $musee;
			$connection = connecter();
					
			/*** préparation ***/
			$rqRecupInfosMusee = "SELECT * FROM MUSEE WHERE NomM = :NomM";
			$stmtRecupInfosMusee = $connection->prepare($rqRecupInfosMusee);
			/*** remplissage des paramètres ***/
			$donnees = array(":NomM" => $musee);
			/*** exécution du statement ***/
			$stmtRecupInfosMusee->execute($donnees);
			/*** récupération du résultat ***/
			$stmtRecupInfosMusee->setFetchMode(PDO::FETCH_OBJ);
			$resultat = $stmtRecupInfosMusee->fetch();	
			$corps .= <<<EOT
			<figure id=imageMusee>
				<img src="style/images/musees/$resultat->NomM.jpg" alt="$resultat->LienImageM"/>
			</figure>
			<article>
				<p>Liste des oeuvres que vous retrouverez ici :</p>
				<div id="Contact">
					<p>Adresse du musée : $resultat->AdresseM</p>
					<p>Contact : $resultat->ContactM</p>
				</div>
				<div id="Oeuvres">
			EOT;
			$indexOeuvre = 0;
			$rqRecupOeuvresMusee = "SELECT * FROM OEUVRE WHERE NomM = :NomM";
			$stmtRecupOeuvresMusee = $connection->prepare($rqRecupOeuvresMusee);
			$stmtRecupOeuvresMusee->execute($donnees);
			$stmtRecupOeuvresMusee->setFetchMode(PDO::FETCH_OBJ);
			while ($resultat = $stmtRecupOeuvresMusee->fetch()) {
				$corps .= "<figure id=image_" . $indexOeuvre . ">";
				$corps .= '<img src="style/images/oeuvres/' . transformeLiensImages($resultat->NomO) . '.jpg"';
				$corps .= ' alt="' . transformeLiensImages($resultat->NomO) . '">';
				$corps .= "<figcaption><p>" . $resultat->NomO . " (" . $resultat->AnneeO . ")</p>";
				$corps .= "<p>Auteur : " . $resultat->AuteurO . "</p>";
				$corps .= "</figcaption></figure>";
				$indexOeuvre += 1;
			}
			$corps .= <<<EOT
				</div>
			</article>
			EOT;
			$feuilleStylePage = $musee;
			$connection = null;
		} else {
			$titrePage = "ERREUR";
			$feuilleStylePage = "Rodin";
		}
		$zonePrincipale = $corps;
		break;
	// Partie explications DM
	case "details":
		$corps = <<<EOT
		<div id="details">
			<h2>22005394 SENECHAL Matisse Groupe 1B</h2>
			<p>Dans ce projet j'ai implémenté :</p>
			<ul id="implementation">
				<li>À l'accueil, il y a 4 boutons qui permettent d'afficher les informations et les oeuvres que possèdent les "objets" : Musée,</li>
				<li>Dans la page <a href="index.php?action=liste">Liste des réservations</a>, j'ai listé tous les "objets" : Visiteur. On peut y retrouver aussi :</li>
				<li class="fonctionnalites">un bouton affichant les informations du visiteur choisi,</li>
				<li class="fonctionnalites">un bouton modifiant (après confirmation) les informations du visiteur choisi,</li>
				<li class="fonctionnalites">un bouton supprimant (après confirmation) le visiteur choisi,</li>
				<li class="fonctionnalites">en cliquant ExpositionV ou sur NomV on peut regrouper les expositions, ou les noms, et en recliquant sur le même bouton on retrouve l'ordre de départ,</li>
				<li class="fonctionnalites">des boutons affichant les 5 visiteurs précédents ou suivants (pagination),</li>
				<li>Dans la page <a href="index.php?action=inserer">Réservations</a>, un visiteur peut s'inscrire pour une expostion (seulement si les données sont valides).</li>
			</ul>
		</div>
		EOT;
		$zonePrincipale = $corps;
		$feuilleStylePage = "details";
		$titrePage = "À propos";
		break;
	// Partie affiche l'accueil
 	default:
	 	$feuilleStylePage = "screen";
		$corps = <<<EOT
		<ul id="listeMusees">
		EOT;
		$connection = connecter();
	 	$stmt = $connection->query("SELECT * FROM MUSEE");
	 	$tableau = $stmt->fetchAll(PDO::FETCH_ASSOC);
	 	foreach ($tableau as $ligne)
			$corps .= '<li id=expo_'. strtolower($ligne["NomM"]) . '><a href="index.php?action=musee&nomMusee=' . $ligne["NomM"] . '">Musée ' . $ligne["NomM"] . '</a></li>';
	 	$corps .= "</ul>";
	 	$zonePrincipale = $corps;
	 	$query = null;
	 	$connection = null;
   		break;
}
include("squel/squelette.php");
?>
