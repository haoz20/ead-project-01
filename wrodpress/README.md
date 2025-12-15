

# WordPress Docker Setup

This project sets up a WordPress site with MariaDB database using Docker Compose.

## Prerequisites

- Docker
- Docker Compose

## Configuration

### 1. Create Environment Configuration File

Before deploying to production, create a `.env` file in the same directory as `docker-compose.yml`:

```bash
touch .env
```

Add the following configuration to `.env`:

```env
# Database Configuration
MYSQL_ROOT_PASSWORD=your_secure_root_password_here
MYSQL_DATABASE=wordpress
MYSQL_USER=wordpress
MYSQL_PASSWORD=your_secure_wordpress_db_password_here

# WordPress Configuration
WORDPRESS_DB_HOST=db
WORDPRESS_DB_USER=wordpress
WORDPRESS_DB_PASSWORD=your_secure_wordpress_db_password_here
WORDPRESS_DB_NAME=wordpress
```

### 2. Update docker-compose.yml for Production

Modify the `docker-compose.yml` to use environment variables instead of hardcoded passwords:

```yaml
services:
  db:
    image: mariadb:10.6.4-focal
    command: '--default-authentication-plugin=mysql_native_password'
    volumes:
      - db_data:/var/lib/mysql
    restart: always
    environment:
      - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
      - MYSQL_DATABASE=${MYSQL_DATABASE}
      - MYSQL_USER=${MYSQL_USER}
      - MYSQL_PASSWORD=${MYSQL_PASSWORD}
    expose:
      - 3306
      - 33060
  wordpress:
    image: wordpress:latest
    volumes:
      - wp_data:/var/www/html
    ports:
      - 80:80
    restart: always
    environment:
      - WORDPRESS_DB_HOST=${WORDPRESS_DB_HOST}
      - WORDPRESS_DB_USER=${WORDPRESS_DB_USER}
      - WORDPRESS_DB_PASSWORD=${WORDPRESS_DB_PASSWORD}
      - WORDPRESS_DB_NAME=${WORDPRESS_DB_NAME}
volumes:
  db_data:
  wp_data:
```

## Changing Passwords for Production

**⚠️ IMPORTANT: Never use default passwords in production!**

### Security Best Practices:

1. **Generate Strong Passwords**: Use a password generator to create strong, random passwords (at least 16 characters with mixed case, numbers, and symbols)

2. **Update the .env file**:
   ```bash
   nano .env
   ```
   Replace all placeholder passwords with strong, unique passwords.

3. **Secure the .env file**:
   ```bash
   chmod 600 .env
   ```
   This ensures only the owner can read/write the file.

4. **Add .env to .gitignore**:
   ```bash
   echo ".env" >> .gitignore
   ```
   Never commit the `.env` file to version control!

## Deployment

### Development Environment
```bash
docker compose up -d
```

### Production Environment

1. Create and configure your `.env` file (see Configuration section above)
2. Ensure passwords are changed from defaults
3. Start the containers:
   ```bash
   docker compose up -d
   ```

## Accessing WordPress

Once the containers are running:
- WordPress site: http://localhost
- Complete the WordPress installation wizard using a secure admin password

## Managing the Application

### View logs
```bash
docker compose logs -f
```

### Stop containers
```bash
docker compose down
```

### Stop and remove volumes (⚠️ This will delete all data!)
```bash
docker compose down -v
```

### Restart containers
```bash
docker compose restart
```

## Backup

To backup your WordPress data:
```bash
# Backup database
docker compose exec db mysqldump -u wordpress -p wordpress > backup.sql

# Backup WordPress files
docker compose exec wordpress tar -czf /tmp/wp-backup.tar.gz /var/www/html
docker compose cp wordpress:/tmp/wp-backup.tar.gz ./wp-backup.tar.gz
```

## Troubleshooting

- If you encounter port conflicts, modify the port mapping in `docker-compose.yml` (e.g., change `80:80` to `8080:80`)
- Check container status: `docker compose ps`
- View container logs: `docker compose logs wordpress` or `docker compose logs db`