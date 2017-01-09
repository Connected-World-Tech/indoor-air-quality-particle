create database iaq;
create user 'php_iaq'@'localhost' identified by 'php_iaq';
grant all on iaq.* to 'php_iaq'@'localhost' with max_user_connections 20;
use iaq;

create table groups (
id INT NOT NULL AUTO_INCREMENT, 
name VARCHAR(30), 
temp_hum INT DEFAULT NULL, -- id of core in cores table
dust INT DEFAULT NULL, -- id of core in cores table
sewer INT DEFAULT NULL, -- id of core in cores table
hcho INT DEFAULT NULL, -- id of core in cores table
co INT DEFAULT NULL, -- id of core in cores table
tz VARCHAR(40), 
UNIQUE(name),
UNIQUE(temp_hum),
UNIQUE(dust),
UNIQUE(sewer),
UNIQUE(hcho),
UNIQUE(co),
PRIMARY KEY (id)
);

create table cores (
id INT NOT NULL AUTO_INCREMENT, 
core_id VARCHAR(24), 
UNIQUE(core_id), 
PRIMARY KEY (id)
);

create table readings (
id INT NOT NULL AUTO_INCREMENT, 
temperature DECIMAL(4,2), 
humidity INT, 
dust INT, 
sewer INT, 
hcho INT, 
co INT,
group_id INT, 
ts INT UNSIGNED, 
PRIMARY KEY (id), 
UNIQUE(group_id, ts)
);

create table geographical (
id INT NOT NULL AUTO_INCREMENT,
name VARCHAR(40),
group_id INT NOT NULL,
zoom_temp_hum DECIMAL(1,0),
zoom_dust DECIMAL(1,0),
zoom_sewer DECIMAL(1,0),
zoom_hcho DECIMAL(1,0),
ts INT UNSIGNED NOT NULL,
UNIQUE(group_id, ts), 
PRIMARY KEY (id)
);

create table locations (
id INT NOT NULL AUTO_INCREMENT, 
type INT NOT NULL,
name VARCHAR(40) NOT NULL,
group_id INT NOT NULL, 
ts INT UNSIGNED NOT NULL, 
UNIQUE(type, group_id, ts), 
PRIMARY KEY (id)
);

create table events (
id INT NOT NULL AUTO_INCREMENT, 
name VARCHAR(40), 
group_id INT NOT NULL, 
ts INT UNSIGNED NOT NULL, 
UNIQUE(group_id, ts), 
PRIMARY KEY (id)
);

create table state_type (
id INT NOT NULL AUTO_INCREMENT, 
location_id INT NOT NULL,
name VARCHAR(40) NOT NULL, 
state_on VARCHAR(20) NOT NULL, 
state_off VARCHAR(20) NOT NULL,
UNIQUE(location_id, name),
PRIMARY KEY (id)
);

create table state_changes (
id INT NOT NULL AUTO_INCREMENT, 
location_id INT NOT NULL,
state_type_id INT NOT NULL,
state INT NOT NULL,
ts INT UNSIGNED NOT NULL,
PRIMARY KEY (id)
);

# ---------------------------- change time zone to UTC
# in /etc/mysql/my.cnf [mysqld] section
# add :
# default-time-zone = '+00:00'
# restart mysql
# sudo service mysql restart
#
# select @@global.time_zone, @@session.time_zone;
# +--------------------+---------------------+
# | @@global.time_zone | @@session.time_zone |
# +--------------------+---------------------+
# | +00:00             | +00:00              |
# +--------------------+---------------------+
