<p align="center"><a href="https://tensile.konstruktiv.nl" target="_blank"><img style="border-radius: 12px; overflow: hidden;" src="https://raw.githubusercontent.com/konstruktivism/tensile/refs/heads/main/public/img/tensile-banner.png?token=GHSAT0AAAAAACV7ZWCFWQPIOZPQ6OUVYY7OZYFSKPQ" width="100%" alt="Tensile Logo"></a></p>

<p align="center"><a href="https://tensile.konstruktiv.nl" target="_blank"><img style="border-radius: 12px; overflow: hidden;" src="https://github.com/konstruktivism/tensile/blob/main/public/img/banner-frontpage.png?raw=true" width="100%" alt="Tensile Logo"></a></p>

<p align="center"><a href="https://tensile.konstruktiv.nl" target="_blank"><img style="border-radius: 12px; overflow: hidden;" src="https://github.com/konstruktivism/tensile/blob/main/public/img/banner-project.png?raw=true" width="100%" alt="Tensile Logo"></a></p>


## About Tensile
Another minimal project management tool to streamline your weekly workflow. Tensile is a web application that allows users to create and manage their own work logs for Clients. Manage organisations, clients and projects.

## Features
- **Weekly and Monthly notifications**: Of all the logged tasks of your project delivered to your mailbox.
- **The right data from your Tools**: Gathered from Jira, GitHub for a simple summary.
- **Reporting**: Generate detailed reports to monitor your project\'s progress.


## Installation
1. Clone the repository:
    ```sh
    git clone https://github.com/konstruktivism/tensile.git
    cd tensile
    ```

2. Install dependencies:
    ```sh
    composer install
    npm install
    npm run dev
    ```

3. Copy the `.env.example` file to `.env` and configure your environment variables:
    ```sh
    cp .env.example .env
    php artisan key:generate
    ```

4. Run the migrations:
    ```sh
    php artisan migrate
    ```

6. Start the local development server:
    ```sh
    php artisan serve
    ```
## Google Calendar

To integrate Google Calendar, set the following environment variables in your `.env` file:
- `GOOGLE_CALENDAR_AUTH_CODE`
- `GOOGLE_CALENDAR_ID`

## Moneybird

To integrate Moneybird, set the following environment variables in your `.env` file:
- `MONEYBIRD_TOKEN`
- `MONEYBIRD_ADMINISTRATION_ID`
- `MONEYBIRD_LEDGER_ACCOUNT_ID`

## Mailcoach

To integrate Mailcoach, set the following environment variable in your `.env` file:
- `MAILCOACH_TOKEN`

## License
The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT). 
