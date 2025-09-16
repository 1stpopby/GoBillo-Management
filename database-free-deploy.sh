#!/bin/bash

echo "🚀 Database-Free GoBillo Deployment"
echo "==================================="

cd /home/forge/gobillo.app || exit 1

echo "📂 Setting storage permissions..."
chmod -R 775 storage bootstrap/cache
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs

echo "🔧 Configuring file-based cache and sessions..."
# Create temporary config that doesn't use database
cat > .env.temp << 'EOF'
APP_NAME=GoBillo
APP_ENV=production
APP_KEY=base64:TFvKMLqAa6J/41r9jp8q3nTmD3qTHJquj/J0zu3qfJg=
APP_DEBUG=false
APP_URL="https://gobillo.app"

# File-based everything (no database required)
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false

CACHE_STORE=file
QUEUE_CONNECTION=sync

BROADCAST_CONNECTION=log

MAIL_MAILER=log
MAIL_FROM_ADDRESS="hello@gobillo.app"
MAIL_FROM_NAME="${APP_NAME}"

LOG_CHANNEL=single
LOG_LEVEL=error
EOF

# Backup original .env and use temp
if [ -f .env ]; then
    cp .env .env.backup
fi
cp .env.temp .env

echo "🧹 Clearing file-based caches..."
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
rm -rf bootstrap/cache/*.php

echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🔐 Setting final permissions..."
chmod -R 755 .
chmod -R 775 storage bootstrap/cache

echo ""
echo "✅ Database-Free Deployment Complete!"
echo "===================================="
echo "🌐 Site URL: https://gobillo.app"
echo ""
echo "📋 Test these URLs:"
echo "   Landing Page: https://gobillo.app"
echo "   Get Started: https://gobillo.app/get-started"
echo ""
echo "Note: Login will not work until MySQL is set up"
echo ""
