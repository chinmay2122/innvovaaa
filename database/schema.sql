-- InnovateX Events Database Schema
-- Create this database before running the application

CREATE DATABASE IF NOT EXISTS innovatex_events;
USE innovatex_events;

-- Admin users table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    cover_image VARCHAR(255) NOT NULL,
    prize_money VARCHAR(100),
    event_date DATE NOT NULL,
    event_head_name VARCHAR(100) NOT NULL,
    rule_book VARCHAR(255),
    registration_link VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Registrations table
CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    team_name VARCHAR(100) NOT NULL,
    leader_name VARCHAR(100) NOT NULL,
    leader_email VARCHAR(100) NOT NULL,
    leader_phone VARCHAR(20) NOT NULL,
    team_size INT NOT NULL,
    college_name VARCHAR(200) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Team members table
CREATE TABLE IF NOT EXISTS team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    designation VARCHAR(100) NOT NULL,
    team_category ENUM('Faculty', 'Organisers', 'Event Heads', 'Design Team', 'Website Team', 'Decor Team', 'Volunteers') NOT NULL,
    photo VARCHAR(255) NOT NULL,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Blogs table
CREATE TABLE IF NOT EXISTS blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    excerpt TEXT NOT NULL,
    cover_image VARCHAR(255) NOT NULL,
    author VARCHAR(100) NOT NULL,
    publish_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact queries table
CREATE TABLE IF NOT EXISTS contact_queries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('unread', 'read') DEFAULT 'unread',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create default admin user (username: admin, password: admin123)
-- Password is hashed using PHP password_hash()
INSERT INTO admins (username, password, email) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@innovatex.com');

-- Sample events for testing (optional)
INSERT INTO events (title, description, cover_image, prize_money, event_date, event_head_name, registration_link) VALUES
('Code Sprint', 'A 24-hour coding marathon where teams compete to build innovative solutions to real-world problems.', 'uploads/sample1.jpg', '₹50,000', '2026-02-15', 'Dr. Rajesh Kumar', '#'),
('Tech Quiz', 'Test your technical knowledge in this fast-paced quiz competition covering programming, algorithms, and technology trends.', 'uploads/sample2.jpg', '₹25,000', '2026-02-16', 'Prof. Anita Sharma', '#'),
('Web Design Challenge', 'Showcase your creativity and design skills by creating stunning website interfaces within the time limit.', 'uploads/sample3.jpg', '₹30,000', '2026-02-17', 'Mr. Vikram Singh', '#');
