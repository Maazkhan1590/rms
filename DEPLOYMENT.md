# Laravel RMS Deployment Guide

## Quick Deployment Steps

### 1. Upload Project Files
Upload all project files to your server via FTP/cPanel File Manager.

### 2. Set Up Domain (Choose ONE method)

#### Method A: Point Domain Directly to Public Folder (RECOMMENDED)
- Point your domain `https://gzlpro.com/rms-dev/` to the `public` folder directly
- This way you access: `https://gzlpro.com/rms-dev/` (no `/public/` in URL)

#### Method B: Keep Current Setup
- If you must use `https://gzlpro.com/rms-dev/public/`, ensure:
  - Domain points to `/rms-dev/` folder
  - Access via `/rms-dev/public/` URL

### 3. Configure Environment File
Create `.env` file in the root directory with these settings:

```env
APP_NAME="RMS"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://gzlpro.com/rms-dev/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

**Important:** 
- Generate APP_KEY: Run `php artisan key:generate` after uploading
- Update APP_URL to match your actual domain
- Update database credentials

### 4. Set File Permissions
Set these permissions on your server:
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod -R 755 public
```

### 5. Run Migrations (if needed)
```bash
php artisan migrate
```

### 6. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## Troubleshooting

### Blank Page / "React App" Title
✅ **FIXED:** Removed React app files (`index.html`, `static/` folder)

### 500 Internal Server Error
- Check `.env` file exists and has correct APP_KEY
- Check file permissions (storage, bootstrap/cache)
- Check error logs: `storage/logs/laravel.log`

### Assets Not Loading (CSS/JS)
- Ensure `APP_URL` in `.env` matches your domain
- Check that `public/css/` and `public/js/` folders are uploaded
- Clear cache: `php artisan config:clear`

### Database Connection Error
- Verify database credentials in `.env`
- Ensure database exists on server
- Check database user has proper permissions

## File Structure After Deployment

```
/rms-dev/
├── app/
├── bootstrap/
├── config/
├── database/
├── public/          ← Domain should point here
│   ├── css/
│   ├── js/
│   ├── index.php   ← Laravel entry point
│   └── .htaccess
├── resources/
├── routes/
├── storage/        ← Must be writable
├── vendor/
├── .env            ← Create this with your settings
└── composer.json
```

## Notes
- ✅ No npm/node required - project uses CDN and static assets
- ✅ No build step needed - just upload and configure
- ✅ All frontend assets are in `public/` folder
