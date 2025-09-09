# Laravel WeatherApp

This is a Laravel project for a weather application. Follow the instructions below to run it locally.

## Requirements

- PHP >= 8.1  
- Composer  
- Node.js & NPM (for frontend assets, if needed)  

## Installation

1. **Clone the repository**

```bash
git clone <repository-url>
cd <repository-folder>
```

2. **Install PHP dependencies**

```bash
composer install
```

3. **Copy `.env` file**

```bash
cp .env.example .env
```

4. **Set your environment variables**

Open the `.env` file and configure your API key:

```bash
TOMORROW_API_KEY=your_api_key_here
```


6. **Run database migrations**

```bash
php artisan migrate
```

7. **Install frontend dependencies (if applicable)**

```bash
npm install
npm run dev
```

8. **Run the development server**

```bash
php artisan serve
```

Your application should now be running at `http://localhost:8000`.

---

## Why I made these choices

- **Backend**: I chose PHP with Laravel because it is the framework I am most familiar with. Laravel provides a structured, clean way to build web applications quickly, which made it ideal for this diagnostic project.

- **Frontend**: I used Tailwind CSS because it allows me to rapidly style components without leaving the HTML, keeping the frontend clean and maintainable. Tailwind’s utility-first approach made it easy to implement responsive designs quickly.

- **Data storage**: Weather data is cached for **1 hour**. 
---
## Future Improvements

- **Database for weather data**: Currently, I use caching for 1 hour to reduce local API requests. In the future, a database could be added to store weather data permanently. This would allow the app to minimize the number of API requests further, especially given the small API rate limit (25 requests per hour), while still providing historical weather data for users.