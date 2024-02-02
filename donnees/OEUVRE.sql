DROP TABLE IF EXISTS `OEUVRE`;

CREATE TABLE IF NOT EXISTS OEUVRE (
  NomM varchar(100) NOT NULL PRIMARY KEY,
  NomO varchar(100) NOT NULL,
  AnneeO int(100) NOT NULL,
  AuteurO varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `OEUVRE` (`NomM`, `NomO`, `AnneeO`, `AuteurO`) VALUES
("Orsay", "Les Glaneuses", 1857, "Jean-François Millet"),
("Orsay", "Le Déjeuner sur l’herbe", 1863, "Édouard Manet"),
("Orsay", "Coquelicots", 1873, "Claude Monet"),
("Louvre", "Portrait de l'artiste tenant un chardon", 1493, "Albrecht Dürer"),
("Louvre", "La Joconde", 1503, "Léonard de Vinci"),
("Louvre", "Les Noces de Cana", 1563, "Paul Véronèse"),
("Rodin", "Les Trois Soeurs", 1917, "Henri Matisse"),
("Rodin", "Antonia", 1915, "Amedeo Modigliani"),
("Rodin", "Les Deux Saules", 1914, "Claude Monet"),
("Orangerie", "L'homme qui marche", 1900, "Auguste Rodin"),
("Orangerie", "Le Penseur", 1880, "Auguste Rodin"),
("Orangerie", "Le Père Tanguy", 1887, "Vincent van Gogh");