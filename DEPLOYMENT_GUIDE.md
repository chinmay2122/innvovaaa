# 🚀 RAILWAY DEPLOYMENT GUIDE - InnovateX Events Platform

**Security Status: ✅ HARDENED & READY FOR PRODUCTION**

---

## 📋 PRE-DEPLOYMENT CHECKLIST

### 1. Security Review ✅
- [x] SQL Injection protection (prepared statements)
- [x] XSS protection (output escaping)
- [x] CSRF protection (tokens on all forms)
- [x] Authentication hardening (session regeneration, rate limiting)
- [x] File upload security (MIME type validation, .htaccess)
- [x] Security headers (CSP, X-Frame-Options, etc.)
- [x] Session security (HTTPOnly, Secure, SameSite cookies)

### 2. Environment Setup
- [ ] Create Railway account if not already done
- [ ] Have production database credentials ready
- [ ] Have admin credentials ready (strong password)
- [ ] SSL certificate ready (Railway provides free SSL)

### 3. Code Preparation
- [ ] Review all changes in SECURITY_AUDIT_REPORT.md
- [ ] Ensure .env.example is in repository
- [ ] Verify .gitignore contains .env and sensitive files
- [ ] Test locally with HTTPS enabled

---

## 🚀 STEP-BY-STEP DEPLOYMENT

### Step 1: Prepare Repository

```bash
# Ensure you're in the project root
cd c:\xampp\htdocs\Inno-Webo

# Add all security files
git add .env.example .gitignore SECURITY_AUDIT_REPORT.md DEPLOYMENT_GUIDE.md

# Commit changes
git commit -m "chore: security hardening and production setup"

# Push to your repository
git push origin main
```

### Step 2: Create Railway Project

1. Go to https://railway.app
2. Click "New Project" → "Deploy from GitHub"
3. Select your repository
4. Railway will auto-detect PHP and MySQL

### Step 3: Configure Environment Variables

In Railway dashboard, go to Variables:

```
DB_HOST=your_railway_db_host
DB_USER=your_railway_db_user
DB_PASS=your_very_strong_password
DB_NAME=innovatex_events
HTTPS_ENABLED=true
ENVIRONMENT=production
```

**Never commit .env to Git!** Railway reads from the Variables tab.

### Step 4: Configure Database

#### Option A: Use Railway's MySQL Plugin (Recommended)
1. In Railway project, add MySQL plugin
2. Copy connection details to environment variables
3. Run migrations (see Step 5)

#### Option B: External Database
1. Ensure your database is accessible from Railway
2. Add credentials to environment variables

### Step 5: Run Database Migrations

Connect to Railway via SSH or use their dashboard:

```bash
# Access the container
# Option: Use railway CLI
railway run bash

# Or use the web terminal in Railway dashboard

# Run migration script
php migrate.php
```

This will:
- Create all necessary tables
- Set up default admin account (if needed)
- Create logs/ directory

### Step 6: Configure PHP Settings

Railway automatically uses sensible PHP defaults. To customize:

1. Create `php.ini` in project root:

```ini
; Production Configuration
display_errors = Off
error_reporting = E_ALL
log_errors = On
error_log = /var/log/railway/php-error.log

; Session Security
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = Strict
session.use_strict_mode = 1
session.gc_maxlifetime = 1800

; File Upload
upload_max_filesize = 5M
post_max_size = 5M
max_file_uploads = 10

; Performance
memory_limit = 256M
max_execution_time = 30

; Security - HTTPS
session.cookie_secure = 1
```

2. Update Procfile (if needed):

```
web: php -S 0.0.0.0:${PORT} -t public/
```

### Step 7: Configure HTTPS/SSL

Railway automatically provides HTTPS. To enable in your app:

1. Update config.php to detect HTTPS:

```php
// Already done in latest version
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
    ini_set('session.cookie_secure', 1);
}
```

2. Add HTTP → HTTPS redirect in config.php:

```php
// Force HTTPS in production
if (getenv('ENVIRONMENT') === 'production' && 
    empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}
```

### Step 8: Set Up Logs

Railway provides log streaming. Your app logs to `/var/log/railway/php-error.log` automatically.

To monitor:
1. Go to Railway Dashboard → Deployments
2. Click your deployment → Logs tab
3. View real-time logs

### Step 9: Create Admin User

Option A: Via migration
```bash
railway run php install.php
# Follow the prompts
```

Option B: Manually
```sql
INSERT INTO admins (username, password) VALUES (
    'admin',
    '$2y$10$...' -- Use password_hash('your_password', PASSWORD_BCRYPT)
);
```

### Step 10: First Deployment

1. Make your final commit
2. Push to GitHub
3. Railway auto-deploys
4. Visit your Railway domain
5. Test registration and admin login

---

## 🧪 POST-DEPLOYMENT VERIFICATION

### Security Tests

```bash
# Test 1: SQL Injection (should fail gracefully)
curl "https://yourdomain.railway.app/register.php?event_id=1 OR 1=1"
# Expected: Normal page, no SQL errors

# Test 2: XSS Prevention (should encode)
curl "https://yourdomain.railway.app/contact.php" \
  -X POST \
  -d 'name=<script>alert(1)</script>&email=test@test.com&...'
# Expected: Script tag rendered as text, not executed

# Test 3: CSRF Protection
curl "https://yourdomain.railway.app/admin_login.php" \
  -X POST \
  -d 'username=admin&password=test'
# Expected: 403 Forbidden (CSRF token missing)

# Test 4: Brute Force Protection (5 login attempts)
for i in {1..6}; do
  curl "https://yourdomain.railway.app/admin_login.php" \
    -X POST \
    -d 'username=admin&password=wrong&csrf_token=test'
done
# Expected: After 5 attempts, "Too many login attempts" message

# Test 5: File Upload Exploit (.php upload)
# Should be blocked by MIME type validation
```

### Functionality Tests

- [ ] Home page loads correctly
- [ ] Events list displays
- [ ] Registration form works
- [ ] Contact form submits
- [ ] Admin login works with correct credentials
- [ ] Admin dashboard loads
- [ ] Add event form works
- [ ] File uploads work (images, PDFs)
- [ ] Logout works and clears session
- [ ] Session timeout works (after 30 minutes)

---

## 🔍 MONITORING & MAINTENANCE

### Daily Checks
- Check error logs for PHP errors
- Monitor database connections
- Check disk space usage

### Weekly Checks
- Review failed login attempts in logs
- Monitor application performance
- Check for any security warnings

### Monthly Checks
- Review and rotate database credentials
- Update PHP version if security patches available
- Test disaster recovery procedures
- Review access logs

### Monitoring Commands

```bash
# Connect to Railway
railway connect

# View logs
tail -f /var/log/railway/php-error.log

# Check database connection
php -r "require 'config.php'; $conn = getDBConnection(); echo 'Connected: ' . $conn->server_info;"

# Check file permissions
ls -la uploads/
```

---

## 🔐 PRODUCTION SECURITY HARDENING

### Additional Measures (Beyond Current Implementation)

1. **Web Application Firewall (WAF)**
   - Enable Railway's DDoS protection
   - Consider Cloudflare for additional WAF

2. **Database Encryption**
   - Enable SSL for database connections
   - Encrypt sensitive fields at application level

3. **Backup Strategy**
   - Set up automated daily backups
   - Test restore procedure weekly
   - Store backups in separate location

4. **Access Control**
   - Use strong, unique passwords (20+ chars)
   - Implement 2FA for admin accounts (future enhancement)
   - Restrict admin panel to known IPs (if possible)

5. **API Rate Limiting**
   - Implement rate limiting on contact form
   - Rate limit registration endpoints

6. **Monitoring & Alerting**
   - Set up email alerts for errors
   - Monitor for suspicious login patterns
   - Alert on file upload failures

---

## 🚨 INCIDENT RESPONSE

### If Compromised:

1. **Immediate Actions**
   - Take application offline
   - Change all database credentials
   - Review access logs
   - Check file modification times

2. **Investigation**
   - Review application logs
   - Check server logs
   - Verify no backdoors were left
   - Document findings

3. **Recovery**
   - Restore from clean backup
   - Apply security patches
   - Re-enable application
   - Notify users if data was compromised

---

## 📞 TROUBLESHOOTING

### Common Issues

**Problem: "Connection failed: Unknown database 'innovatex_events'"**
- Solution: Run migration script to create database
  ```bash
  railway run php migrate.php
  ```

**Problem: "File upload fails - Permission denied"**
- Solution: Check uploads/ directory permissions
  ```bash
  railway run chmod -R 755 uploads/
  ```

**Problem: "Session not working / User logged out immediately"**
- Solution: Check SESSION_TIMEOUT value (should be 1800)
- Check database connection is persistent
- Verify Redis isn't interfering with sessions

**Problem: "MIME type validation failing"**
- Solution: Ensure finfo_file is available
  ```bash
  railway run php -i | grep finfo
  ```

**Problem: "HTTPS not enforcing"**
- Solution: Verify headers are being sent
  ```bash
  curl -I https://yourdomain.railway.app
  # Should show security headers
  ```

---

## 📖 USEFUL RAILWAY COMMANDS

```bash
# Login
railway login

# Link to project
railway link

# View project status
railway status

# Deploy specific branch
railway deploy --branch main

# View environment variables
railway env

# Connect to database
railway database

# SSH into container
railway shell

# View deployment logs
railway logs

# Trigger redeploy
railway redeploy
```

---

## ✅ FINAL CHECKLIST

Before going live:

- [ ] All security audits passed
- [ ] Environment variables configured
- [ ] Database migrations completed
- [ ] Admin account created and tested
- [ ] SSL/HTTPS configured
- [ ] Email configuration (if using)
- [ ] Error logging verified
- [ ] Backups configured
- [ ] Monitoring alerts set up
- [ ] Team notified of deployment
- [ ] Rollback procedure documented

---

## 📞 SUPPORT

For Railway-specific issues:
- Railway Docs: https://docs.railway.app
- Railway Support: https://support.railway.app
- GitHub Issues: Track deployment issues

---

**Last Updated: February 17, 2026**
**Status: ✅ PRODUCTION READY**
