#!/bin/bash

# Quick Fix Script for Laravel Queue Worker on Ubuntu Server
# This script will set up and start the queue worker for HLS conversion

echo "🚀 Starting Laravel Queue Worker Setup..."
echo "========================================"

# Check if running as root
if [ "$EUID" -eq 0 ]; then
    echo "❌ Please don't run this script as root. Run as your regular user with sudo."
    exit 1
fi

# Get the current directory
PROJECT_DIR="/var/www/veenixnew"
echo "📁 Project directory: $PROJECT_DIR"

# Check if project directory exists
if [ ! -d "$PROJECT_DIR" ]; then
    echo "❌ Project directory not found: $PROJECT_DIR"
    echo "Please update the PROJECT_DIR variable in this script to match your actual project path."
    exit 1
fi

echo "✅ Project directory found"

# 1. Install Supervisor if not installed
echo ""
echo "📦 Installing Supervisor..."
if ! command -v supervisorctl &> /dev/null; then
    sudo apt update
    sudo apt install supervisor -y
    echo "✅ Supervisor installed"
else
    echo "✅ Supervisor already installed"
fi

# 2. Create Supervisor configuration
echo ""
echo "⚙️ Creating Supervisor configuration..."
sudo tee /etc/supervisor/conf.d/laravel-worker.conf > /dev/null << EOF
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $PROJECT_DIR/artisan queue:work --sleep=3 --tries=2 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=$PROJECT_DIR/storage/logs/worker.log
stopwaitsecs=3600
EOF

echo "✅ Supervisor configuration created"

# 3. Set proper permissions
echo ""
echo "🔐 Setting proper file permissions..."
sudo chown -R www-data:www-data $PROJECT_DIR/storage
sudo chmod -R 775 $PROJECT_DIR/storage
sudo chmod -R 775 $PROJECT_DIR/bootstrap/cache
echo "✅ Permissions set"

# 4. Check FFmpeg
echo ""
echo "🎬 Checking FFmpeg installation..."
if ! command -v ffmpeg &> /dev/null; then
    echo "❌ FFmpeg not found. Installing..."
    sudo apt install ffmpeg -y
    echo "✅ FFmpeg installed"
else
    echo "✅ FFmpeg already installed"
fi

# Test FFmpeg as www-data user
sudo -u www-data ffmpeg -version > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo "✅ FFmpeg accessible by www-data user"
else
    echo "⚠️ FFmpeg may not be accessible by www-data user"
fi

# 5. Start the queue worker
echo ""
echo "🔄 Starting Laravel queue worker..."
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start lar-worker:*

# Wait a moment for startup
sleep 3

# 6. Check status
echo ""
echo "📊 Checking queue worker status..."
sudo supervisorctl status

# 7. Test queue worker
echo ""
echo "🧪 Testing queue worker..."
cd $PROJECT_DIR
php artisan queue:monitor

# 8. Show log locations
echo ""
echo "📋 Log file locations:"
echo "   Laravel logs: $PROJECT_DIR/storage/logs/laravel.log"
echo "   Worker logs:  $PROJECT_DIR/storage/logs/worker.log"
echo ""
echo "🔍 To monitor logs in real-time:"
echo "   tail -f $PROJECT_DIR/storage/logs/laravel.log | grep '🎬\|🔧\|✅\|❌'"
echo "   tail -f $PROJECT_DIR/storage/logs/worker.log"

# 9. Next steps
echo ""
echo "🎯 Next steps:"
echo "   1. Test HLS conversion from your admin panel"
echo "   2. Monitor the logs for conversion progress"
echo "   3. Check movie status in admin panel"
echo ""
echo "🔧 Useful commands:"
echo "   Check queue status: cd $PROJECT_DIR && php artisan queue:monitor"
echo "   Check failed jobs: cd $PROJECT_DIR && php artisan queue:failed"
echo "   Restart workers:   sudo supervisorctl restart lar-worker:*"
echo "   Clear queue:       cd $PROJECT_DIR && php artisan queue:clear"

echo ""
echo "✅ Setup complete! Your queue worker should now be running."
echo "   Try the HLS conversion now and monitor the logs."
