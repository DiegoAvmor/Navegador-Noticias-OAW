
CREATE TABLE web_scrapping (
  web_id integer PRIMARY KEY AUTO_INCREMENT,
  title varchar(100) NOT NULL UNIQUE,
  url varchar(100) NOT NULL,
  raw text NOT NULL,
  keywords varchar(100) NOT NULL,
  last_modified date DEFAULT NULL,
  FULLTEXT(raw)
);