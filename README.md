# Hotelly - Hotel Reservation System

A modern hotel reservation system built with Laravel and modern frontend technologies.

## Features

- User authentication and authorization
- Hotel and room management
- Booking system
- Admin dashboard
- User dashboard
- Payment integration (mock)
- Responsive design

## Requirements

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL
- XAMPP/WAMP/MAMP

## Installation

1. Clone the repository:
```bash
git clone [repository-url]
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install NPM dependencies:
```bash
npm install
```

4. Create and configure .env file:
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your database in .env file

6. Run migrations:
```bash
php artisan migrate
```

7. Seed the database:
```bash
php artisan db:seed
```

8. Start the development server:
```bash
php artisan serve
```

9. Compile assets:
```bash
npm run dev
```

## Usage

Visit `http://localhost:8000` to access the application.

Default admin credentials:
- Email: admin@hotelly.com
- Password: password

## License

This project is licensed under the MIT License.
