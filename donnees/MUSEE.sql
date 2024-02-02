DROP TABLE IF EXISTS `MUSEE`;

CREATE TABLE IF NOT EXISTS MUSEE (
  NomM varchar(100) NOT NULL PRIMARY KEY,
  AdresseM varchar(100) NOT NULL,
  ContactM varchar(100) NOT NULL,
  LienImageM varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `MUSEE` (`NomM`, `AdresseM`, `ContactM`) VALUES
("Louvre", "91BA Rue de Rivoli, 75001 Paris","info@louvre.fr, +33 (0)1 40 20 53 17", "https://www.giftcampaign.fr/blog/wp-content/uploads/2021/04/poto-louvre-1-1024x576.jpg"),
("Orangerie", "18 Quai des Tuileries, 75001 Paris","+33 (0)1 44 50 43 00", "https://fr.wikipedia.org/wiki/Mus%C3%A9e_de_l%27Orangerie#/media/Fichier:2011-12-Musee_de_lorangerie.jpg"),
("Orsay", "Esplanade Valéry Giscard d'Estaing, 75007 Paris","+33 (0)1 40 49 48 14", "https://keewego.com/wp-content/uploads/2018/10/musee-orsay-face-seine-keewego-paris.jpg"),
("Rodin", "77 rue de Varenne, 75007 Paris","+33 (0)1 44 18 61 10", "https://www.connaissancedesarts.com/wp-content/thumbnails/uploads/2020/07/cda20_actu_rodin_bronzes_jardins-tt-width-1280-height-720-fill-1-crop-0-bgcolor-ffffff.jpg");