--DROP DATABASE symfony_angular;

CREATE DATABASE IF NOT EXISTS symfony_angular;
USE symfony_angular;

CREATE TABLE users(
id			int(255) auto_increment NOT NULL,
role		varchar(255),
name		varchar(255),
surname     varchar(255),
email		varchar(255),
password	varchar(255),
image		varchar(255),
created_at	datetime,
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE videos(
id			int(255) auto_increment NOT NULL,
user_id		int(255) NOT NULL,
title		varchar(255),
description	text,
status		varchar(255),
image		varchar(255),
video_path 	varchar(255),
created_at	datetime DEFAULT NULL,
updated_at	datetime DEFAULT NULL,
CONSTRAINT pk_videos PRIMARY KEY(id),
CONSTRAINT fk_videos_users FOREIGN KEY(user_id) REFERENCES users(id)
)ENGINE=InnoDb;

CREATE TABLE commets(
id			int(255) auto_increment NOT NULL,
user_id		int(255) NOT NULL,
video_id	int(255) NOT NULL,
body		text,
created_at	datetime DEFAULT NULL,
updated_at	datetime DEFAULT NULL,
CONSTRAINT pk_comments PRIMARY KEY(id),
CONSTRAINT fk_comments_users FOREIGN KEY(user_id) REFERENCES users(id),
CONSTRAINT fk_comments_videos FOREIGN KEY(video_id) REFERENCES videos(id)
)ENGINE=InnoDb;
