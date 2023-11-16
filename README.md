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
## B
Develop a query which results in the columns title, the amount of hits per job, the point in time
where the first and the point in time where the last access occurred.

```SQL
SELECT job_title, COUNT(*) AS hits, MIN(date_time) AS first_access, MAX(date_time) AS last_access
FROM hits
GROUP BY job_title;
```
#### result:
| job_title | hits | first_access            | last_access             |
|-----------------|-------|---------------------|---------------------|
| Mechatroniker   | 1     | 2021-01-01 13:37:00 | 2021-01-01 13:37:00 |
| Sachbearbeiter  | 1     | 2021-08-15 00:47:11 | 2021-08-15 00:47:11 |
| Verkäufer (m/w) | 3     | 2021-05-11 07:10:10 | 2021-06-20 21:53:34 |
| Webentwickler (m/w) | 2     | 2021-03-11 17:53:12 | 2021-05-11 03:10:41 |

## C
How should one change the structure of the data to normalize the database and what would the
query in b) look like then?

```SQL
CREATE TABLE jobs (
  job_id INT PRIMARY KEY,
  job_title VARCHAR(50) NOT NULL
);

INSERT INTO jobs (job_id, job_title)
SELECT DISTINCT job_id, job_title FROM hits;

CREATE TABLE access_logs (
  access_log_id INT PRIMARY KEY AUTO_INCREMENT,
  job_id INT NOT NULL,
  date_time DATETIME NOT NULL,
  FOREIGN KEY (job_id) REFERENCES jobs(job_id)
);

INSERT INTO access_logs (job_id, date_time)
SELECT job_id, date_time FROM hits;

-- DROP TABLE hits;
-- could be usefull in prod

SELECT j.job_title, COUNT(*) AS hits, MIN(al.date_time) AS first_access, MAX(al.date_time) AS last_access
FROM jobs j
JOIN access_logs al ON j.job_id = al.job_id
GROUP BY j.job_title;

```
#### result:

| job_title           | hits | first_access        | last_access         |
|---------------------|------|---------------------|---------------------|
| Webentwickler (m/w) |    2 | 2021-03-11 17:53:12 | 2021-05-11 03:10:41 |
| Mechatroniker       |    1 | 2021-01-01 13:37:00 | 2021-01-01 13:37:00 |
| Sachbearbeiter      |    1 | 2021-08-15 00:47:11 | 2021-08-15 00:47:11 |
| Verkufer (m/w)      |    3 | 2021-05-11 07:10:10 | 2021-06-20 21:53:34 |

# 2.)

Describe briefly in your own words which risks come up when you have to work with

Most prominent would be Injections, unauthorised data access, data coruption/manipulations and DoS.  
Never trust raw user input, sanatize and validate it, parameterized queries, stored procedures or atleas Escape and encode should be impemented

# 4.)
7.2 & gd aint friends, using 7.4  
Write a PHP file that outputs a PNG image with a checkerboard pattern using the GD library. The
width (width) and height (height) of the image as well as the side length of the fields (fieldWidth)
should be passed via GET parameters.
No object-oriented approach is necessary for this task.

[checkerboard.php](checkerboard.php)  
[localhost:8000/checkerboard.php](http://localhost:8000/checkerboard.php?width=800&height=800&fieldWidth=50)  

# 5.)
Write a minimalistic backup solution.
You need a shell script (sh or bash) which archives the directories /var/www/applicant and
/var/www/test together with their contents into a tar.bz2 file. The archive should contain the current
date in the name and be stored in the directory /mnt/backuptest. This directory will only contain
your backup files, but since the disk space is limited, there should never be more than 5 files here - so
if the 6th file is added, the script should delete the oldest one.
The script should be executed daily at 1:15 at night - how do you realize this?

create dirs to backup  
```bash
mkdir /var/www/test
mkdir /var/www/applicant
```
[backup script](backup_script.sh)
set daily exe at 1:15
```bash
    (echo "15 1 * * * /var/www/html/backup_script.sh") | crontab -u root -
```  
