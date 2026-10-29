CREATE DATABASE IF NOT EXISTS crm_candidatures CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE crm_candidatures;
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY,name VARCHAR(80) NOT NULL,email VARCHAR(150) NOT NULL UNIQUE,password VARCHAR(255) NOT NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP);
CREATE TABLE applications(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL,company VARCHAR(120) NOT NULL,position VARCHAR(150) NOT NULL,
status ENUM('a_postuler','postule','entretien','offre','refuse') NOT NULL DEFAULT 'a_postuler',contract VARCHAR(40),location VARCHAR(120),salary VARCHAR(60),url VARCHAR(500),
contact_name VARCHAR(120),contact_email VARCHAR(150),notes TEXT,applied_at DATE NULL,follow_up DATE NULL,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE,INDEX(user_id,status));
USE crm_candidatures;
CREATE TABLE companies(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL,name VARCHAR(120) NOT NULL,sector VARCHAR(80),website VARCHAR(255),city VARCHAR(100),logo VARCHAR(255),notes TEXT,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,UNIQUE(user_id,name),FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE);
ALTER TABLE applications ADD company_id INT NULL AFTER user_id,ADD priority TINYINT NOT NULL DEFAULT 2,ADD FOREIGN KEY(company_id) REFERENCES companies(id) ON DELETE SET NULL;
INSERT IGNORE INTO companies(user_id,name) SELECT DISTINCT user_id,company FROM applications;
UPDATE applications a JOIN companies c ON c.user_id=a.user_id AND c.name=a.company SET a.company_id=c.id;
CREATE TABLE activities(id INT AUTO_INCREMENT PRIMARY KEY,application_id INT NOT NULL,user_id INT NOT NULL,type ENUM('appel','email','entretien','note','relance') NOT NULL DEFAULT 'note',content TEXT,happened_at DATETIME DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(application_id) REFERENCES applications(id) ON DELETE CASCADE,INDEX(application_id));
