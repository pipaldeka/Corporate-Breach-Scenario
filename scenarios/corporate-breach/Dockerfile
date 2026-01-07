FROM ubuntu:22.04

# Prevent interactive prompts
ENV DEBIAN_FRONTEND=noninteractive

# Install all required services
RUN apt-get update && apt-get install -y \
    sudo \
    apache2 \
    php8.1 \
    libapache2-mod-php8.1 \
    php8.1-mysqli \
    php8.1-cli \
    mariadb-server \
    openssh-server \
    vim \
    nano \
    curl \
    wget \
    net-tools \
    iputils-ping \
    cron \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache PHP module (correct way for Ubuntu 22.04)
RUN a2enmod php8.1 && \
    a2enmod rewrite

# Configure Apache to serve from /var/www/html
RUN echo '<VirtualHost *:80>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html\n\
    \n\
    <Directory /var/www/html>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
        DirectoryIndex index.html index.php\n\
    </Directory>\n\
    \n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Ensure Apache uses the correct PHP handler
RUN echo '<FilesMatch \\.php$>\n\
    SetHandler application/x-httpd-php\n\
</FilesMatch>' > /etc/apache2/conf-available/php.conf && \
    a2enconf php

# Create flag directory structure
RUN mkdir -p /flags \
    /home/developer \
    /home/webadmin \
    /var/www/html/.hidden \
    /tmp/.hidden \
    /opt/.config \
    /var/cache/.backup \
    /usr/local/share/.data && \
    chmod 755 /flags

# Copy webapp files
COPY webapp/ /var/www/html/
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    find /var/www/html -type f -exec chmod 644 {} \;

# Copy database init script
COPY database/ /docker-entrypoint-initdb.d/
RUN chmod 644 /docker-entrypoint-initdb.d/*.sql 2>/dev/null || true

# Copy and set up startup script
COPY scripts/start.sh /start.sh
RUN chmod +x /start.sh

# Configure SSH
RUN mkdir -p /var/run/sshd && \
    echo "PermitRootLogin no" >> /etc/ssh/sshd_config && \
    echo "PasswordAuthentication yes" >> /etc/ssh/sshd_config && \
    echo "PubkeyAuthentication yes" >> /etc/ssh/sshd_config

# Create vulnerable users for privilege escalation
RUN useradd -m -s /bin/bash developer && \
    echo "developer:DevPass456" | chpasswd && \
    mkdir -p /home/developer/.ssh && \
    chown -R developer:developer /home/developer && \
    chmod 700 /home/developer

RUN useradd -m -s /bin/bash webadmin && \
    echo "webadmin:SuperSecretPass123" | chpasswd && \
    usermod -aG sudo webadmin && \
    mkdir -p /home/webadmin/.ssh && \
    chown -R webadmin:webadmin /home/webadmin && \
    chmod 700 /home/webadmin

# Setup cron job for privilege escalation
COPY ssh-server/setup_cron.sh /tmp/setup_cron.sh
RUN chmod +x /tmp/setup_cron.sh && \
    bash /tmp/setup_cron.sh && \
    rm /tmp/setup_cron.sh

# Expose ports
EXPOSE 80 22

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
  CMD curl -f http://localhost/ || exit 1

# Start services
CMD ["/start.sh"]
