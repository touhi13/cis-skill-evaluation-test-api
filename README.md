# CIS Skill Evaluation Test App

This project is a simple web application that demonstrates a user authentication and subscription system. Users can register, log in, and manage their subscription using Stripe. The system includes custom authentication, account activation, and deactivation features with email notifications and payment reporting.

## Installation

1. Clone the repository to your local machine:

    ```bash
    git clone https://github.com/touhi13/cis-skill-evaluation-test-api.git
    ```

2. Navigate to the project directory:

    ```bash
    cd cis-skill-evaluation-test-api
    ```

3. Install composer dependencies:

    ```bash
    composer install
    ```

4. Copy the `.env.example` file and rename it to `.env`:

    ```bash
    cp .env.example .env
    ```

5. Generate an application key:

    ```bash
    php artisan key:generate
    ```

6. Configure your database in the `.env` file:

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_username
    DB_PASSWORD=your_database_password
    ```

7. Configure your mail driver and SMTP settings in the `.env` file for sending reminder emails:

    ```env
    MAIL_MAILER=smtp
    MAIL_HOST=smtp.mailtrap.io
    MAIL_PORT=2525
    MAIL_USERNAME=your_mailtrap_username
    MAIL_PASSWORD=your_mailtrap_password
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS=your_email@example.com
    MAIL_FROM_NAME="${APP_NAME}"
    ```

    Replace `your_mailtrap_username`, `your_mailtrap_password`, and `your_email@example.com` with your actual Mailtrap credentials or other SMTP server details.

8. Configure your Stripe settings in the `.env` file:

    ```env
    STRIPE_KEY=your_stripe_key
    STRIPE_SECRET=your_stripe_secret
    STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret
    STRIPE_PRICE_ID=your_stripe_product_price_id
    ```

    Replace `your_stripe_key`, `your_stripe_secret`, and `your_stripe_webhook_secret` with your actual Stripe API keys and webhook secret.

9. Update the `APP_URL` for the React frontend in your `.env` file:

    ```env
    APP_URL=http://localhost:5173
    ```

    Ensure to restart your React development server after making this change.

10. Run migrations to create database tables:

    ```bash
    php artisan migrate
    ```

11. Generate a JWT secret key for API authentication:

    ```bash
    php artisan jwt:secret
    ```

12. Set up the scheduler by adding the following Cron entry to your server. This will call the Laravel command scheduler every minute:

    ```bash
    php artisan schedule:work
    ```

## Usage

1. Start the Laravel development server:

    ```bash
    php artisan serve
    ```

2. Access the application in your web browser at `http://127.0.0.1:8000`.

3. Register a new account or log in with existing credentials.

4. Subscribe to activate your account and manage your subscription using Stripe.

5. View your monthly payment report and download invoices as needed. Reminder emails will be sent to specified recipients based on account deactivation details.

## Additional Configuration

- For advanced mail configuration, refer to the Laravel documentation: [Mail Configuration](https://laravel.com/docs/mail).
- For Stripe integration details, refer to the Stripe documentation: [Stripe Integration](https://stripe.com/docs).
- For scheduling tasks, refer to the Laravel documentation: [Task Scheduling](https://laravel.com/docs/scheduling).

## Contributing

Contributions are welcome! Please feel free to submit any issues or pull requests.

## License

This project is open-source and available under the [MIT License](LICENSE).
