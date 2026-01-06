FROM ubuntu:22.04

# Prevent interactive prompts during build
ENV DEBIAN_FRONTEND=noninteractive

# Install necessary packages
RUN apt-get update && \
    apt-get install -y \
    openssh-server \
    sudo \
    vim \
    nano \
    curl \
    wget \
    net-tools \
    iputils-ping \
    cron \
    tar \
    gzip \
    && rm -rf /var/lib/apt/lists/*

# Create webadmin user with home directory
RUN useradd -m -s /bin/bash webadmin && \
    echo 'webadmin:SuperSecretPass123' | chpasswd && \
    mkdir -p /home/webadmin

# Configure SSH
RUN mkdir -p /var/run/sshd && \
    sed -i 's/#PermitRootLogin prohibit-password/PermitRootLogin no/' /etc/ssh/sshd_config && \
    sed -i 's/#PasswordAuthentication yes/PasswordAuthentication yes/' /etc/ssh/sshd_config && \
    sed -i 's/#PubkeyAuthentication yes/PubkeyAuthentication yes/' /etc/ssh/sshd_config

# Create FLAG2 in webadmin's home directory
RUN echo "FLAG{rahul_SSH_access_granted_8x3K9mP2}" > /home/webadmin/flag2.txt && \
    chown webadmin:webadmin /home/webadmin/flag2.txt && \
    chmod 400 /home/webadmin/flag2.txt

# Create FLAG3 (root flag) in root's home directory
RUN echo "FLAG{rahul_root_pwned_via_cron_7Q2nM9pK5rT8w}" > /root/flag3.txt && \
    chmod 400 /root/flag3.txt
