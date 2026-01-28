═══════════════════════════════════════════════════════════════
  LARAVEL RMS - FINAL CONFIGURATION
═══════════════════════════════════════════════════════════════

URL STRUCTURE:
✅ https://gzlpro.com/rms-dev/public/ (with /public/)

═══════════════════════════════════════════════════════════════
  .env CONFIGURATION
═══════════════════════════════════════════════════════════════

Update your .env file:

APP_URL=https://gzlpro.com/rms-dev/public
ASSET_URL=/rms-dev/public
CLEAR_CACHE_TOKEN=your-secret-token-here

(Keep /public/ in both URLs)
(CLEAR_CACHE_TOKEN is for the cache clear route security)

═══════════════════════════════════════════════════════════════
  API CONFIGURATION
═══════════════════════════════════════════════════════════════

API URLs will be:
✅ https://gzlpro.com/rms-dev/public/api/v1/publications
✅ https://gzlpro.com/rms-dev/public/api/user/login

Sanctum config updated to use APP_URL automatically.

═══════════════════════════════════════════════════════════════
  FILES UPDATED (LATEST FIX - URL GENERATION)
═══════════════════════════════════════════════════════════════

✅ app/Providers/AppServiceProvider.php 
   - Auto-detects subdirectory and forces root URL
   - Fixes route() and url() helpers to include subdirectory
   - Handles both APP_URL from .env and auto-detection

✅ resources/views/layouts/public.blade.php
   - Injects BASE_URL and ASSET_URL for JavaScript
   - Allows JS to use correct paths in subdirectory

✅ public/js/publications.js
   - Updated to use BASE_URL instead of hardcoded paths
   - Fixes API calls and links to work in subdirectory

✅ resources/views/publications/index.blade.php
   - Fixed hardcoded URLs in JavaScript
   - Now uses BASE_URL for all fetch calls and links

✅ routes/web.php
   - Added /clear-cache route for easy cache clearing
   - Protected with token-based security (CLEAR_CACHE_TOKEN)
   - Clears: config, cache, route, and view caches

═══════════════════════════════════════════════════════════════
  FINAL STEPS
═══════════════════════════════════════════════════════════════

1. Update .env (CRITICAL - MUST BE SET CORRECTLY):
   APP_URL=https://gzlpro.com/rms-dev/public
   ASSET_URL=/rms-dev/public
   CLEAR_CACHE_TOKEN=your-secret-token-here
   
   ⚠️ IMPORTANT NOTES:
   - APP_URL MUST include the full path: https://gzlpro.com/rms-dev/public
   - Do NOT use trailing slash in APP_URL
   - ASSET_URL should be: /rms-dev/public (with leading slash, no trailing)
   - After updating .env, ALWAYS clear caches (see step 3)

2. Upload updated files:
   - app/Providers/AppServiceProvider.php
   - resources/views/layouts/public.blade.php
   - public/js/publications.js
   - resources/views/publications/index.blade.php

3. Clear all caches on server:
   
   Option A - Use Web Route (EASIEST):
   Visit: https://gzlpro.com/rms-dev/public/clear-cache?token=YOUR_SECRET_TOKEN
   
   First, add to your .env file:
   CLEAR_CACHE_TOKEN=your-secret-token-here
   
   Option B - Use Artisan Commands:
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   
   Option C - Delete manually:
   - bootstrap/cache/config.php
   - bootstrap/cache/routes.php
   - bootstrap/cache/services.php

4. Test:
   ✅ https://gzlpro.com/rms-dev/public/
   ✅ https://gzlpro.com/rms-dev/public/api/v1/publications

═══════════════════════════════════════════════════════════════
