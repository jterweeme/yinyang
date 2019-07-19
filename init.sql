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
    ("jterweeme", "dd12f9ea008725c266a07d94753d5693"),
    ("jbond", "dd12f9ea008725c266a07d94753d5693");

INSERT INTO xgroup(name) VALUES
    ("admin"),
    ("basis"),
    ("groep1"),
    ("test"),
    ("trivia");

INSERT INTO member(groupname, username) VALUES
    ("admin", "jterweeme"),
    ("groep1", "jbond"),
    ("test", "jbond");

INSERT INTO assignment(groupname, examfile) VALUES
    ("groep1", "ite60.xml"),
    ("test", "test.xml"),
    ("test", "test2.xml"),
    ("test", "test3.xml"),
    ("test", "test4.xml"),
    ("test", "test5.xml");



