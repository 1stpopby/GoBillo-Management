#!/bin/bash

echo "🔧 Quick MySQL Fix for GoBillo"
echo "=============================="

cd /home/forge/gobillo.app || exit 1

echo "🗄️ Creating MySQL database..."
mysql -u forge -p -e "CREATE DATABASE IF NOT EXISTS gobillo;"
mysql -u forge -p -e "GRANT ALL PRIVILEGES ON gobillo.* TO 'forge'@'localhost';"
mysql -u forge -p -e "FLUSH PRIVILEGES;"

echo "🧹 Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear

echo "🗄️ Running migrations and seeders..."
php artisan migrate:fresh --seed --force

echo "⚡ Final optimizations..."
php artisan config:cache

echo "✅ Done! Try https://gobillo.app"
echo "🔑 Login: admin@gobillo.app / password123"
