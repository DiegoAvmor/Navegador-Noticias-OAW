/* Database schema */
CREATE DATABASE IF NOT EXISTS news_feed_rss;
USE news_feed_rss;

CREATE TABLE news (
  news_id integer PRIMARY KEY AUTO_INCREMENT,
  title varchar(100) NOT NULL UNIQUE,
  url varchar(100) NOT NULL,
  description text NOT NULL,
  author varchar(40) DEFAULT NULL,
  publish_date varchar(50) DEFAULT NULL,
  FULLTEXT(description)
);