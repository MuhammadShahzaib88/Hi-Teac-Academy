#!/bin/bash
# Hi Teac Academy - Database Setup Script
# This script requires sudo access to configure MySQL

echo "=== Hi Teac Academy - Database Setup ==="
echo ""

# Step 1: Configure MySQL root user for password authentication
echo "[1/3] Configuring MySQL root user for password auth..."
sudo mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY ''; FLUSH PRIVILEGES;"
if [ $? -ne 0 ]; then
    echo "ERROR: Could not configure MySQL root user. Make sure MySQL is running."
    exit 1
fi
echo "  ✓ MySQL root configured for password auth"

# Step 2: Create the database
echo "[2/3] Creating database 'hi_teac_academy'..."
mysql -u root -e "CREATE DATABASE IF NOT EXISTS hi_teac_academy CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
if [ $? -ne 0 ]; then
    echo "ERROR: Could not create database."
    exit 1
fi
echo "  ✓ Database created"

# Step 3: Import schema and seed data
echo "[3/3] Importing schema and seed data..."
mysql -u root hi_teac_academy < database/database.sql
if [ $? -ne 0 ]; then
    echo "ERROR: Could not import database schema."
    exit 1
fi
echo "  ✓ Schema and seed data imported"

echo ""
echo "=== Database setup complete! ==="
echo "You can now run the project with: php -S localhost:8000"
