<?php
// scenarios/corporate-breach/webapp/admin.php

session_start();
require_once 'config.php';

// Check if logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Check if admin
if ($_SESSION['role'] !== 'admin') {
    $warning = "⚠️ This page requires admin access. You are logged in as: " . htmlspecialchars($_SESSION['role']);
}

$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - CyberCorp</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #1a1a2e;
            color: white;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #00ff88 0%, #00cc88 100%);
            color: #1a1a2e;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        h1 {
            color: #00000f;
            margin-top: 0;
        }
        .info-box {
            background: #16213e;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid #00ff88;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #16213e;
            border-radius: 10px;
            overflow: hidden;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #333;
        }
        th {
            background: #00ff88;
            color: #1a1a2e;
            font-weight: bold;
        }
        tr:hover {
            background: #1f2f4e;
        }
        .warning {
            background: #ff4444;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        code {
            background: #0f0f0f;
            padding: 4px 8px;
            border-radius: 3px;
            font-family: monospace;
            color: #0f0;
            font-size: 14px;
        }
        .credential-box {
            background: #0f3d0f;
            border: 2px solid #00ff88;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .logout {
            float: right;
            background: #15213e;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: #fff;
            border: 1px solid #000000;
        }
        .logout:hover {
            background: #15213e;
        }
        .success-box {
            background: #0f3d0f;
            border-left: 4px solid #00ff88;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="logout.php" class="logout">Logout</a>
            <h1>👨‍💼 Administrator Control Panel</h1>
            <p>Welcome, <?= htmlspecialchars($username) ?>!</p>
        </div>
        
        <?php if (isset($warning)): ?>
            <div class="warning"><?= $warning ?></div>
            <p>Try logging in as an administrator to access this panel...</p>
        <?php else: ?>
            
            <div class="success-box">
                <h2>✅ Admin Access Granted</h2>
                <p>You've successfully escalated your privileges to administrator level. 
                You now have access to sensitive system credentials.</p>
            </div>

            <div class="info-box">
                <h2>📊 System Status</h2>
                <p><strong>Server:</strong> <?= gethostname() ?></p>
                <p><strong>Internal IP:</strong> <?= gethostbyname(gethostname()) ?></p>
                <p><strong>Database Status:</strong> ✅ Connected</p>
                <p><strong>SSH Service:</strong> ✅ Running on port 22</p>
            </div>

            <div class="info-box">
                <h2>👥 Employee Database</h2>
                <?php
                $employees_query = "SELECT id, username, role, email, name, position FROM employees";
                $employees_result = $conn->query($employees_query);
                if ($employees_result && $employees_result->num_rows > 0):
                ?>
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Position</th>
                        <th>Email</th>
                    </tr>
                    <?php while($employee = $employees_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $employee['id'] ?></td>
                        <td><?= htmlspecialchars($employee['username']) ?></td>
                        <td><?= htmlspecialchars($employee['name']) ?></td>
                        <td><?= htmlspecialchars($employee['role']) ?></td>
                        <td><?= htmlspecialchars($employee['position']) ?></td>
                        <td><?= htmlspecialchars($employee['email']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
                <?php endif; ?>
            </div>

            <!-- SSH CREDENTIALS SECTION -->
            <div class="credential-box">
                <h2>🔐 System Credentials</h2>
                <p><strong>⚠️ CONFIDENTIAL - ADMIN EYES ONLY ⚠️</strong></p>
                
                <?php
                $creds_query = "SELECT * FROM admin_credentials ORDER BY service";
                $creds_result = $conn->query($creds_query);
                if ($creds_result && $creds_result->num_rows > 0):
                ?>
                <table>
                    <tr>
                        <th>Service</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Notes</th>
                    </tr>
                    <?php while($cred = $creds_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($cred['service']) ?></td>
                        <td><code><?= htmlspecialchars($cred['username']) ?></code></td>
                        <td><code><?= htmlspecialchars($cred['password']) ?></code></td>
                        <td><?= htmlspecialchars($cred['notes']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </table>
                <?php endif; ?>
                
                <div style="margin-top: 20px; padding: 15px; background: rgba(255,200,0,0.1); border-left: 4px solid #ffcc00;">
                    <strong>🎯 Next Objective:</strong><br>
                    Use the SSH credentials above to access the server remotely.<br>
                    <strong>SSH Access:</strong> <code>ssh webadmin@<?= gethostbyname(gethostname()) ?> -p 22</code><br>
                    <strong>From Host:</strong> <code>ssh webadmin@localhost -p 2319</code>
                </div>
            </div>

            <div class="info-box">
                <h2>📝 Admin Audit Logs</h2>
                <p><em>Last login: <?= date('Y-m-d H:i:s') ?></em></p>
                <p><em>Session ID: <?= session_id() ?></em></p>
            </div>

        <?php endif; ?>
    </div>
</body>
</html>
