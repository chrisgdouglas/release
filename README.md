A digital photo release form that emails the signed release to the client and the photographer.

Reads associated photographers from a CSV file (default: `users.csv`) stored outside the webroot.

Format:
```
foob,foobar@example.com
barf,barfoo@example.com
```

## Credentials

The app expects a `creds.php` file outside the webroot. It holds your SMTP settings and is **gitignored** — never commit it.

A sample is provided as `creds.php.example`. **Copy it to `creds.php`** and fill in real values:

```bash
cp creds.php.example creds.php   # then edit creds.php
```

`creds.php` returns an array:
```php
<?php

declare(strict_types=1);

return [
    'smtp_server'   => 'smtp.your.server',
    'smtp_username' => 'your_smtp_username',
    'smtp_password' => 'your_smtp_password',
    'sender_name'   => 'Name that should be on outgoing emails',
    'subject'       => 'Your organization',
];
```

## Routes

- `/?u=<username>` — release form for that photographer
- `/?qr=<username>` — printable QR code linking to the form (works even when submissions are closed)
- `POST /` — AJAX form submission (CSRF-protected)

The recipient email is always resolved server-side from the username; it is never accepted from the client.

## Deployment

- Requires PHP 8.1.2+ and Composer (`composer install --no-dev` in production).
- Point your web server docroot at `public/`. Keep `creds.php` and `users.csv` one level above it.
- Example server config: `deploy/nginx.conf.example` (or the bundled `public/.htaccess` for Apache).

## Development

```bash
composer install
composer test    # PHPUnit
composer stan    # PHPStan level 8
php -S 127.0.0.1:8000 -t public
```
