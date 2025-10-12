-- Create the database
CREATE DATABASE IF NOT EXISTS bmace_admin;
USE bmace_admin;

-- =============================
-- Table: admins
-- =============================
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



-- =============================
-- Table: contact_messages
-- =============================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    subject VARCHAR(255),
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================
-- Table: career_applications
-- =============================
CREATE TABLE IF NOT EXISTS career_applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    position VARCHAR(255) NOT NULL,
    resume VARCHAR(255),
    home_address VARCHAR(255),
    gender ENUM('Male','Female','Other'),
    marital_status ENUM('Single','Married','Divorced','Widowed'),
    country VARCHAR(100),
    qualifications TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================
-- Table: projects
-- =============================
CREATE TABLE IF NOT EXISTS projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    status ENUM('Ongoing','Completed') NOT NULL DEFAULT 'Ongoing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample projects
INSERT INTO projects (title, description, image, status) VALUES
('Solar Panel Installation', 'Installation of 250W solar panels at multiple sites.', 'images/solar1.jpg', 'Completed'),
('Road Construction Phase 2', 'Ongoing road construction in Abuja.', 'images/road1.jpg', 'Ongoing');

-- =============================
-- Table: ongoing_projects (separate)
-- =============================
CREATE TABLE IF NOT EXISTS ongoing_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    start_date DATE,
    expected_end_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample ongoing project
INSERT INTO ongoing_projects (title, description, image, start_date, expected_end_date) VALUES
('Water Treatment Plant', 'Construction of water treatment facility.', 'images/water1.jpg', '2025-01-15', '2025-12-15');