USE crm_candidatures;
CREATE TABLE companies(id INT AUTO_INCREMENT PRIMARY KEY,user_id INT NOT NULL,name VARCHAR(120) NOT NULL,sector VARCHAR(80),website VARCHAR(255),city VARCHAR(100),logo VARCHAR(255),notes TEXT,created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,UNIQUE(user_id,name),FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE);
ALTER TABLE applications ADD company_id INT NULL AFTER user_id,ADD priority TINYINT NOT NULL DEFAULT 2,ADD FOREIGN KEY(company_id) REFERENCES companies(id) ON DELETE SET NULL;
INSERT IGNORE INTO companies(user_id,name) SELECT DISTINCT user_id,company FROM applications;
UPDATE applications a JOIN companies c ON c.user_id=a.user_id AND c.name=a.company SET a.company_id=c.id;
CREATE TABLE activities(id INT AUTO_INCREMENT PRIMARY KEY,application_id INT NOT NULL,user_id INT NOT NULL,type ENUM('appel','email','entretien','note','relance') NOT NULL DEFAULT 'note',content TEXT,happened_at DATETIME DEFAULT CURRENT_TIMESTAMP,FOREIGN KEY(application_id) REFERENCES applications(id) ON DELETE CASCADE,INDEX(application_id));
