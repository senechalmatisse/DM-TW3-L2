DROP TABLE IF EXISTS `VISITEUR`;

CREATE TABLE IF NOT EXISTS VISITEUR (
  IdV int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
  NomV varchar(100) NOT NULL,
  PrenomV varchar(100) NOT NULL,
  DateN date NOT NULL,
  TelephoneV varchar(100) NOT NULL
  ExpositionV varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;