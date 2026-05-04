<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework. You can also check out [Laravel Learn](https://laravel.com/learn), where you will be guided through building a modern Laravel application.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Queue Implementation: Order Confirmation Emails

To improve checkout performance, order confirmation emails are now handled asynchronously using Laravel's database queue.

### Checkout Performance Comparison

| Metric | Before (Synchronous) | After (Queued) |
| :--- | :--- | :--- |
| **Response Time** | ~2-5 seconds (depends on SMTP) | ~200-500ms |
| **User Experience** | Page hangs until email is sent | Immediate redirect to success page |
| **Reliability** | Fails if SMTP is down | Automatic retries via queue |

### Mail Methods Explained

*   **`Mail::send()`**: Sends the email immediately during the request lifecycle. This blocks the user from seeing the response until the email is successfully transmitted to the mail server.
*   **`Mail::queue()`**: Pushes the email job onto the default queue. The request finishes immediately, and the email is sent later by a background worker.
*   **`Mail::later($delay, $mailable)`**: Pushes the email job onto the queue with a specified delay. In this project, we use `now()->addMinutes(5)` to ensure the invoice generation is complete before the email is sent.

### Queue Worker Requirement

The queue worker **must** be running for emails to be processed. If the worker is not running:
1.  Jobs will accumulate in the `jobs` table.
2.  Users will NOT receive their confirmation emails until the worker starts.
3.  Once `php artisan queue:work --queue=emails` is started, all pending jobs will be processed.

### Retry & Failure Handling

*   **Retries**: Configured for 3 attempts (`$tries = 3`).
*   **Backoff**: Incremental delay between retries: 10s, 30s, and 60s (`$backoff = [10, 30, 60]`).
*   **Failures**: If all attempts fail (e.g., persistent SMTP error), the job moves to the `failed_jobs` table for manual inspection.

## Admin Notifications: Low Stock Alerts

The system automatically notifies administrators when a product's stock falls below the threshold (10 units).

### Mailing Best Practices

*   **`to()`**: The primary recipient of the email. They are visible to all other recipients.
*   **`cc()` (Carbon Copy)**: Additional recipients who should be informed. They are visible to everyone else.
*   **`bcc()` (Blind Carbon Copy)**: Recipients who receive the email privately. Neither the `to` nor `cc` recipients can see who is in the `bcc` list. This is useful for archiving or auditing without cluttering the recipient list.

### Why Use Config Files?

Instead of hardcoding email addresses directly in the listeners or mailables, we use `config/mail.php` and `.env`:
1.  **Security**: Keeps sensitive email addresses out of version control (GIT).
2.  **Environment Flexibility**: Use different addresses for local testing (Mailtrap) vs production (Real admin emails).
3.  **Maintainability**: If an admin's email changes, you only need to update the `.env` file instead of searching through code files.
4.  **Scalability**: Allows easy addition of more administrative roles without modifying core logic.

### Throttling & Spam Prevention

To prevent flooding admin inboxes, we use Laravel's **Cache** system to ensure only one alert is sent per product per hour.
*   **Key**: `low_stock_alert_{product_id}`
*   **Duration**: 3600 seconds (1 hour)
