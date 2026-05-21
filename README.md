# Laravel Vue Tailwind Base

Base project nay danh cho viec hoc Laravel + Vue + TailwindCSS tu nen CRUD co ban.

## Cach chay project

Mo terminal tai thu muc project:

```bash
cd /d E:\laravel-vue-tailwind-base
```

Cai package JavaScript:

```bash
npm install
```

Bat MySQL trong Laragon, roi tao database:

```sql
CREATE DATABASE laravel_vue_tailwind_base CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Chay migration:

```bash
php artisan migrate
```

Chay Laravel:

```bash
php artisan serve
```

Mo them terminal thu hai va chay Vite:

```bash
npm run dev
```

Truy cap:

```text
http://127.0.0.1:8000
```

## Cac file nen biet

- `routes/web.php`: khai bao route web.
- `resources/views/welcome.blade.php`: file Blade chua the `<div id="app"></div>`.
- `resources/js/app.js`: noi Vue duoc gan vao trang.
- `resources/js/App.vue`: component Vue dau tien.
- `resources/css/app.css`: TailwindCSS.
- `.env`: cau hinh database va moi truong chay local.

## Goi y bai hoc tiep theo

Tao module CRUD dau tien voi Products:

```bash
php artisan make:model Product -mcr
```

Sau do ban se sua migration, model, controller va route de tao chuc nang quan ly san pham.
