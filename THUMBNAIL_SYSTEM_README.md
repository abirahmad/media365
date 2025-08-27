# Thumbnail Processing System

A Laravel-based thumbnail processing system with tier-based quotas and priority queue processing.

## Features

- **Three User Tiers:**
  - Free: 50 images per request
  - Pro: 100 images per request  
  - Enterprise: 200 images per request

- **Priority Queue Processing:**
  - Enterprise users: 3x priority
  - Pro users: 2x priority
  - Free users: Base priority

- **Background Processing:** Asynchronous thumbnail generation simulation
- **Real-time Status Updates:** Track processing progress
- **Shopify Polaris UI:** Modern, responsive interface

## Setup Instructions

### 1. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### 2. Queue Workers
Run the batch file to start priority queue workers:
```bash
start-workers.bat
```

Or manually start individual workers:
```bash
# High priority (Enterprise)
php artisan queue:work --queue=thumbnails-high --sleep=1 --tries=3

# Medium priority (Pro)  
php artisan queue:work --queue=thumbnails-medium --sleep=2 --tries=3

# Low priority (Free)
php artisan queue:work --queue=thumbnails-low --sleep=3 --tries=3
```

### 3. Test Users
- **Free User:** free@example.com / password
- **Pro User:** pro@example.com / password  
- **Enterprise User:** enterprise@example.com / password

## Usage

1. Login with any test user
2. Navigate to "Thumbnails" in the sidebar
3. Paste image URLs (one per line) in the text area
4. Submit for processing
5. Monitor progress in the table below

## Architecture

### SOLID Principles Implementation

- **Single Responsibility:** Each class has one clear purpose
- **Open/Closed:** Services are extensible without modification
- **Liskov Substitution:** Models implement consistent interfaces
- **Interface Segregation:** Focused, minimal interfaces
- **Dependency Inversion:** Controllers depend on abstractions (services)

### Key Components

- **Models:** User, ThumbnailRequest, ThumbnailImage
- **Services:** ThumbnailService, NodeJsSimulatorService
- **Jobs:** ProcessThumbnailBatch, ProcessSingleThumbnail
- **Controllers:** ThumbnailController
- **Policies:** ThumbnailRequestPolicy

### Queue System

The system uses Laravel's database queue with priority-based processing:
- Different queues for each user tier
- Automatic job distribution based on user priority
- Simulated processing with 90% success rate

## API Endpoints

- `GET /thumbnails` - List user's thumbnail requests
- `POST /thumbnails` - Create new thumbnail request
- `GET /thumbnails/{id}` - View specific request details

## Frontend

Built with React and Shopify Polaris components:
- Responsive design
- Real-time status updates
- Filtering and pagination
- User-friendly error handling