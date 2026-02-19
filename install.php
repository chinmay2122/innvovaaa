<?php
// Database Installer for InnovateX Events System
// Run this file ONCE to create the database and tables

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'innovatex_events';

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>InnovateX Database Installer</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #010101; }
        .success { color: #22c55e; padding: 10px; background: #f0fdf4; border-left: 4px solid #22c55e; margin: 10px 0; }
        .error { color: #ef4444; padding: 10px; background: #fef2f2; border-left: 4px solid #ef4444; margin: 10px 0; }
        .info { color: #3b82f6; padding: 10px; background: #eff6ff; border-left: 4px solid #3b82f6; margin: 10px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #010101; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background: #333; }
        pre { background: #f9fafb; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🚀 InnovateX Database Installer</h1>";

try {
    // Connect to MySQL (without database)
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<div class='success'>✓ Connected to MySQL server successfully!</div>";
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success'>✓ Database '$dbname' created successfully!</div>";
    } else {
        throw new Exception("Error creating database: " . $conn->error);
    }
    
    // Select the database
    $conn->select_db($dbname);
    echo "<div class='success'>✓ Database selected!</div>";
    
    // Create admins table
    $sql = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success'>✓ Table 'admins' created successfully!</div>";
    } else {
        throw new Exception("Error creating admins table: " . $conn->error);
    }
    
    // Create events table
    $sql = "CREATE TABLE IF NOT EXISTS events (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success'>✓ Table 'events' created successfully!</div>";
    } else {
        throw new Exception("Error creating events table: " . $conn->error);
    }
    
    // Create registrations table
    $sql = "CREATE TABLE IF NOT EXISTS registrations (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success'>✓ Table 'registrations' created successfully!</div>";
    } else {
        throw new Exception("Error creating registrations table: " . $conn->error);
    }
    
    // Create team_members table
    $sql = "CREATE TABLE IF NOT EXISTS team_members (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        designation VARCHAR(100) NOT NULL,
        team_category ENUM('Faculty', 'Organisers', 'Event Heads', 'Design Team', 'Website Team', 'Decor Team', 'Volunteers') NOT NULL,
        photo VARCHAR(255) NOT NULL,
        display_order INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success'>✓ Table 'team_members' created successfully!</div>";
    } else {
        throw new Exception("Error creating team_members table: " . $conn->error);
    }
    
    // Create blogs table
    $sql = "CREATE TABLE IF NOT EXISTS blogs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(200) NOT NULL,
        excerpt TEXT NOT NULL,
        cover_image VARCHAR(255) NOT NULL,
        author VARCHAR(100) NOT NULL,
        publish_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success'>✓ Table 'blogs' created successfully!</div>";
    } else {
        throw new Exception("Error creating blogs table: " . $conn->error);
    }
    
    // Check if default admin exists
    $result = $conn->query("SELECT COUNT(*) as count FROM admins WHERE username = 'admin'");
    $row = $result->fetch_assoc();
    
    if ($row['count'] == 0) {
        // Create default admin user
        $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO admins (username, password, email) VALUES ('admin', ?, 'admin@innovatex.com')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $defaultPassword);
        
        if ($stmt->execute()) {
            echo "<div class='success'>✓ Default admin user created!</div>";
            echo "<div class='info'>
                <strong>Default Login Credentials:</strong><br>
                Username: <strong>admin</strong><br>
                Password: <strong>admin123</strong>
            </div>";
        } else {
            throw new Exception("Error creating admin user: " . $conn->error);
        }
        $stmt->close();
    } else {
        echo "<div class='info'>✓ Admin user already exists!</div>";
    }
    
    // Create uploads directory if it doesn't exist
    $uploadsDir = __DIR__ . '/uploads';
    if (!is_dir($uploadsDir)) {
        if (mkdir($uploadsDir, 0755, true)) {
            echo "<div class='success'>✓ Uploads directory created!</div>";
        } else {
            echo "<div class='error'>⚠ Could not create uploads directory. Please create it manually.</div>";
        }
    } else {
        echo "<div class='success'>✓ Uploads directory already exists!</div>";
    }
    
    echo "<div style='margin-top: 30px; padding: 20px; background: #f0fdf4; border-radius: 5px;'>
        <h2 style='color: #22c55e; margin-top: 0;'>✅ Installation Complete!</h2>
        <p>Your InnovateX Event Management System is ready to use.</p>
        <p><strong>Next Steps:</strong></p>
        <ol>
            <li>Login to admin panel</li>
            <li>Add your first event</li>
            <li>Share the events page with students</li>
        </ol>
        <a href='admin_login.php' class='btn'>Go to Admin Login</a>
        <a href='events.php' class='btn' style='background: #4fb7dd;'>View Events Page</a>
    </div>";
    
    echo "<div class='info' style='margin-top: 20px;'>
        <strong>⚠️ Important:</strong> For security, delete or rename this install.php file after installation.
    </div>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
    echo "<div class='info'>
        <strong>Troubleshooting:</strong><br>
        1. Make sure XAMPP MySQL is running<br>
        2. Check your database credentials in config.php<br>
        3. Verify MySQL port (default: 3306)
    </div>";
}

echo "</div></body></html>";
?>
