# FREISM Final Project — Step-by-Step Deployment Guide

This document walks you through every requirement of the FREISM Final Project,
from Git branching to AWS EC2 deployment. Follow each step in order.

---

## What You Need Before Starting

1. **An AWS account** — sign up at https://aws.amazon.com/ (use the free tier or your
   student credits)
2. **Git installed on your computer** — download from https://git-scm.com/downloads
3. **A GitHub account** — create one at https://github.com/ if you don't have one
4. **Your group members' full names** — to put on the landing page
5. **Photos of each group member** — save as `member1.jpg`, `member2.jpg`, etc.

---

## PHASE 1 — Git Branch & Landing Page (Do this on your computer)

### Step 1: Initialize Git in the PHP project

Open a terminal (Command Prompt, PowerShell, or Git Bash) in the `sewerbot-php` folder:

```bash
cd "C:\Users\Aireen Antiola\OneDrive\Desktop\NEW ARVIN FILES 4TH YR\SEWERBOT FREISM\sewerbot-php"

git init
git add .
git commit -m "initial: SewerBot PHP web application converted from Kotlin"
```

### Step 2: Create a GitHub repository

1. Go to https://github.com/new
2. Name it something like `sewerbot-freism`
3. Set it to **Public** (so the instructor can see it)
4. Do NOT check "Add a README"
5. Click "Create repository"

### Step 3: Push the initial code to GitHub

```bash
git remote add origin https://github.com/YOUR_USERNAME/sewerbot-freism.git
git branch -M main
git push -u origin main
```

### Step 4: Create the FREISM branch

```bash
git switch -c feat/freism-ec2
```

### Step 5: Customize the landing page

1. Open `public/index.php` in a text editor
2. Replace the placeholder names with your actual group members' names
3. Place member photos in `public/members/` as `member1.jpg`, `member2.jpg`, etc.
4. Update the `<img>` tags in `index.php` to point to the photos:

   Replace this line:
   ```html
   <span class="placeholder">👤</span>
   ```
   
   With this (for each member):
   ```html
   <img src="members/member1.jpg" alt="Member 1 Name">
   ```

5. Save the file

### Step 6: Commit and push the FREISM branch

```bash
git add .
git commit -m "feat: add FREISM landing page with group members for EC2 deployment"
git push -u origin feat/freism-ec2
```

### Step 7: Verify the branch on GitHub

1. Go to your repository on GitHub
2. Click the branch dropdown (it says "main")
3. You should see `feat/freism-ec2` — select it
4. Verify your landing page changes are there

### Step 8: Take screenshots for documentation

Take screenshots of:
- [ ] The branch in your local terminal: `git branch -v`
- [ ] The commit in your local terminal: `git log --oneline -5`
- [ ] The branch on GitHub (the branch dropdown)
- [ ] The push output in your terminal

---

## PHASE 2 — AWS EC2 Setup (Do this in the AWS Console)

### Step 9: Create an EC2 Instance

1. Log in to https://console.aws.amazon.com/
2. In the search bar at top, type "EC2" and click it
3. Click **"Launch Instance"**
4. Fill in:
   - **Name**: `sewerbot-freism`
   - **OS**: Click "Ubuntu" → select **Ubuntu Server 24.04 LTS** (the free-tier eligible one)
   - **Instance type**: `t3.micro` (this is enough for a PHP app)
   - **Key pair**: Click "Create new key pair"
     - Name it: `sewerbot-key`
     - Type: RSA
     - Format: .pem (for Mac/Linux) or .ppk (for Windows/PuTTY)
     - Click "Create key pair"
     - **IMPORTANT: Save the downloaded file somewhere safe. You cannot download it again.**
   - **Network settings**: Leave default (it creates a security group for you)
   - **Storage**: Leave at default (8 GB is fine)
5. Click **"Launch instance"**
6. Wait for the instance state to show **"Running"**

Take screenshots of:
- [ ] The EC2 instance running with name and type visible
- [ ] The instance type (t3.micro)
- [ ] The OS (Ubuntu 24.04 LTS)

### Step 10: Create the SSH Key Pair

You already did this in Step 9 when you clicked "Create new key pair".

Take a screenshot of:
- [ ] The key pair name in EC2 → Key Pairs section

> **SECURITY**: Never put the private key file contents in your PDF, GitHub, 
> or anywhere public. Keep it on your computer only.

### Step 11: Allocate and Associate an Elastic IP

1. In the EC2 dashboard, scroll the left sidebar and click **"Elastic IPs"**
2. Click **"Allocate Elastic IP address"**
3. Click **"Allocate"**
4. You now have a static IP. Select it, then click **"Associate"**
5. Select your `sewerbot-freism` instance
6. Click **"Associate"**
7. Copy the Elastic IP address — you'll need it later

Take screenshots of:
- [ ] The Elastic IP allocated
- [ ] The Elastic IP associated with your instance

### Step 12: Configure Security Group Inbound Rules

1. In the EC2 dashboard, click **"Security Groups"** in the left sidebar
2. Click the security group associated with your instance
3. Click the **"Inbound rules"** tab, then **"Edit inbound rules"**
4. Add these rules:

   | Type | Port | Source | Purpose |
   |------|------|--------|---------|
   | SSH | 22 | My IP (or a specific IP) | Remote admin |
   | HTTP | 80 | 0.0.0.0/0 (Anywhere) | Public web access |
   | Custom TCP | 8080 | 0.0.0.0/0 (Anywhere) | PHP dev server (if using port 8080) |

   - For SSH, click the source field and select "My IP" — this restricts SSH to 
     only your current public IP for security
   - For HTTP and Custom TCP, select "Anywhere-IPv4"

5. Click **"Save rules"**

Take a screenshot of:
- [ ] All inbound rules listed

---

## PHASE 3 — Server Configuration (Do this via SSH)

### Step 13: Connect to EC2 via SSH

**On Windows (using PowerShell or Command Prompt):**

```bash
ssh -i "C:\path\to\your\sewerbot-key.pem" ubuntu@YOUR_ELASTIC_IP
```

- Replace `C:\path\to\your\sewerbot-key.pem` with the actual path to your key file
- Replace `YOUR_ELASTIC_IP` with your Elastic IP (e.g., `13.212.45.67`)
- The username is `ubuntu` (not "root" or "ec2-user")
- Type `yes` when it asks about the fingerprint

If you get a "permissions too open" error on Windows, fix the key file permissions:

**In PowerShell (Run as Administrator):**
```powershell
icacls "C:\path\to\your\sewerbot-key.pem" /inheritance:r
icacls "C:\path\to\your\sewerbot-key.pem" /grant:r "$($env:USERNAME):(R)"
```

Take a screenshot of:
- [ ] The SSH terminal showing you're logged in (the prompt shows `ubuntu@...`)

### Step 14: Install Server Software

Once you're connected via SSH, run these commands one at a time:

```bash
# Update the system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.1+ and required extensions
sudo apt install -y php8.3 php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip unzip

# Install Composer (PHP package manager)
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Git
sudo apt install -y git

# Install Nginx (production web server)
sudo apt install -y nginx

# Verify installations
php -v
composer --version
git --version
nginx -v
```

Take screenshots of:
- [ ] PHP version output
- [ ] Composer installed
- [ ] Nginx installed

### Step 15: Clone the GitHub Repository

```bash
cd /var/www
sudo mkdir sewerbot
sudo chown ubuntu:ubuntu sewerbot
cd sewerbot

# Clone YOUR repository (replace with your URL)
git clone https://github.com/YOUR_USERNAME/sewerbot-freism.git .
```

Take a screenshot of:
- [ ] The cloned files (run `ls -la` to show them)

### Step 16: Switch to the FREISM Branch

```bash
git fetch origin
git switch feat/freism-ec2
git branch -v
```

Verify:
- The branch name shows `feat/freism-ec2`
- The latest commit message is visible

Take a screenshot of:
- [ ] `git branch -v` output showing the correct branch

### Step 17: Install PHP Dependencies

```bash
composer install --no-dev --optimize-autoloader
```

Take a screenshot of:
- [ ] Composer installing dependencies successfully

### Step 18: Set Permissions

```bash
# Set the web server user as owner
sudo chown -R www-data:www-data /var/www/sewerbot
sudo chmod -R 755 /var/www/sewerbot
```

---

## PHASE 4 — Web Server & systemd Configuration

### Step 19: Configure Nginx

Create the Nginx configuration file:

```bash
sudo nano /etc/nginx/sites-available/sewerbot
```

Paste this configuration (copy from here, then in the terminal: right-click to paste 
in PuTTY, or Ctrl+Shift+V in most Linux terminals):

```nginx
server {
    listen 80;
    server_name _;
    
    root /var/www/sewerbot/public;
    index index.php index.html;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.3-fpm.sock;
    }
    
    location ~ /\. {
        deny all;
    }
}
```

Save and exit (Ctrl+O, Enter, Ctrl+X).

Enable the site and restart Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/sewerbot /etc/nginx/sites-enabled/
sudo rm /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl restart nginx
```

### Step 20: Install and Start PHP-FPM

```bash
sudo apt install -y php8.3-fpm
sudo systemctl enable php8.3-fpm
sudo systemctl start php8.3-fpm
```

### Step 21: Create the systemd Service

Even though Nginx already has a systemd service, the instructions say to create
a service for your application. This service will manage both Nginx and PHP-FPM
together as one unit:

```bash
sudo nano /etc/systemd/system/freism-frontend.service
```

Paste this:

```ini
[Unit]
Description=SewerBot FREISM Frontend Service
After=network.target

[Service]
Type=oneshot
RemainAfterExit=yes
ExecStart=/usr/bin/php-fpm8.3 -F
ExecStop=/bin/kill -QUIT $MAINPID
Restart=on-failure
User=www-data
Group=www-data

[Install]
WantedBy=multi-user.target
```

Wait — that approach is complex. A simpler and more reliable approach is to
create a service that manages Nginx (since Nginx is your web server):

```ini
[Unit]
Description=SewerBot FREISM Frontend (Nginx)
After=network.target

[Service]
Type=simple
ExecStart=/usr/sbin/nginx -g "daemon off;"
ExecStop=/usr/sbin/nginx -s stop
Restart=on-failure
User=root

[Install]
WantedBy=multi-user.target
```

Save and exit (Ctrl+O, Enter, Ctrl+X).

### Step 22: Enable and Start the Service

```bash
sudo systemctl daemon-reload
sudo systemctl enable --now freism-frontend.service
sudo systemctl status freism-frontend.service
sudo systemctl is-enabled freism-frontend.service
```

Take screenshots of:
- [ ] The service file contents (`cat /etc/systemd/system/freism-frontend.service`)
- [ ] `systemctl status` showing active (running)
- [ ] `systemctl is-enabled` showing "enabled"

### Step 24: Test Public Accessibility

1. Open a browser on your computer
2. Go to: `http://YOUR_ELASTIC_IP`
3. You should see the FREISM landing page with course info and group members
4. Click "Get Started" to verify the login page works
5. To prove it survives SSH disconnect: close your SSH terminal, then refresh 
   the browser — the site should still be up

Take screenshots of:
- [ ] The landing page in a browser showing the Elastic IP
- [ ] The login page accessible via the Elastic IP
- [ ] The site still working after SSH disconnect

---

## PHASE 5 — Documentation & Submission

### What to put in your PDF

Compile all your screenshots and explanations into a single PDF with these sections:

**Part 1 — Project Information**
- Project title: "SewerBot — Sewer Pipeline Inspection Robot System"
- Group member names
- Course of origin: ELDROID (or CAPSTONE)
- Brief description
- Technologies: PHP 8.3, Nginx, Firebase (Auth, Firestore, RTDB), Ubuntu 24.04 LTS, AWS EC2
- GitHub repository URL
- Branch name: `feat/freism-ec2`

**Part 2 — Application Development Evidence**
- Screenshot of original Kotlin Android project
- Screenshot of converted PHP project directory
- Screenshot of modified FREISM landing page
- Screenshot of branch creation
- Screenshot of branch-specific commit
- Screenshot of push to GitHub

**Part 3 — AWS Deployment Documentation**
- All screenshots from Steps 9-24, in order, with brief explanations

**Part 4 — Final Deployment Evidence**
- GitHub repository and branch name
- EC2 instance type (t3.micro)
- Elastic IP address
- Application port (80)
- Full public URL: `http://YOUR_ELASTIC_IP`
- systemd service name: `freism-frontend.service`
- Screenshot of active and enabled service
- Screenshot of final deployed landing page
- Screenshot showing site works after SSH disconnect

**Part 5 — Technical Essay Questions**
- Answer all 10 questions from the project instructions
- Relate each answer to your actual deployment experience

---

## Quick Reference: All Commands in Order

```bash
# === On your computer ===
git init && git add . && git commit -m "initial: SewerBot PHP"
git remote add origin https://github.com/YOUR_USERNAME/sewerbot-freism.git
git branch -M main && git push -u origin main
git switch -c feat/freism-ec2
# ... edit index.php with member names and photos ...
git add . && git commit -m "feat: add FREISM landing page for EC2 deployment"
git push -u origin feat/freism-ec2
git branch -v

# === On EC2 via SSH ===
ssh -i sewerbot-key.pem ubuntu@YOUR_ELASTIC_IP
sudo apt update && sudo apt upgrade -y
sudo apt install -y php8.3 php8.3-cli php8.3-fpm php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip unzip git nginx
curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer
cd /var/www && sudo mkdir sewerbot && sudo chown ubuntu:ubuntu sewerbot && cd sewerbot
git clone https://github.com/YOUR_USERNAME/sewerbot-freism.git .
git switch feat/freism-ec2 && git branch -v
composer install --no-dev --optimize-autoloader
sudo chown -R www-data:www-data /var/www/sewerbot && sudo chmod -R 755 /var/www/sewerbot
# Configure Nginx (see Step 19)
sudo systemctl restart nginx
# Create systemd service (see Steps 21-22)
sudo systemctl daemon-reload && sudo systemctl enable --now freism-frontend.service
sudo systemctl status freism-frontend.service
```
