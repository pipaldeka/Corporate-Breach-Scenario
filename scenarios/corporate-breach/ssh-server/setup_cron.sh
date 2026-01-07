#!/bin/bash
# Setup vulnerable cron job for privilege escalation training

set -e  # Exit on error

echo "[*] Setting up cron-based privilege escalation vector..."

# Create scripts directory
echo "[+] Creating /opt/scripts directory..."
mkdir -p /opt/scripts

# Create the vulnerable backup script
echo "[+] Creating vulnerable backup script..."
cat > /opt/scripts/backup.sh << 'SCRIPT_EOF'
#!/bin/bash
# Automated backup script - runs every minute as root
# Backs up important web application files

BACKUP_DIR="/var/backups/webapp"
SOURCE_DIR="/var/www/html"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Create backup directory if it doesn't exist
mkdir -p "$BACKUP_DIR"

# Perform backup (suppress errors to avoid log spam)
tar -czf "$BACKUP_DIR/backup_$TIMESTAMP.tar.gz" "$SOURCE_DIR" 2>/dev/null || true

# Keep only last 10 backups to save space
ls -t "$BACKUP_DIR"/backup_*.tar.gz 2>/dev/null | tail -n +11 | xargs -r rm -f

# Log backup completion
echo "$(date '+%Y-%m-%d %H:%M:%S'): Backup completed - backup_$TIMESTAMP.tar.gz" >> /var/log/backup.log
SCRIPT_EOF

# Make script executable
echo "[+] Making backup script executable..."
chmod +x /opt/scripts/backup.sh

# VULNERABILITY: Make script writable by webadmin group
echo "[+] Configuring VULNERABLE permissions (group-writable)..."
chown root:webadmin /opt/scripts/backup.sh
chmod 775 /opt/scripts/backup.sh  # Group-writable - THIS IS THE VULNERABILITY!

# Ensure webadmin user is in webadmin group
echo "[+] Adding webadmin to webadmin group..."
usermod -aG webadmin webadmin

# Create cron job (runs every minute as root)
echo "[+] Creating cron job (runs every minute as root)..."
cat > /etc/cron.d/backup-job << 'CRON_EOF'
# Automated backup job - runs every minute
# WARNING: This is intentionally misconfigured for training purposes
* * * * * root /opt/scripts/backup.sh >/dev/null 2>&1
CRON_EOF

# Set proper permissions on cron file
chmod 644 /etc/cron.d/backup-job

# Create backup directory with proper permissions
echo "[+] Creating backup directory..."
mkdir -p /var/backups/webapp
chown root:root /var/backups/webapp
chmod 755 /var/backups/webapp

# Create log file
echo "[+] Creating backup log file..."
touch /var/log/backup.log
chmod 644 /var/log/backup.log


# Display vulnerability info (for testing/debugging)
echo ""
echo "======================================"
echo "   PRIVILEGE ESCALATION VECTOR READY"
echo "======================================"
echo ""
echo "[!] Vulnerable Script: /opt/scripts/backup.sh"
echo "[!] Permissions: -rwxrwxr-x (group-writable!)"
echo "[!] Owner: root:webadmin"
echo "[!] Cron Job: Runs every minute as root"
echo "[!] Cron File: /etc/cron.d/backup-job"
echo ""
echo "[+] Exploitation:"
echo "    1. SSH as webadmin"
echo "    2. Modify /opt/scripts/backup.sh"
echo "    3. Wait up to 60 seconds"
echo "    4. Script executes with root privileges"
echo ""
echo "======================================"
echo ""

# Test cron service
echo "[+] Testing cron service..."
service cron status || service cron start

echo "[✓] Cron-based privilege escalation setup complete!"
