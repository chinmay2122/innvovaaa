<?php
// Migration Script - Add Team Members and Blogs Tables
// Run this file ONCE to add new tables to existing database

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'innovatex_events';

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Database Migration | InnovateX</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; background: #f5f5f5; }
        .container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { color: #010101; }
        .success { color: #22c55e; padding: 10px; background: #f0fdf4; border-left: 4px solid #22c55e; margin: 10px 0; }
        .error { color: #ef4444; padding: 10px; background: #fef2f2; border-left: 4px solid #ef4444; margin: 10px 0; }
        .info { color: #3b82f6; padding: 10px; background: #eff6ff; border-left: 4px solid #3b82f6; margin: 10px 0; }
        .btn { display: inline-block; padding: 10px 20px; background: #010101; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        .btn:hover { background: #333; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔄 Database Migration</h1>
        <p>Adding new tables for Team Members and Blogs...</p>";

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    echo "<div class='success'>✓ Connected to database successfully!</div>";
    
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
    
    // Create contact_queries table
    $sql = "CREATE TABLE IF NOT EXISTS contact_queries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        phone VARCHAR(20),
        subject VARCHAR(200) NOT NULL,
        message TEXT NOT NULL,
        status ENUM('unread', 'read') DEFAULT 'unread',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='success'>✓ Table 'contact_queries' created successfully!</div>";
    } else {
        throw new Exception("Error creating contact_queries table: " . $conn->error);
    }
    
    echo "<div style='margin-top: 30px; padding: 20px; background: #f0fdf4; border-radius: 5px;'>
        <h2 style='color: #22c55e; margin-top: 0;'>✅ Migration Complete!</h2>
        <p>New tables have been added successfully. You can now:</p>
        <ul>
            <li>Add team members through admin panel</li>
            <li>View the About page</li>
            <li>Continue using all features</li>
        </ul>
        <a href='admin_dashboard.php' class='btn'>Go to Admin Dashboard</a>
        <a href='about.php' class='btn' style='background: #4fb7dd;'>View About Page</a>
    </div>";
    
    echo "<div class='info' style='margin-top: 20px;'>
        <strong>⚠️ Important:</strong> Delete this migrate.php file after successful migration for security.
    </div>";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<div class='error'>❌ Error: " . $e->getMessage() . "</div>";
    echo "<div class='info'>
        <strong>Troubleshooting:</strong><br>
        1. Make sure XAMPP MySQL is running<br>
        2. Verify database 'innovatex_events' exists<br>
        3. Check your database credentials in this file
    </div>";
}

echo "</div></body></html>";
?>
