# Delos International Website

Official website for Delos International, an Iraqi luxury interiors brand showcasing Italian kitchens, furniture, wardrobes, projects, showroom branches, and consultation booking.

## Stack

- Laravel 13
- PHP 8.3
- Vite
- Tailwind CSS 4

## Pages

- Home
- About
- Services
- Projects
- Brands
- Branches
- Contact

## Local Development

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
php artisan serve
npm run dev
```

## Production Notes

- Do not commit `.env` or any real credentials.
- Install PHP and Node dependencies on the server or in CI before deployment.
- Build frontend assets with `npm run build`.

## Repository Purpose

This repository is intended for the Delos International website codebase only. Local AI tooling folders and generated dependencies are excluded from version control.
