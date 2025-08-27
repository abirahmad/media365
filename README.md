# Thumbnail Processing System

A Laravel-based thumbnail processing system with tier-based quotas, priority queue processing, and modern React UI using Shopify Polaris.

## Installation

### Prerequisites
- PHP 8.1+
- Composer
- Node.js & NPM
- MySQL/SQLite

### Clone & Setup

```bash
# Clone the repository
git clone <repository-url>
cd media365

# Install PHP dependencies
composer install

# Install Node dependencies
npm install --legacy-peer-deps

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build frontend assets
npm run build
```

### Configuration

Update `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=media365
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
```

## Running the Application

### 1. Start Laravel Server
```bash
php artisan serve
```

### 2. Start Queue Workers
```bash
# Option 1: Single worker for all queues
php artisan queue:work --tries=3 --timeout=60

# Option 2: Priority-based workers (separate terminals)
php artisan queue:work --queue=thumbnails-high --sleep=1 --tries=3
php artisan queue:work --queue=thumbnails-medium --sleep=2 --tries=3
php artisan queue:work --queue=thumbnails-low --sleep=3 --tries=3
```

### 3. Access Application
Visit: `http://localhost:8000`

## Test Users

| Tier | Email | Password | Quota |
|------|-------|----------|-------|
| Free | free@example.com | password | 50 images |
| Pro | pro@example.com | password | 100 images |
| Enterprise | enterprise@example.com | password | 200 images |

## Usage

1. **Login** with any test user
2. **Navigate** to "Thumbnails" in sidebar
3. **Paste URLs** (one per line) in the input field
4. **Monitor** real-time line count
5. **Submit** for processing
6. **Track** progress in the results table
7. **View Details** for individual image status

### Sample Test URLs
```
https://picsum.photos/800/600?random=1
https://picsum.photos/800/600?random=2
https://picsum.photos/800/600?random=3
https://picsum.photos/800/600?random=4
https://picsum.photos/800/600?random=5
```

## Architecture

### Backend Structure
```
app/
├── Models/
│   ├── User.php (with tier and quota methods)
│   ├── ThumbnailRequest.php
│   └── ThumbnailImage.php
├── Services/
│   ├── ThumbnailService.php (business logic)
│   └── NodeJsSimulatorService.php (processing simulation)
├── Jobs/
│   ├── ProcessThumbnailBatch.php (batch processing)
│   └── ProcessSingleThumbnail.php (individual processing)
└── Http/Controllers/
    └── ThumbnailController.php
```

### Frontend Structure
```
resources/js/
├── pages/Thumbnails/
│   ├── Index.tsx (main interface)
│   └── Show.tsx (detailed view)
└── layouts/
    └── app-layout.tsx
```

### Database Schema
- **users**: User accounts with tier information
- **thumbnail_requests**: Batch processing requests
- **thumbnail_images**: Individual image processing records
- **jobs**: Laravel queue jobs

## Development

### Queue Monitoring
```bash
# Monitor queue status
php artisan queue:monitor

# Clear failed jobs
php artisan queue:clear

# Restart queue workers
php artisan queue:restart
```

### Frontend Development
```bash
# Development mode
npm run dev

## Troubleshooting

### Queue Not Processing
- Ensure queue workers are running
- Check database connection
- Verify Laravel logs: `storage/logs/laravel.log`
