#!/bin/bash

echo "🚀 GoBillo Laravel - Complete Forge Deployment Fix"
echo "================================================="

# Navigate to application directory
cd /home/forge/gobillo.app || exit 1

echo "📁 Current directory: $(pwd)"

# Step 1: Create and set permissions for database
echo "🗄️ Setting up SQLite database..."
mkdir -p database
touch database/database.sqlite
chmod 664 database/database.sqlite
chown forge:www-data database/database.sqlite

# Step 2: Set proper storage permissions
echo "📂 Setting storage permissions..."
chmod -R 775 storage bootstrap/cache
chown -R forge:www-data storage bootstrap/cache

# Step 3: Create required storage directories
echo "📁 Creating storage directories..."
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions  
mkdir -p storage/framework/views
mkdir -p storage/logs
chmod -R 775 storage/framework
chown -R forge:www-data storage/framework

# Step 4: Clear all caches before migration
echo "🧹 Clearing caches..."
php artisan optimize:clear
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Step 5: Generate application key if needed
echo "🔑 Generating application key..."
php artisan key:generate --force

# Step 6: Run all migrations fresh
echo "🗄️ Running migrations..."
php artisan migrate:fresh --force

# Step 7: Create SuperAdmin user
echo "👤 Creating SuperAdmin user..."
php artisan tinker --execute="
try {
    \App\Models\User::create([
        'name' => 'Super Admin',
        'email' => 'admin@gobillo.app',
        'password' => bcrypt('password123'),
        'role' => 'superadmin',
        'email_verified_at' => now()
    ]);
    echo 'SuperAdmin user created successfully!' . PHP_EOL;
} catch (\Exception \$e) {
    echo 'SuperAdmin user already exists or error: ' . \$e->getMessage() . PHP_EOL;
}
"

# Step 8: Create default site content
echo "📝 Creating default site content..."
php artisan tinker --execute="
try {
    \App\Models\SiteContent::firstOrCreate(
        ['key' => 'landing_hero_title'],
        [
            'page' => 'landing',
            'section' => 'hero',
            'type' => 'text',
            'label' => 'Hero Title',
            'value' => 'Professional Construction Management Made Simple',
            'default_value' => 'Professional Construction Management Made Simple',
            'description' => 'Main hero title on landing page',
            'sort_order' => 1,
            'is_active' => true
        ]
    );

    \App\Models\SiteContent::firstOrCreate(
        ['key' => 'landing_hero_subtitle'],
        [
            'page' => 'landing',
            'section' => 'hero',
            'type' => 'textarea',
            'label' => 'Hero Subtitle',
            'value' => 'Streamline your construction projects with our comprehensive management platform. From project planning to team collaboration, we have got you covered.',
            'default_value' => 'Streamline your construction projects with our comprehensive management platform. From project planning to team collaboration, we have got you covered.',
            'description' => 'Hero subtitle/description on landing page',
            'sort_order' => 2,
            'is_active' => true
        ]
    );

    \App\Models\SiteContent::firstOrCreate(
        ['key' => 'landing_hero_cta_text'],
        [
            'page' => 'landing',
            'section' => 'hero',
            'type' => 'text',
            'label' => 'Hero CTA Button Text',
            'value' => 'Start Free Trial',
            'default_value' => 'Start Free Trial',
            'description' => 'Text for the main call-to-action button',
            'sort_order' => 3,
            'is_active' => true
        ]
    );

    echo 'Default site content created successfully!' . PHP_EOL;
} catch (\Exception \$e) {
    echo 'Error creating site content: ' . \$e->getMessage() . PHP_EOL;
}
"

# Step 9: Create storage link
echo "🔗 Creating storage link..."
php artisan storage:link

# Step 10: Final cache optimization
echo "⚡ Final optimizations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Step 11: Set final permissions
echo "🔐 Setting final permissions..."
chmod -R 755 .
chmod -R 775 storage bootstrap/cache database
chown -R forge:www-data storage bootstrap/cache database

echo ""
echo "✅ Deployment Fix Complete!"
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
