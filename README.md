# 1.)
## A
You will find the file "hits.xls" attached.  
Put the data from the file into a mySQL-table and describe how you accomplished that.  

prep envierment to run on:
```bash
docker compose -f "docker-compose.yml" up -d --build 
```
```SQL
CREATE DATABASE JC;
USE JC;
CREATE TABLE hits (
  job_id INT,
  job_title VARCHAR(50),
  date_time DATETIME
);
```
export xls as csv and import via script:
```SQL
USE JC;
LOAD DATA INFILE '/var/lib/mysql-files/hits.csv'
INTO TABLE hits
FIELDS TERMINATED BY ',' ENCLOSED BY '"' LINES TERMINATED BY '\n'
IGNORE 1 ROWS;
```
or lazy via [PMA](http://localhost:8080/index.php) or any other GUI
