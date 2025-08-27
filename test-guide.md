# Testing Guide for Thumbnail Processing System

## 1. Start the Application

### Terminal 1 - Laravel Server
```bash
php artisan serve
```

### Terminal 2 - Queue Workers (Priority Processing)
```bash
# Start all priority queues
start-workers.bat

# OR manually start each queue:
php artisan queue:work --queue=thumbnails-high --sleep=1 --tries=3
php artisan queue:work --queue=thumbnails-medium --sleep=2 --tries=3  
php artisan queue:work --queue=thumbnails-low --sleep=3 --tries=3
```

## 2. Test Users & Login

Visit: http://localhost:8000/login

**Test Accounts:**
- Free: free@example.com / password (50 images max)
- Pro: pro@example.com / password (100 images max)
- Enterprise: enterprise@example.com / password (200 images max)

## 3. Test Image URLs

Use these sample URLs for testing:

```
https://picsum.photos/800/600?random=1
https://picsum.photos/800/600?random=2
https://picsum.photos/800/600?random=3
https://picsum.photos/800/600?random=4
https://picsum.photos/800/600?random=5
https://picsum.photos/800/600?random=6
https://picsum.photos/800/600?random=7
https://picsum.photos/800/600?random=8
https://picsum.photos/800/600?random=9
https://picsum.photos/800/600?random=10
```

## 4. Testing Scenarios

### A. Basic Functionality Test
1. Login as Free user
2. Navigate to "Thumbnails" 
3. Paste 5 image URLs
4. Submit and watch processing
5. Click "View Details" to see individual image status

### B. Quota Limit Test
1. Login as Free user (50 image limit)
2. Try submitting 60 URLs
3. Should get error: "Image count (60) exceeds quota limit (50)"

### C. Priority Processing Test
1. **Setup:** Start queue workers in separate terminals
2. **Test:** Submit requests simultaneously:
   - Enterprise user: 10 images
   - Pro user: 10 images  
   - Free user: 10 images
3. **Expected:** Enterprise processes first, then Pro, then Free

### D. Status Filtering Test
1. Submit multiple requests
2. Use status filter dropdown: All, Pending, Processing, Completed, Failed
3. Verify filtering works correctly

### E. Error Handling Test
1. Submit invalid URLs (non-image URLs)
2. Submit empty form
3. Verify proper error messages

## 5. Monitor Processing

### Queue Status
```bash
php artisan queue:monitor
```

### Database Check
```bash
php artisan tinker
```
```php
// Check requests
App\Models\ThumbnailRequest::with('images')->get();

// Check processing status
App\Models\ThumbnailImage::where('status', 'processed')->count();
```

## 6. Expected Results

### Processing Simulation
- 90% success rate (some images will "fail" randomly)
- Processing takes 1-3 seconds per image
- Status updates in real-time

### Priority Verification
- Enterprise jobs process faster (sleep=1s)
- Pro jobs process medium speed (sleep=2s)
- Free jobs process slower (sleep=3s)

### UI Behavior
- Progress shows: "5/10" format
- Status badges: Pending (blue), Processing (yellow), Completed (green), Failed (red)
- Real-time updates without page refresh

## 7. Troubleshooting

### No Processing Happening
- Check if queue workers are running
- Verify database connection
- Check Laravel logs: `storage/logs/laravel.log`

### 403 Errors
- Ensure user is logged in
- Check if accessing own requests only

### Frontend Issues
- Run `npm run build` if assets not loading
- Check browser console for errors