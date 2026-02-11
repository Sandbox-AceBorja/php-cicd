# 🚀 InfinityFree CI/CD Setup Guide

Complete guide to connect your GitHub project to InfinityFree hosting with automated deployments.

## 📋 Prerequisites

- ✅ InfinityFree account: `if0_41129394`
- ✅ Domain: `php-cicd.free.nf`
- ✅ GitHub repository
- ✅ This project files

## 🔑 Step 1: Get Your InfinityFree FTP Credentials

1. **Log in to InfinityFree Dashboard**: https://dash.infinityfree.com/accounts/if0_41129394

2. **Find FTP Details** (in your account panel):
   - Click on your account `if0_41129394`
   - Look for "FTP Details" or "Account Settings"
   - You'll need:
     - **FTP Hostname**: Usually `ftpupload.net` or similar
     - **FTP Username**: Usually `if0_41129394`
     - **FTP Password**: Your FTP password (if forgotten, reset it)

3. **Find MySQL Details**:
   - Go to "MySQL Databases" section
   - Note down:
     - **MySQL Hostname**: Usually `sql###.infinityfree.com`
     - **MySQL Database Name**: Usually `if0_41129394_xxxxx`
     - **MySQL Username**: Usually `if0_41129394`
     - **MySQL Password**: Your database password

## 🔐 Step 2: Add Secrets to GitHub Repository

1. **Go to your GitHub repository**

2. **Navigate to**: Settings → Secrets and variables → Actions

3. **Click "New repository secret"** and add these three secrets:

   **Secret 1:**
   - Name: `FTP_SERVER`
   - Value: Your FTP hostname (e.g., `ftpupload.net`)

   **Secret 2:**
   - Name: `FTP_USERNAME`
   - Value: Your FTP username (e.g., `if0_41129394`)

   **Secret 3:**
   - Name: `FTP_PASSWORD`
   - Value: Your FTP password

## 📝 Step 3: Update Database Configuration

1. **Edit `src/index.php`** and update these lines with your MySQL details:

```php
$host = 'sql###.infinityfree.com'; // Your MySQL hostname from InfinityFree
$db = 'if0_41129394_myapp';        // Your database name
$user = 'if0_41129394';            // Your database username
$pass = 'YOUR_DB_PASSWORD';        // Your database password
```

2. **Replace the Docker version** with the InfinityFree version:
   - Rename `src/index-infinityfree.php` to `src/index.php`
   - OR copy the database config from `index-infinityfree.php`

## 🗄️ Step 4: Setup Database

1. **Access phpMyAdmin** from your InfinityFree dashboard

2. **Create the database** (if not exists):
   - Database name: `if0_41129394_myapp` (or similar)

3. **Run the initialization script**:
   - Click on your database
   - Click "SQL" tab
   - Copy and paste the contents of `init.sql`
   - Click "Go" to execute

The `init.sql` contains:
```sql
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO users (name, email) VALUES
    ('John Doe', 'john@example.com'),
    ('Jane Smith', 'jane@example.com'),
    ('Bob Johnson', 'bob@example.com'),
    ('Alice Williams', 'alice@example.com');
```

## 📁 Step 5: Setup GitHub Repository

1. **Create a new repository** on GitHub (or use existing)

2. **Add the GitHub Actions workflow**:
   - Create folder: `.github/workflows/`
   - Add file: `deploy-infinityfree.yml`

3. **Commit and push your code**:

```bash
git init
git add .
git commit -m "Initial commit with CI/CD"
git branch -M main
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO.git
git push -u origin main
```

## 🎯 Step 6: Test the Deployment

1. **Push any change** to the main branch:
```bash
echo "# Test" >> README.md
git add README.md
git commit -m "Test deployment"
git push
```

2. **Check GitHub Actions**:
   - Go to your repository
   - Click "Actions" tab
   - Watch the workflow run

3. **Verify deployment**:
   - Visit: https://php-cicd.free.nf
   - You should see your PHP application running!

## 🔄 How It Works

Every time you push to the `main` branch:

1. ✅ GitHub Actions runs PHP syntax validation
2. ✅ Tests your code
3. ✅ Deploys files to InfinityFree via FTP
4. ✅ Your site updates automatically!

## 📂 InfinityFree File Structure

Your files will be deployed to:
```
/htdocs/              ← Your web root
  ├── index.php       ← Main application
  └── (other files)
```

**Note**: InfinityFree uses `/htdocs/` as the web root, not `/public_html/`

## 🐛 Troubleshooting

### FTP Connection Failed
- ✓ Check FTP credentials are correct
- ✓ Verify secrets are added to GitHub
- ✓ FTP hostname should not include `ftp://`
- ✓ Try using IP address instead of hostname

### Database Connection Failed
- ✓ Verify MySQL hostname is correct
- ✓ Check database name exists
- ✓ Ensure MySQL user has permissions
- ✓ Test connection in phpMyAdmin first

### 403 Forbidden Error
- ✓ Make sure files are in `/htdocs/` directory
- ✓ Check file permissions (should be 644 for files, 755 for folders)
- ✓ Rename `index-infinityfree.php` to `index.php`

### GitHub Actions Failing
- ✓ Check the Actions tab for error details
- ✓ Verify all secrets are set correctly
- ✓ Check PHP syntax locally first: `php -l src/index.php`

## 🎨 Next Steps

Once deployed, you can:

1. **Add more pages**: Create new PHP files in `src/`
2. **Add forms**: Create POST handlers for user input
3. **Add authentication**: Implement login/register system
4. **Add API endpoints**: Create REST API routes
5. **Add .htaccess**: For URL rewriting and security

## 📊 Free Tier Limits (InfinityFree)

- ✓ Unlimited bandwidth
- ✓ Unlimited disk space
- ✓ 400 MySQL databases
- ✓ PHP 8.x support
- ✓ MySQL support
- ✗ No SSH access
- ✗ Hit limits (apply after heavy traffic)

## 🔗 Useful Links

- InfinityFree Dashboard: https://dash.infinityfree.com
- Your Site: https://php-cicd.free.nf
- InfinityFree Support: https://forum.infinityfree.com

## 🎉 Success Checklist

- [ ] GitHub secrets configured (FTP_SERVER, FTP_USERNAME, FTP_PASSWORD)
- [ ] Database credentials updated in `src/index.php`
- [ ] Database created and initialized with `init.sql`
- [ ] Code pushed to GitHub `main` branch
- [ ] GitHub Actions workflow completed successfully
- [ ] Site accessible at https://php-cicd.free.nf
- [ ] Database connection working
- [ ] Sample users displaying on homepage

---

**Need help?** Check the GitHub Actions logs for detailed error messages!