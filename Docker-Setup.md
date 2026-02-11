# PHP MySQL Docker CI/CD Project

A simple PHP and MySQL application with Docker containerization and GitHub Actions CI/CD pipeline.

## 🚀 Features

- **PHP 8.3** with Apache
- **MySQL Latest** with sample database
- **phpMyAdmin** for database management
- **Docker Compose** for easy setup
- **GitHub Actions** CI/CD pipeline
- Sample application with database integration

## 📋 Prerequisites

- Docker Desktop installed
- Docker Compose installed
- Git installed
- GitHub account (for CI/CD)

## 🛠️ Quick Start

### 1. Clone the repository

```bash
git clone <your-repo-url>
cd <your-repo-name>
```

### 2. Start the Docker containers

```bash
docker-compose up -d
```

### 3. Access the application

- **Main Application**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
  - Username: `myapp_user`
  - Password: `myapp_password`

### 4. Stop the containers

```bash
docker-compose down
```

## 📁 Project Structure

```
.
├── .github/
│   └── workflows/
│       └── ci-cd.yml          # GitHub Actions workflow
├── src/
│   └── index.php              # Main PHP application
├── docker-compose.yml          # Docker services configuration
├── Dockerfile                  # PHP container configuration
├── init.sql                    # Database initialization script
└── README.md                   # This file
```

## 🔧 Configuration

### Database Configuration

Edit the environment variables in `docker-compose.yml`:

```yaml
environment:
  MYSQL_ROOT_PASSWORD: root_password
  MYSQL_DATABASE: myapp_db
  MYSQL_USER: myapp_user
  MYSQL_PASSWORD: myapp_password
```

Update the connection details in `src/index.php` accordingly.

### Port Configuration

Default ports:
- PHP Application: `8080`
- MySQL: `3306`
- phpMyAdmin: `8081`

To change ports, modify the `ports` section in `docker-compose.yml`.

## 🔄 CI/CD Pipeline

The GitHub Actions workflow automatically:

1. **Tests**:
   - Validates PHP syntax
   - Runs PHPUnit tests (if configured)
   - Checks MySQL connection

2. **Builds**:
   - Builds Docker image
   - Tests Docker Compose setup
   - Optionally pushes to Docker Hub

3. **Deploys**:
   - Ready for deployment configuration
   - Runs only on main branch

### Setting up GitHub Actions

1. Push your code to GitHub
2. The workflow will automatically run on push/PR
3. (Optional) Add Docker Hub credentials as secrets:
   - `DOCKER_USERNAME`
   - `DOCKER_PASSWORD`

## 🐳 Docker Commands

### Rebuild containers

```bash
docker-compose up -d --build
```

### View logs

```bash
docker-compose logs -f
```

### Access PHP container

```bash
docker exec -it php_app bash
```

### Access MySQL container

```bash
docker exec -it mysql_db mysql -u myapp_user -p
```

### Remove all containers and volumes

```bash
docker-compose down -v
```

## 🧪 Adding Tests

Create `phpunit.xml` and add tests in a `tests/` directory:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit bootstrap="vendor/autoload.php">
    <testsuites>
        <testsuite name="Application Test Suite">
            <directory>./tests</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

Install PHPUnit:

```bash
composer require --dev phpunit/phpunit
```

## 🚀 Deployment Options (Free)

1. **Oracle Cloud Free Tier**
   - Free VPS with Docker support
   - SSH deployment via GitHub Actions

2. **Railway.app**
   - Free tier available
   - Direct GitHub integration

3. **Render.com**
   - Free tier for web services
   - Docker support

4. **Fly.io**
   - Free tier available
   - Docker-based deployment

## 📝 Development Workflow

1. Make changes to `src/` files
2. Changes are automatically reflected (volume mount)
3. Commit and push to GitHub
4. CI/CD pipeline runs automatically
5. Deploy to production (configure deployment step)

## 🔒 Environment Variables

For production, use environment variables instead of hardcoded credentials:

```yaml
environment:
  DB_HOST: ${DB_HOST}
  DB_NAME: ${DB_NAME}
  DB_USER: ${DB_USER}
  DB_PASS: ${DB_PASS}
```

Create a `.env` file (add to `.gitignore`):

```
DB_HOST=mysql
DB_NAME=myapp_db
DB_USER=myapp_user
DB_PASS=myapp_password
```

## 🐛 Troubleshooting

### Port already in use

```bash
# Find process using port 8080
lsof -i :8080
# Kill the process or change port in docker-compose.yml
```

### MySQL connection refused

```bash
# Wait for MySQL to fully start
docker-compose logs mysql
# Check if MySQL is healthy
docker-compose ps
```

### Permission issues

```bash
# Fix file permissions
sudo chown -R $USER:$USER src/
chmod -R 755 src/
```

## 📚 Resources

- [Docker Documentation](https://docs.docker.com/)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [PHP Documentation](https://www.php.net/docs.php)
- [MySQL Documentation](https://dev.mysql.com/doc/)

## 📄 License

This project is open source and available under the MIT License.