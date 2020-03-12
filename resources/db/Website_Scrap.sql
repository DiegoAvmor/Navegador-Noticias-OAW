
CREATE TABLE web_scrapping (
  web_id integer PRIMARY KEY AUTO_INCREMENT,
  title varchar(100) NOT NULL UNIQUE,
  url varchar(100) NOT NULL,
  raw text NOT NULL,
  keywords varchar(100) NOT NULL,
  last_modified date DEFAULT NULL,
  FULLTEXT(raw)
);
CREATE TABLE references (
  father_url varchar(100) NOT NULL,
  child_url varchar(100) NOT NULL,
   FOREIGN KEY(father_url) 
   REFERENCES news(url),
   FOREIGN KEY(child_url) 
   REFERENCES news(url),
   CONSTRAINT relationship_not_repeated UNIQUE(father_url, child_url)
);