-- Database init

DROP TABLE IF EXISTS xuser;
DROP TABLE IF EXISTS xgroup;
DROP TABLE IF EXISTS member;
DROP TABLE IF EXISTS assignment;

CREATE TABLE IF NOT EXISTS xuser
(
    name VARCHAR(255) PRIMARY KEY NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS xgroup
(
    name VARCHAR(255) PRIMARY KEY NOT NULL
);

CREATE TABLE IF NOT EXISTS member
(
    groupname VARCHAR(255),
    username VARCHAR(255),

    PRIMARY KEY (groupname, username),
    FOREIGN KEY (groupname) REFERENCES xgroup(name),
    FOREIGN KEY (username) REFERENCES xuser(name)
);

CREATE TABLE IF NOT EXISTS assignment
(
    groupname VARCHAR(255),
    examfile VARCHAR(255),
    
    PRIMARY KEY (groupname, examfile),
    FOREIGN KEY (groupname) REFERENCES xgroup(name)
);

INSERT INTO xuser(name, password) VALUES
    ("jasper", "02b0732024cad6ad3dc2989bc82a1ef5");

INSERT INTO xgroup(name) VALUES
    ("admin"),
    ("groep1");

INSERT INTO member(groupname, username) VALUES
    ("admin", "jasper");

INSERT INTO assignment(groupname, examfile) VALUES
    ("groep1", "ite60.xml");

