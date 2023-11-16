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