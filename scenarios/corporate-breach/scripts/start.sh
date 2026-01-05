#!/bin/bash
# scenarios/corporate-breach/scripts/start.sh

set -e

echo "==================================="
echo "Starting Corporate Breach Services"
echo "==================================="

# Fix MariaDB permissions
mkdir -p /var/run/mysqld
chown mysql:mysql /var/run/mysqld
chmod 755 /var/run/mysqld

# Start MariaDB
echo "Starting MariaDB..."
service mariadb start

# Wait for MariaDB to be ready
echo "Waiting for MariaDB..."
for i in {1..30}; do
    if mysqladmin ping -h localhost --silent 2>/dev/null; then
        echo "✅ MariaDB is ready!"
        break
    fi
    if [ $i -eq 30 ]; then
        echo "❌ MariaDB failed to start"
        exit 1
    fi
    echo "Waiting... ($i/30)"
    sleep 2
done

# Create database and user
echo "Setting up database..."
mysql -u root <<'EOF'
CREATE DATABASE IF NOT EXISTS corporate_db;

DROP USER IF EXISTS 'webapp_user'@'localhost';
DROP USER IF EXISTS 'webapp_user'@'%';

CREATE USER 'webapp_user'@'localhost' IDENTIFIED BY 'myPass123';
CREATE USER 'webapp_user'@'%' IDENTIFIED BY 'myPass123';

GRANT ALL PRIVILEGES ON corporate_db.* TO 'webapp_user'@'localhost';
GRANT ALL PRIVILEGES ON corporate_db.* TO 'webapp_user'@'%';

FLUSH PRIVILEGES;
EOF

echo "✅ Database and users created!"

# Load database schema if exists
if [ -f /docker-entrypoint-initdb.d/init.sql ]; then
    echo "Loading database schema..."
    mysql -u root corporate_db < /docker-entrypoint-initdb.d/init.sql
    echo "✅ Schema loaded!"
else
    echo "⚠️  No init.sql found, skipping schema load"
fi

# Verify database setup
echo "Verifying database..."
if mysql -u webapp_user -pmyPass123 corporate_db -e "SELECT COUNT(*) as employee_count FROM employees;" 2>/dev/null; then
    echo "✅ Database verification successful!"
else
    echo "⚠️  Could not verify employees table (might not exist yet)"
fi

# Start SSH
echo "Starting SSH..."
mkdir -p /var/run/sshd
service ssh start
echo "✅ SSH started!"

# Start Apache in foreground (keeps container running)
echo "Starting Apache..."
echo "==================================="
echo "✅ All services started!"
echo "==================================="
echo "Web: http://localhost"
echo "SSH: Port 22"
echo "==================================="

# Start Apache in foreground
exec apache2ctl -D FOREGROUND
