# DineBook — Installation Manual

## 1. System Requirements

| Requirement | Minimum Version |
|-------------|----------------|
| PHP | 8.0 or higher |
| MongoDB Community Server | 6.0 or higher |
| Composer | 2.x |
| PHP MongoDB Extension | ext-mongodb 1.15+ |
| Web Server | Apache, Nginx, or PHP built-in server |
| Operating System | macOS, Linux, or Windows |
| Web Browser | Chrome, Firefox, Safari, Edge (modern) |

---

## 2. Install MongoDB

### macOS (Homebrew)
```bash
brew tap mongodb/brew
brew install mongodb-community
brew services start mongodb-community
```

### Windows
Download from https://www.mongodb.com/try/download/community and install. Start the MongoDB service.

### Linux (Ubuntu)
```bash
sudo apt-get install -y mongodb-org
sudo systemctl start mongod
sudo systemctl enable mongod
```

### Verify MongoDB is running
```bash
mongosh --eval "db.runCommand({ping: 1})"
```

---

## 3. Install PHP MongoDB Extension

```bash
pecl install mongodb
```

Add to php.ini if not auto-configured:
```
extension=mongodb.so
```

Verify:
```bash
php -m | grep mongodb
```

---

## 4. Install Composer Dependencies

Navigate to the dinebook folder and run:
```bash
cd /path/to/dinebook
composer require mongodb/mongodb
```

This creates the `vendor/` folder with the MongoDB PHP library and autoloader.

---

## 5. Configure Database Connection

Edit `config.php` if your MongoDB runs on a different host/port:
```php
$client = new MongoDB\Client("mongodb://localhost:27017");
```

The database name is `dinebook`. No schema creation needed — MongoDB creates collections automatically on first insert.

---

## 6. Create Admin User

Run the seed script once:
```bash
php auth/seed_user.php
```

Output: `Admin user created successfully. Username: admin / Password: admin123`

**Important**: Change the password in production by modifying `seed_user.php` before running.

---

## 7. Start the Web Server

### Option A: PHP Built-in Server
```bash
cd /path/to/dinebook
php -S localhost:8000
```
Then open: http://localhost:8000/

### Option B: Apache (XAMPP/MAMP)
Copy the `dinebook/` folder to your web server's document root (e.g., `htdocs/`).
Open: http://localhost/dinebook/

### Option C: Nginx
Configure a server block pointing to the `dinebook/` folder.

---

## 8. Verify Installation

1. Open the URL in your browser
2. The login screen should appear
3. Enter: **admin** / **admin123**
4. The dashboard should load with quick stats
5. Try creating a reservation to verify MongoDB connectivity

---

## 9. Troubleshooting

### "vendor/autoload.php not found"
Run `composer require mongodb/mongodb` in the dinebook folder.

### "Connection refused on localhost:27017"
MongoDB is not running. Start it:
- macOS: `brew services start mongodb-community`
- Linux: `sudo systemctl start mongod`
- Windows: Start the MongoDB service from Services panel

### "ext-mongodb missing"
Install the PHP extension: `pecl install mongodb`
Then restart your web server.

### "Class MongoDB\Client not found"
The Composer autoloader is not loading. Verify `vendor/autoload.php` exists and `config.php` has the correct require path.

### Session issues / redirect loops
Ensure PHP sessions are enabled and the session save path is writable.
Check that `session_start()` is called (handled by `guard.php`).

### "Permission denied" errors
Ensure the web server user has read access to all files in the dinebook folder.
