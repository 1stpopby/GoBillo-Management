#!/bin/bash

echo "🚀 GoBillo Laravel - MySQL Deployment Setup"
echo "============================================"

# Navigate to application directory
cd /home/forge/gobillo.app || exit 1

echo "📁 Current directory: $(pwd)"

# Step 1: Set proper storage permissions
echo "📂 Setting storage permissions..."
chmod -R 775 storage bootstrap/cache
chown -R forge:www-data storage bootstrap/cache

# Step 2: Create required storage directories
echo "📁 Creating storage directories..."
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions  
mkdir -p storage/framework/views
mkdir -p storage/logs
chmod -R 775 storage/framework
chown -R forge:www-data storage/framework

# Step 3: Clear all caches
echo "🧹 Clearing caches..."
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Step 4: Generate application key if needed
echo "🔑 Generating application key..."
php artisan key:generate --force

# Step 5: Run all migrations fresh and seed
echo "🗄️ Running MySQL migrations and seeding database..."
php artisan migrate:fresh --seed --force

# Step 6: Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Step 7: Final cache optimization
echo "⚡ Final optimizations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 8: Set final permissions
echo "🔐 Setting final permissions..."
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
chown -R forge:www-data storage bootstrap/cache

echo ""
echo "✅ MySQL Deployment Complete!"
echo "================================"
echo "🌐 Site URL: https://gobillo.app"
echo "🔑 SuperAdmin Login:"
echo "   Email: admin@gobillo.app"
echo "   Password: password123"
echo ""
echo "📋 Test these URLs:"
echo "   Landing Page: https://gobillo.app"
echo "   Login Page: https://gobillo.app/login"
echo "   Dashboard: https://gobillo.app/dashboard"
echo ""
