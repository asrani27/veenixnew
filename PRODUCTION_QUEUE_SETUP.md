# Production Queue Setup for HLS Conversion

## Issue Identified
The HLS conversion job is being dispatched successfully but not executing because the Laravel queue worker is not running on the Ubuntu production server.

## Immediate Fix Required

### 1. Install Supervisor (Queue Worker Manager)
```bash
# SSH into your Ubuntu server
ssh your-user@your-server

# Update package list
sudo apt update

# Install supervisor
sudo apt install supervisor -y

# Verify installation
sudo systemctl status supervisor
```

### 2. Create Supervisor Configuration
Create a supervisor configuration file for Laravel queue workers:

```bash
# Create the configuration file
sudo nano /etc/supervisor/conf.d/laravel-worker.conf
```

Add the following content (adjust paths as needed):

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/veenixnew/artisan queue:work --sleep=3 --tries=2 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/veenixnew/storage/logs/worker.log
stopwaitsecs=3600
```

### 3. Activate the Queue Worker
```bash
# Tell Supervisor to read the new configuration
sudo supervisorctl reread

# Update Supervisor with the new configuration
sudo supervisorctl update

# Start the Laravel worker processes
sudo supervisorctl start lar-worker:*

# Check the status
sudo supervisorctl status
```

### 4. Verify Queue Worker is Running
```bash
# Check if workers are running
sudo supervisorctl status

# Monitor the worker log
tail -f /var/www/veenixnew/storage/logs/worker.log

# Check Laravel queue status
cd /var/www/veenixnew
php artisan queue:monitor
```

## Alternative: Manual Queue Worker (Temporary)

If you need to test immediately without Supervisor:

```bash
# Navigate to project directory
cd /var/www/veenixnew

# Start queue worker manually (for testing)
php artisan queue:work --timeout=3600 --tries=2 --sleep=3

# Or run in background
nohup php artisan queue:work --timeout=3600 --tries=2 --sleep=3 > storage/logs/queue.log 2>&1 &

# Check if it's running
ps aux | grep "queue:work"
```

## Additional Production Setup

### 5. Verify FFmpeg Installation
```bash
# Check if FFmpeg is installed
which ffmpeg
ffmpeg -version

# Install if not present
sudo apt install ffmpeg -y

# Test FFmpeg execution as web user
sudo -u www-data ffmpeg -version
```

### 6. Set Proper File Permissions
```bash
# Set ownership for web files
sudo chown -R www-data:www-data /var/www/veenixnew

# Set proper permissions
sudo find /var/www/veenixnew -type f -exec chmod 644 {} \;
sudo find /var/www/veenixnew -type d -exec chmod 755 {} \;

# Special permissions for storage
sudo chmod -R 775 /var/www/veenixnew/storage
sudo chmod -R 775 /var/www/veenixnew/bootstrap/cache

# Ensure uploads directory is writable
sudo chmod -R 775 /var/www/veenixnew/public/uploads
sudo chmod -R 775 /var/www/veenixnew/storage/app/public/uploads
```

### 7. PHP-FPM Configuration for Long Processes
Edit your PHP-FPM configuration:
```bash
# Find your PHP-FPM configuration
sudo nano /etc/php/8.2/fpm/pool.d/www.conf
```

Update these settings:
```ini
; Increase execution time
max_execution_time = 3600

; Increase memory limit
memory_limit = 512M

; Remove exec from disabled functions if present
disable_functions = 
```

Restart PHP-FPM:
```bash
sudo systemctl restart php8.2-fpm
```

## Testing the Fix

### 8. Test HLS Conversion
1. Go to your admin panel
2. Upload a video or use existing one
3. Click "Convert to HLS"
4. Monitor the logs:
   ```bash
   # Monitor Laravel logs
   tail -f /var/www/veenixnew/storage/logs/laravel.log | grep "🎬\|🔧\|✅\|❌"
   
   # Monitor worker logs
   tail -f /var/www/veenixnew/storage/logs/worker.log
   ```

### 9. Debugging Commands
```bash
# Check failed jobs
cd /var/www/veenixnew
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear queue (if needed)
php artisan queue:clear

# Check job table
mysql -u root -p veenix -e "SELECT * FROM jobs ORDER BY id DESC LIMIT 5;"

# Check failed_jobs table
mysql -u root -p veenix -e "SELECT * FROM failed_jobs ORDER BY failed_at DESC LIMIT 5;"
```

## Expected Log Output

Once the queue worker is running, you should see logs like this:
```
[2025-10-10 11:14:35] local.INFO: 🎬 Memulai konversi HLS untuk: ppni
[2025-10-10 11:14:35] local.INFO: 🔍 Searching for file with filename: ppni.mp4
[2025-10-10 11:14:35] local.INFO: 📁 File found in new location: /var/www/veenixnew/storage/app/public/uploads/ppni.mp4
[2025-10-10 11:14:35] local.INFO: 🔧 Menjalankan FFmpeg: ffmpeg -i "/var/www/veenixnew/storage/app/public/uploads/ppni.mp4" -profile:v baseline -level 3.0 -start_number 0 -hls_time 6 -hls_list_size 0 -f hls "/var/www/veenixnew/storage/app/temp/hls/the-conjuring-last-rites/index.m3u8" -y
[2025-10-10 11:16:45] local.INFO: ✅ Konversi HLS selesai: /var/www/veenixnew/storage/app/temp/hls/the-conjuring-last-rites/index.m3u8
[2025-10-10 11:16:45] local.INFO: 🚀 Memulai upload ke Wasabi storage...
[2025-10-10 11:17:00] local.INFO: 🎉 Upload ke Wasabi selesai! Total file: 15
[2025-10-10 11:17:00] local.INFO: ✅ Movie fields updated - hls_master_playlist_path: the-conjuring-last-rites/index.m3u8, hls_status: completed
```

## Monitoring Setup

### 10. Setup Log Rotation
```bash
# Create logrotate configuration for Laravel logs
sudo nano /etc/logrotate.d/laravel
```

Add:
```
/var/www/veenixnew/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        sudo supervisorctl restart lar-worker:*
    endscript
}
```

## Troubleshooting

### Common Issues and Solutions

1. **Worker stops running**
   ```bash
   sudo supervisorctl restart lar-worker:*
   ```

2. **Permission denied errors**
   ```bash
   sudo chown -R www-data:www-data /var/www/veenixnew/storage
   sudo chmod -R 775 /var/www/veenixnew/storage
   ```

3. **FFmpeg not found**
   ```bash
   sudo apt install ffmpeg
   which ffmpeg
   ```

4. **Memory issues**
   ```bash
   # Increase PHP memory limit
   sudo nano /etc/php/8.2/fpm/php.ini
   # Update: memory_limit = 512M
   sudo systemctl restart php8.2-fpm
   ```

5. **Jobs getting stuck**
   ```bash
   php artisan queue:clear
   php artisan queue:restart
   sudo supervisorctl restart lar-worker:*
   ```

This setup should resolve the HLS conversion issue on your Ubuntu production server.
