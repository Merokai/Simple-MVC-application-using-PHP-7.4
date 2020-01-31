CREATE TABLE users
(
    email       VARCHAR(75) PRIMARY KEY UNIQUE NOT NULL,
    displayName VARCHAR(75)                    NOT NULL,
    password    VARCHAR(75)                    NOT NULL
);

INSERT INTO users(email, displayName, password)
VALUES ('antoine.molina@live.fr', 'Amo', 'P@ssw0rd');
