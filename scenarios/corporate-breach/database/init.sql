USE corporate_db;

-- Employees table (for login)
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255),
    role ENUM('user', 'developer', 'admin') NOT NULL DEFAULT 'user',
    email VARCHAR(100),
    name VARCHAR(100),
    position VARCHAR(50),
    salary DECIMAL(10,2),
    ssn VARCHAR(20)
);

-- Insert employees (ADMIN FIRST for OR 1=1 injection)
INSERT INTO employees (username, password, role, email, name, position, salary, ssn) VALUES
('admin', MD5('admin123'), 'admin', 'admin@cybercorp.com', 'Jane Smith', 'Administrator', 150000.00, '987-65-4321'),
('john', MD5('password'), 'user', 'john@cybercorp.com', 'John Doe', 'Developer', 85000.00, '123-45-6789'),
('alice', MD5('alice123'), 'user', 'alice@cybercorp.com', 'Alice Johnson', 'Analyst', 75000.00, '555-12-3456');

-- Admin credentials table (contains SSH info)
CREATE TABLE IF NOT EXISTS admin_credentials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service VARCHAR(50),
    username VARCHAR(50),
    password VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert SSH credentials (only visible to admin)
INSERT INTO admin_credentials (service, username, password, notes) VALUES
('SSH Server', 'webadmin', 'SuperSecretPass123', 'Main SSH access for server administration'),
('Database', 'root', 'RootDBPass456', 'MySQL root credentials'),
('VPN', 'admin', 'VPNPass789', 'VPN access for remote work');

-- User announcements table (for user homepage)
CREATE TABLE IF NOT EXISTS announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    content TEXT,
    posted_by VARCHAR(50),
    posted_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO announcements (title, content, posted_by) VALUES
('Welcome to CyberCorp', 'Welcome to the employee portal. Please review company policies.', 'HR Department'),
('System Maintenance', 'Scheduled maintenance this weekend. Systems will be unavailable.', 'IT Department');
