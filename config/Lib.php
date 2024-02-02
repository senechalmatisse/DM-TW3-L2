<?php
// Permet de se connecter à la base de données
function connecter() {
    try {
        $dsn = 'mysql:host=mysql.info.unicaen.fr;dbname=22005394_1;charset=utf8';
        $utilisateur = '22005394';
        $motDePasse = file_get_contents("config/donnees/ressources.txt");
        // Options de connection
        $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8", PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION);
        $connection = new PDO($dsn, $utilisateur, $motDePasse, $options);
        return($connection);
    } catch (Exception $e) {
        echo "Connection à MySQL impossible : ", $e->getMessage();
        die();
    }
}

// Renvoie les pages de la pagination pour la liste
function renvoiePages($nbPages, $nbIndex, $page, $param) {
    $corps = '<ul id="pagination">';
    if ($page > 1) {
        $pageCourante = $page - 1;
        $corps .= '<li id="precedent"><a href="index.php?action=' . $param . '&page=' . $pageCourante . '">Précédent</a></li>';
    }
    if ($page > 3)
        $corps .= '<li><a href="index.php?action=' . $param . '&page=1">1</a></li><li>...</li>';
    // La première page à afficher
    $premierePage = max(1, $page - 2);
    // La dernière page à afficher
    $dernierePage = min($page + 2, ceil($nbPages / $nbIndex));
    for ($numero = $premierePage; $numero <= $dernierePage; $numero++) {
        $corps .= '<li';
        if ($numero == $page)
            $corps .= ' id="pageActuelle"';
        $corps .= '><a href="index.php?action=' . $param . '&page=' . $numero . '">' . $numero . '</a></li>';
    }
    if ($page < ceil($nbPages / $nbIndex) - 2)
        $corps .= '<li>...</li><li><a href="index.php?action=' . $param . '&page=' . ceil($nbPages / $nbIndex).'">' . ceil($nbPages / $nbIndex) . '</a></li>';
    if ($page < ceil($nbPages / $nbIndex)) {
        $pageCourante = $page + 1;
        $corps .= '<li id="suivant"><a href="index.php?action=' . $param . '&page=' . $pageCourante . '">Suivant</a></li>';
    }
    $corps .= '</ul>';
    return $corps;
}

// Transforme le paramètre en entier
function convertirGet($param) {
    return (int)$param;
}

// Vérifie si la date est valide
function controlerDate($valeur) {
    if (preg_match("/^(\d{1,2})[\/|\-|\.](\d{1,2})[\/|\-|\.](\d\d)(\d\d)?$/", $valeur, $regs)) {
        $jour = ($regs[1] < 10) ? "0".$regs[1] : $regs[1]; 
        $mois = ($regs[2] < 10) ? "0".$regs[2] : $regs[2]; 
        if ($regs[4]) {
            $an = $regs[3] . $regs[4];
            if (checkdate($mois, $jour, $an)) 
                return true;
        }
        return false;
    }
    return false;
}

// Vérifie si la valeur contient un chiffre
function controlerNum($valeur, $strict=false) {
    if ($strict) {
        if (preg_match("/^[0-9]+$/", $valeur))
            return true;
        return false;
    }
    else if (preg_match("/^[\d|\s|\-|\+|E|e|,|\.]+$/", $valeur))
        return true;
    return false;
}

// Vérifie si le numéro de téléphone est valide
function controlerTel($valeur) {
    if (preg_match('/^(0|[+][3][3])\s?[1-9]((\s?\d{2}){4}|(\s\d{2}){3}\s?\d{2})$/', $valeur))
      return true;
    return false;
}

// Vérifie si l'année est valide
function anneeValide($date) {
    $annee = substr($date, 6, 4);
    if ($annee > 1907)
      return true;
    return false;
}

// Vérifie si le nom ou le prénom est valide
function nomPrenomValide($valeur) {
    if (preg_match('/^[A-ZÀ-ÖØ-Ýa-zà-öø-ý\s\'-]*(-[A-ZÀ-ÖØ-Ýa-zà-öø-ý\s\'-]*)*$/u', $valeur))
        return true;
    return false;
}

// Change le format de la date pour qu'elle soit valide pour le format date de SQL
function changeFormatDate($valeur) {
    $inv = array_reverse(explode('-', $valeur));
    return implode('-', $inv);
}

// Rend utilisable le lien pourt l'image
function transformeLiensImages($nomOeuvre) {
    $lienOeuvre = supprimeAccents(strtolower($nomOeuvre));
    $lienOeuvre = str_replace("'", " ", $lienOeuvre);
    $tab = explode(" ", $lienOeuvre);
    $lienOeuvre = implode("_", $tab);
    return $lienOeuvre;
}

// Supprime les accents du texte
function supprimeAccents($texte) {
    $utf8 = array(
        '/[áàâãªä]/u' => 'a',
        '/[ÁÀÂÃÄ]/u' => 'A',
        '/[ÍÌÎÏ]/u' => 'I',
        '/[íìîï]/u' => 'i',
        '/[éèêë]/u' => 'e',
        '/[ÉÈÊË]/u' => 'E',
        '/[óòôõºö]/u' => 'o',
        '/[ÓÒÔÕÖ]/u' => 'O',
        '/[úùûü]/u' => 'u',
        '/[ÚÙÛÜ]/u' => 'U',
        '/ç/' => 'c',
        '/Ç/' => 'C',
        '/ñ/' => 'n',
        '/Ñ/' => 'N',
        "/’/" => '_',
    );
    return preg_replace(array_keys($utf8), array_values($utf8), $texte);
}

// Vértifie si l'id est valide
function idValide($id) {
    $utf8 = array(
        "/['\'.]/u" => '',
        "/[.\'']/u" => ''
    );
    return preg_replace(array_keys($utf8), array_values($utf8), $id);
}

// Vértifie si le nom du musée est valide
function verifieNomMusee($nomMusee) {
    $listeNomsMusee = array("Orsay", "Louvre", "Orangerie", "Rodin");
    foreach($listeNomsMusee as $index => $nom) {
        if($nomMusee === $nom)
            return true;
    }
    return false;
}

// Renvoie le titre de la page pour le nom du musée correspondant
function nomMuseeCorrespondant($nomMusee) {
    switch($nomMusee) {
        case 'louvre':
            return "le musée du Louvre";
        case 'orangerie':
            return "le musée de l'Orangerie";
        case 'orsay':
            return "le musée d'Orsay";
        case 'rodin':
            return "le musée Rodin";    
    }
}

// Ajoute le visiteur dans le tableau des visiteurs
function ajouterVisiteur($visiteurs, $visiteur) {
    $visiteurs[] = $visiteur;
    return $visiteurs;
}

// Supprime le visiteur dans le tableau des visiteurs
function supprimerVisiteur($visiteurs, $idV) {
    foreach ($visiteurs as $index => $visiteur) {
        if ($visiteur->getIdV() == $idV) {
            unset($visiteurs[$index]);
            $visiteurs = array_values($visiteurs);
            break;
        }
    }
    return $visiteurs;
}

// Modifie le visiteur dans le tableau des visiteurs
function modifierVisiteur($visiteurs, $idV, $visiteurModifie) {
    foreach ($visiteurs as $index => $visiteur) {
        if ($visiteur->getIdV() == $idV) {
            $visiteurs[$index] = $visiteurModifie;
            break;
        }
    }
    return $visiteurs;
}

// Une classe Visiteur permettant de manipuler un visiteur
class Visiteur {
    private $IdV, $nom, $prenom, $dateN, $telephone, $exposition;

    public function __construct($IdV, $nom, $prenom, $dateN, $telephone, $exposition) {
        $this->IdV = $IdV;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->dateN = $dateN;
        $this->telephone = $telephone;
        $this->exposition = $exposition;
    }

    public function getIdV() {
        return $this->IdV;
    }
    public function getNomV() {
        return $this->nom;
    }
    public function getPrenomV() {
        return $this->prenom;
    }
    public function getDateN() {
        return $this->dateN;
    }
    public function getTelephoneV() {
        return $this->telephone;
    }
    public function getExpositionV() {
        return $this->exposition;
    }

    public function selectionVisiteur() {
        $corps = <<<EOT
        <span class="c1">
            <b>
                <u>$this->IdV</u>
            </b>
        </span>
        <span class="c1">$this->nom</span>
        <span class="c1">$this->prenom</span>
        <span class="c1">$this->dateN</span>
        <span class="c1">$this->telephone</span>
        <span class="c1">$this->exposition</span>
        <span>
            <a href=index.php?action=select&IdV=$this->IdV><span class="glyphicon glyphicon-eye-open"></span></a>
        </span>
        <span>
            <a href="index.php?action=update&IdV=$this->IdV"><span class="glyphicon glyphicon-pencil"></span></a>
        </span>
        <span>
            <a href="index.php?action=delete&IdV=$this->IdV"><span class="glyphicon glyphicon-trash"></span></a>
        </span>
        <br>
        EOT;
        return $corps;
    }

    public function ajoutVisiteur() {
        $exposition = nomMuseeCorrespondant($this->exposition);
        $corps = <<<EOT
        <div id="infos">
            <ul>
                <li>Ce visiteur a pour identifiant : $this->IdV</li>
                <li>Le visiteur s'appelle : $this->prenom $this->nom </li>
                <li>Il/Elle est né(e) le $this->dateN</li>
                <li>Il/Elle est a comme numéro de téléphone : $this->telephone</li>
                <li>Il/Elle est a choisi de visiter $exposition</li>
            </ul>
        </div>
        EOT;
        return $corps;
    }
}

$IdV=null;$nom = null;$prenom = null;$dateN = null;$telephone = null;
$erreur = array("IdV"=>null, "nom"=>null, "prenom"=>null, "dateN"=>null, "telephone"=>null);
?>
