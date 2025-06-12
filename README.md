# Campus Connect

Campus Connect is a placement management tool for educational institutions, currently under development. Built with Laravel and Filament, it provides an administrative interface to manage students, departments, and divisions, streamlining the campus placement process.

## Features

-   Manage students, departments, and divisions to streamline the placement process.
-   A beautiful and responsive user interface powered by the TALL stack.
-   Simple CSV import for bulk student creation.

## Technology Stack

-   **Backend:** PHP 8.2, Laravel 11
-   **Admin Panel:** Filament 3
-   **Frontend:** Tailwind CSS, Alpine.js
-   **Database:** SQLite (default), MySQL, or PostgreSQL

## Installation

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/prathzzzz/campus-connect.git
    cd campus-connect
    ```

2.  **Install dependencies:**
    ```bash
    composer install
    npm install
    ```

3.  **Setup environment:**
    ```bash
    cp .env.example .env
    ```
    Then, configure your database and other environment variables in the `.env` file.

4.  **Generate application key:**
    ```bash
    php artisan key:generate
    ```

5.  **Run database migrations and seeders:**
    This will create the necessary tables and populate them with initial data, including the admin user.
    ```bash
    php artisan migrate --seed
    ```

6.  **Build frontend assets:**
    ```bash
    npm run build
    ```

## Usage

1.  **Start the development server:**
    ```bash
    php artisan serve
    ```

2.  **Access the application:**
    Open your browser and navigate to `http://127.0.0.1:8000`.

3.  **Login to the admin panel:**
    -   **URL:** `http://127.0.0.1:8000/admin`
    -   **Email:** `admin@example.com`
    -   **Password:** `password`

    The default admin user is created by the `DatabaseSeeder`.


