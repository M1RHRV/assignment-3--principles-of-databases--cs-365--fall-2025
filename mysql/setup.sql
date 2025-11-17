-- Standing up the database
DROP DATABASE IF EXISTS student_passwords;
CREATE DATABASE student_passwords;
USE student_passwords;

-- User w/ no password
DROP USER IF EXISTS 'passwords_user'@'localhost';
CREATE USER 'passwords_user'@'localhost';
GRANT ALL PRIVILEGES ON student_passwords.* TO 'passwords_user'@'localhost';

-- Encryption setup
SET SESSION block_encryption_mode = 'aes-256-cbc';
SET @key_str = UNHEX(SHA2('my spectacular passphrase', 256));
SET @init_vector = RANDOM_BYTES(16);

-- My tables
CREATE TABLE IF NOT EXISTS users (
  first_name VARCHAR(128) NOT NULL,
  last_name VARCHAR(128) NOT NULL,
  user_id SMALLINT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (user_id)
);

CREATE TABLE IF NOT EXISTS websites (
  site_name VARCHAR(128) NOT NULL,
  url VARCHAR(256) UNIQUE NOT NULL,
  site_id SMALLINT NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (site_id)
);

CREATE TABLE IF NOT EXISTS account_credentials (
  username VARCHAR(128) NOT NULL,
  password VARBINARY(512) NOT NULL,
  email_address VARCHAR(128) NOT NULL,
  user_id SMALLINT NOT NULL,
  site_id SMALLINT NOT NULL,
  comment VARCHAR(512),
  time_stamp DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, site_id, username),
  FOREIGN KEY (user_id) REFERENCES users(user_id)
    ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (site_id) REFERENCES websites(site_id)
    ON UPDATE CASCADE ON DELETE CASCADE
);

-- My data
INSERT INTO users (first_name, last_name) VALUES
  ('Amir', 'Harvey'),
  ('Steve', 'Narain');

INSERT INTO websites (site_name, url) VALUES
  ('Youtube', 'https://www.youtube.com/'),
  ('GitHub', 'https://github.com/'),
  ('Nintendo', 'https://www.nintendo.com/'),
  ('Netflix', 'https://www.netflix.com/browse'),
  ('ISC2', 'https://www.isc2.org/');

INSERT INTO account_credentials (username, password, email_address, user_id, site_id, comment) VALUES
  ('mirmir21', AES_ENCRYPT('GlFigurinOutMyPass21', @key_str, @init_vector), 'mir.hrv3@gmail.com', 1, 1, 'Please like and subscribe.'),
  ('M1RHRV', AES_ENCRYPT('pokemodder567654', @key_str, @init_vector), 'mir.hrv3@gmail.com', 1, 2, 'Im going to mod the very best.'),
  ('Harvmeister', AES_ENCRYPT('F5q`f1K7@[N4=I+G7', @key_str, @init_vector), 'mirmir03@gmail.com', 1, 3, 'Nintendo is on top.'),
  ('Not_Amir03', AES_ENCRYPT('TheBinger2003', @key_str, @init_vector), 'kidharvey000000003@gmail.com', 1, 4, 'Streaming all day.'),
  ('ItsHarvey', AES_ENCRYPT('ajhhja7864jhaaha', @key_str, @init_vector), 'aharvey@hartford.edu', 1, 5, NULL),
  ('TheFewer27', AES_ENCRYPT('sn768473918375', @key_str, @init_vector), 'thefewer@real.com', 2, 1, 'I love watching videos.'),
  ('SteveNarain', AES_ENCRYPT('NarainIsTheBest', @key_str, @init_vector), 'steve0032@gmail.com', 2, 2, 'GitHub is the best place to host code.');
  ('SteveIsMyName', AES_ENCRYPT('ILoveNintendo64', @key_str, @init_vector), 'NintendoloverSteve@gmail.com', 2, 3, 'Nintendo is the best gaming company.'),
  ('SteveBinges', AES_ENCRYPT('N3tfl1xNcH1!!', @key_str, @init_vector), 'SteveIsMyName@gmail.com', 2, 4, 'Binge watching is my favorite hobby.'),
  ('CyberSecSteve', AES_ENCRYPT('hack84948dn2i2dnr', @key_str, @init_vector), 'SteveIsMyName@gmail.com', 2, 5, 'I want to be a CISSP one day.');
