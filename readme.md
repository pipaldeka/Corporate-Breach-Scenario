# Corporate Breach CTF

A containerized Capture The Flag (CTF) scenario designed for learning 
web application security and Linux privilege escalation.

![Docker](https://img.shields.io/badge/Docker-Required-blue)
![Difficulty](https://img.shields.io/badge/Difficulty-Easy--Medium-yellow)
![License](https://img.shields.io/badge/License-MIT-green)

## Learning Objectives

- SQL Injection (basic → advanced)
- Authentication bypass techniques
- SSH enumeration
- Linux privilege escalation (cron misconfiguration)
- CTF methodology

## Architecture
```
┌─────────────────────────────┐
│   Web Application (PHP)     │ ← SQL Injection
│   Port 8119                  │
└─────────────────────────────┘
           ↓
┌─────────────────────────────┐
│   MySQL Database            │
└─────────────────────────────┘
           ↓
┌─────────────────────────────┐
│   SSH Server (Ubuntu)       │ ← Privilege Escalation
│   Port 2319                  │
└─────────────────────────────┘
```

## Quick Start

### Prerequisites
- Docker
- Docker Compose
- 2GB free RAM
- 5GB free disk space

### Setup
```bash
# Clone repository
git clone https://github.com/yourusername/corporate-breach-ctf.git
cd corporate-breach-ctf

# Start containers
docker-compose up -d

# Verify services
docker-compose ps
```

### Access Points

- **Web Application:** http://localhost:8119
- **SSH Server:** `ssh webadmin@localhost -p 2319`

## Challenge Overview

### 3 Flags to Capture:

| Flag | Challenge | Difficulty |
|------|-----------|------------|
| FLAG1 | SQL Injection → User Access | Easy |
| FLAG2 | SSH Access | Easy |
| FLAG3 | Privilege Escalation → Root | Medium |

## Walkthrough

**Beginner?** Start here: [SOLUTION.md](docs/SOLUTION.md)

**Want hints only?** Check the web app's login page and SSH server's HINTS.txt

## Technical Details

**Tech Stack:**
- Docker & Docker Compose
- PHP 7.4 + Apache
- MySQL 8.0
- Ubuntu 22.04
- OpenSSH

**Vulnerabilities:**
- SQL Injection (authentication bypass)
- Cron job misconfiguration
- File permission issues

## Documentation

- [Complete Solution](docs/SOLUTION.md)
- [Architecture Details](docs/ARCHITECTURE.md)
- [Setup Guide](docs/SETUP.md)

## Security Warning

**This is intentionally vulnerable software for educational purposes.**

- ❌ **DO NOT** deploy in production
- ❌ **DO NOT** expose to public internet
- ✅ **USE** in isolated lab environments only
- ✅ **USE** for learning and teaching


**Ideas for improvements:**
- Additional vulnerabilities (XSS, CSRF, etc.)
- More privilege escalation vectors
- Multi-machine scenarios
- Kubernetes deployment
- Integration of monitoring platform (ELK stack + Agentic AI)

## 📄 License

MIT License - see [LICENSE](LICENSE)

##  Contact

[Pipal-Deka]: https://www.linkedin.com/in/pipal-deka-0a1808190/



- LinkedIn: [Pipal-Deka]
- Email: 21pipal.deka@gmail.com

---

**⭐ If you found this helpful, please star the repo!**
