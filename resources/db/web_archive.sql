CREATE DATABASE IF NOT EXISTS web_archive;
USE web_archive;

CREATE TABLE website(
    website_id INTEGER AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(250) UNIQUE NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    body TEXT NOT NULL,
    keywords VARCHAR(200),
    last_modified INTEGER,
    FULLTEXT(title, description, body, keywords)
);

CREATE TABLE reference(
    website_id_parent INTEGER,
    website_id_child INTEGER,
    PRIMARY KEY(website_id_parent, website_id_child),
    FOREIGN KEY(website_id_parent) REFERENCES website(website_id),
    FOREIGN KEY(website_id_child) REFERENCES website(website_id)
);
