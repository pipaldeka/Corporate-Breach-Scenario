FROM ubuntu:22.04

# Prevent interactive prompts
ENV DEBIAN_FRONTEND=noninteractive

# Install all required services
RUN apt-get update && apt-get install -y \
    apache2 \
    php \
    php-mysqli \
    mariadb-server \
    openssh-server \
    sudo \
    vim \
    nano \
    curl \
    wget \
    net-tools \
    iputils-ping \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache PHP module
RUN a2enmod php8.1 2>/dev/null || a2enmod php || true

# Create flag directory structure (empty, Ansible will populate)
# Create all potential directories that flags might be placed in
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
    chmod -R 755 /var/www/html

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

# Add sudo configuration for privilege escalation challenge
# This creates a vulnerability students can exploit
RUN echo "webadmin ALL=(ALL) NOPASSWD: /usr/bin/vim" >> /etc/sudoers.d/webadmin && \
    chmod 440 /etc/sudoers.d/webadmin

# Set proper permissions for Apache
RUN chown -R www-data:www-data /var/www && \
    chmod -R 755 /var/www

# Expose ports
EXPOSE 80 22

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
  CMD curl -f http://localhost/ || exit 1

# Start services
CMD ["/start.sh"]
