

## About Filament App Starter

A simple Filament starting app for your next web projects with nice features such as:

- Filament shield plugin
- spatie laravel settings plugin
- filament laravel log plugin

## Getting Started
#### Getting Started

Create project with this git command:

```bash
git clone https://github.com/stevemo/Filament-App-Starter.git
```

Setup your env:

```bash
cd filament-app-starter
composer install
cp .env.example .env
```

Run migration & seeder:

```bash
php artisan migrate
php artisan db:seed
```

<p align="center">or</p>

```bash
php artisan migrate:fresh --seed
```

Generate key:

```bash
php artisan key:generate
```

Run :

```bash
npm run dev
OR
npm run build
```

```bash
php artisan serve
```

Now you can access with `/admin` path, using:

```bash
username: admin
password: password
```

## License

The Filament starter app is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
