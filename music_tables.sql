create table if not exists Artist (id int primary key auto_increment, name varchar(100) unique not null);

create table if not exists Album (title varchar(200),genre varchar(100), released date, artist_id int not null,
image_url varchar(200), price decimal(5,2) unsigned not null, foreign key (artist_id) references Artist(id), 
primary key (title, artist_id));

create table if not exists Song (song_title varchar(200) not null,
artist varchar(200) not null, album_name varchar(200) not null, track_num int not null,
audio_url varchar(200) not null,
primary key(song_title, album_name, artist));

create table if not exists Users (id int primary key auto_increment, username varchar(100) unique not null,
email varchar(200) unique not null, pass varchar(200) not null, 
image_url varchar(100));

create table if not exists Favorites(artist varchar(100), title varchar(200),
username varchar(100), primary key(artist, title, username), foreign key(username) references Users(username),
thumbnail varchar(200),
foreign key(artist) REFERENCES Artist(name));

create table if not exists Orders(artist varchar(100), title varchar(200),
username varchar(100), quantity int, format varchar(10), price decimal(5,2), thumbnail varchar(200),
primary key(artist, title, username),
foreign key(username) references Users(username), foreign key(artist) REFERENCES Artist(name));

create table if not exists OrderHistory(artist varchar(100), title varchar(200),
username varchar(100), quantity int, format varchar(10), price decimal(5,2), thumbnail varchar(200),
primary key(artist, title, username),
foreign key(username) references Users(username), foreign key(artist) REFERENCES Artist(name));