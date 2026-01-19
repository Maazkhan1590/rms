# Debug Guide for URL Issues

## How to Debug URL Generation Problems

### 1. Check Server Logs

After uploading the updated files, check your Laravel log file:
```
storage/logs/laravel.log
```

Look for entries starting with `=== URL DEBUG START ===` and `=== URL DEBUG END ===`. This will show:
- What `APP_URL` is set in `.env`
- What base path is detected
- What URLs are being generated
- Test route generation results

### 2. Check Browser Console

Open your browser's Developer Tools (F12) and check the Console tab. You should see:
- `=== URL DEBUG ===` section showing:
  - `window.BASE_URL` value
  - `window.ASSET_URL` value
  - Laravel generated routes
  - Current location and pathname

When you click on a publication, you'll also see:
- `=== Publication Fetch Debug ===` showing the exact URL being fetched

### 3. Use Debug Route

Visit this URL in your browser:
```
https://gzlpro.com/rms-dev/public/debug-urls
```

This will show a JSON response with all URL-related configuration:
- Environment variables
- Config values
- Request information
- Generated test routes
- JavaScript variables

### 4. Common Issues to Check

#### Issue: APP_URL not set correctly
**Symptom:** Logs show `APP_URL from .env: NOT SET`
**Fix:** Ensure `.env` has: `APP_URL=https://gzlpro.com/rms-dev/public`

#### Issue: BASE_URL is wrong in JavaScript
**Symptom:** Console shows `window.BASE_URL` without `/rms-dev/public`
**Fix:** 
1. Clear caches: `https://gzlpro.com/rms-dev/public/clear-cache?token=YOUR_TOKEN`
2. Check that `APP_URL` in `.env` is correct
3. Hard refresh browser (Ctrl+F5)

#### Issue: Fetch URL is wrong
**Symptom:** Console shows fetch URL without subdirectory
**Fix:** Check that `window.BASE_URL` is set correctly (see above)

#### Issue: Cached routes
**Symptom:** Routes still generating old URLs even after fixes
**Fix:** 
1. Clear route cache: `php artisan route:clear`
2. Or use: `https://gzlpro.com/rms-dev/public/clear-cache?token=YOUR_TOKEN`

### 5. What to Look For

✅ **Correct:**
- `APP_URL from .env: https://gzlpro.com/rms-dev/public`
- `window.BASE_URL: https://gzlpro.com/rms-dev/public`
- `Full fetch URL: https://gzlpro.com/rms-dev/public/publications/3`

❌ **Wrong:**
- `APP_URL from .env: NOT SET` or `http://localhost`
- `window.BASE_URL: https://gzlpro.com` (missing subdirectory)
- `Full fetch URL: https://gzlpro.com/publications/3` (missing subdirectory)

### 6. Steps to Fix

1. **Check `.env` file:**
   ```env
   APP_URL=https://gzlpro.com/rms-dev/public
   ASSET_URL=/rms-dev/public
   ```

2. **Clear all caches:**
   ```
   https://gzlpro.com/rms-dev/public/clear-cache?token=YOUR_TOKEN
   ```

3. **Check debug route:**
   ```
   https://gzlpro.com/rms-dev/public/debug-urls
   ```

4. **Check browser console** for JavaScript variables

5. **Test a publication click** and check console logs

6. **Check server logs** for Laravel debug output

### 7. Remove Debug Logs (After Fixing)

Once the issue is resolved, you can remove the debug logging by:
1. Removing `\Log::info()` calls from `app/Providers/AppServiceProvider.php`
2. Removing `console.log()` calls from JavaScript files
3. Removing the `/debug-urls` route from `routes/web.php`

Or keep them for future debugging - they won't affect production performance significantly.
