<?php
// scenarios/corporate-breach/webapp/dashboard.php

session_start();
require_once 'config.php';

// Check if logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Redirect admins to their page
if ($_SESSION['role'] == 'admin') {
    header("Location: admin.php");
    exit();
}

// Get user info
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Get announcements
$announcements_query = "SELECT * FROM announcements ORDER BY posted_date DESC LIMIT 5";
$announcements_result = $conn->query($announcements_query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Employee Dashboard - CyberCorp</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .card h3 {
            color: #667eea;
            margin-top: 0;
        }
        .success {
            color: green;
            background: #e8f5e9;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #4caf50;
        }
        .announcement {
            border-left: 3px solid #667eea;
            padding-left: 15px;
            margin: 15px 0;
        }
        .announcement-title {
            font-weight: bold;
            color: #333;
        }
        .announcement-meta {
            font-size: 12px;
            color: #999;
        }
        .logout {
            float: right;
            background: rgba(255,255,255,0.2);
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
        }
        .logout:hover {
            background: rgba(255,255,255,0.3);
        }
        code {
            background: #333;
            color: #0f0;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 15px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="logout.php" class="logout">Logout</a>
            <h1>👤 Employee Dashboard</h1>
            <p>Welcome, <?= htmlspecialchars($username) ?>!</p>
        </div>

        <!-- FLAG 1 SECTION -->
        <div class="card">
            <h3>🎉 Welcome Message</h3>
            <div class="success">
                <strong>✅ Authentication Successful!</strong><br><br>
                Congratulations on gaining access to the employee portal!<br><br>
                🚩 <strong>FLAG 1:</strong> <?= file_get_contents('/flags/flag1.txt') ?>
            </div>
            
            <div class="info-box">
                <strong>💡 Next Steps:</strong><br>
                You've successfully exploited the SQL injection vulnerability and gained user-level access. 
                But there's more to discover... Can you find a way to access the admin panel?
            </div>
        </div>

        <!-- ANNOUNCEMENTS SECTION -->
        <div class="card">
            <h3>📢 Company Announcements</h3>
            <?php if ($announcements_result && $announcements_result->num_rows > 0): ?>
                <?php while($announcement = $announcements_result->fetch_assoc()): ?>
                    <div class="announcement">
                        <div class="announcement-title"><?= htmlspecialchars($announcement['title']) ?></div>
                        <div class="announcement-meta">
                            Posted by <?= htmlspecialchars($announcement['posted_by']) ?> 
                            on <?= htmlspecialchars($announcement['posted_date']) ?>
                        </div>
                        <p><?= htmlspecialchars($announcement['content']) ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No announcements at this time.</p>
            <?php endif; ?>
        </div>

        <!-- USER INFO SECTION -->
        <div class="card">
            <h3>👤 My Profile</h3>
            <?php
            $user_query = "SELECT * FROM employees WHERE id = " . $user_id;
            $user_result = $conn->query($user_query);
            if ($user_result && $user_result->num_rows > 0) {
                $user_data = $user_result->fetch_assoc();
            ?>
                <p><strong>Name:</strong> <?= htmlspecialchars($user_data['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user_data['email']) ?></p>
                <p><strong>Position:</strong> <?= htmlspecialchars($user_data['position']) ?></p>
                <p><strong>Role:</strong> <?= htmlspecialchars($user_data['role']) ?></p>
            <?php } ?>
        </div>

        <div class="card">
            <h3>🔍 Enumeration Hint</h3>
            <p>User-level access is just the beginning. Administrators have access to sensitive 
            system credentials. Try to find a way to access the admin panel...</p>
            <p><strong>Hint:</strong> The admin's username is <code>admin</code>. But what's the password?</p>
        </div>
    </div>
</body>
</html>
