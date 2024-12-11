# 📚 Library Management API

Technical test for backend developer position at PT. Altech Omega Andalan. Simple RESTful API for a library management system. This repository contains a simple RESTful API for a library management system. The API manages authors and books, allowing operations such as creation, retrieval, update, and deletion of records. This project demonstrates clean code design, database schema implementation, unit testing, and performance tuning techniques.

---

## 📑 About this Project

This project showcases my backend development expertise, with a focus on designing and implementing robust, scalable, and maintainable APIs. I chose PHP (Laravel) to complete this technical test. Laravel is a highly popular framework widely adopted in production environments. It offers numerous built-in features that simplify handling a variety of use cases. Another reason for choosing Laravel is my extensive experience with it—I have been using the framework for over four years. This familiarity allows me to efficiently apply its best practices to deliver high-quality solutions.

### Tech Stack

-   Language: PHP
-   Framework: Laravel
-   Database: MySQL/Postgresql/SQLite
-   Documentation: Swagger
-   Testing: PHPUnit

### Design Pattern

I use the Repository Design Pattern in this project. The Repository Design Pattern in Laravel is utilized to separate data access logic from the business logic of the application. This approach offers benefits such as more structured code, easier testing, and flexibility to accommodate changes. By using a repository, you can replace or modify the data source (e.g., switching from a database to an external API) without altering the business logic in controllers or services. Additionally, this pattern facilitates the implementation of SOLID principles, particularly the Dependency Inversion Principle, as controllers or services depend only on contracts (interfaces) rather than direct implementations. This makes the application easier to maintain and extend.

### Database Design

#### Table: `authors`

| Column Name  | Data Type   | Attributes                  | Description                      |
| ------------ | ----------- | --------------------------- | -------------------------------- |
| `id`         | `bigint`    | Primary Key, Auto Increment | Unique identifier for the author |
| `name`       | `string`    | Indexed, Not Null           | Name of the author               |
| `bio`        | `text`      | Nullable                    | Short biography of the author    |
| `birth_date` | `date`      | Nullable                    | Birth date of the author         |
| `created_at` | `timestamp` | Auto-managed by Laravel     | Record creation timestamp        |
| `updated_at` | `timestamp` | Auto-managed by Laravel     | Record update timestamp          |

#### Indexes

| Index Name           | Columns | Description                                       |
| -------------------- | ------- | ------------------------------------------------- |
| `authors_name_index` | `name`  | Index for optimizing queries on the `name` column |

---

#### Table: `books`

| Column Name    | Data Type   | Attributes                  | Description                            |
| -------------- | ----------- | --------------------------- | -------------------------------------- |
| `id`           | `bigint`    | Primary Key, Auto Increment | Unique identifier for the book         |
| `title`        | `string`    | Indexed, Not Null           | Title of the book                      |
| `description`  | `text`      | Nullable                    | Description of the book                |
| `publish_date` | `date`      | Indexed, Not Null           | Publication date of the book           |
| `author_id`    | `bigint`    | Foreign Key, Not Null       | References `id` in the `authors` table |
| `created_at`   | `timestamp` | Auto-managed by Laravel     | Record creation timestamp              |
| `updated_at`   | `timestamp` | Auto-managed by Laravel     | Record update timestamp                |

#### Foreign Keys

| Foreign Key Name          | Column      | References   | Action on Delete | Description                                                |
| ------------------------- | ----------- | ------------ | ---------------- | ---------------------------------------------------------- |
| `books_author_id_foreign` | `author_id` | `authors.id` | Cascade          | Deletes book records when the associated author is deleted |

#### Indexes

| Index Name                 | Columns        | Description                                               |
| -------------------------- | -------------- | --------------------------------------------------------- |
| `books_title_index`        | `title`        | Index for optimizing queries on the `title` column        |
| `books_publish_date_index` | `publish_date` | Index for optimizing queries on the `publish_date` column |

### Performance Tuning

To optimize the performance of this project, I implemented several tuning strategies.

-   **Eager loading (N+1)**, was utilized to minimize the number of database queries by preloading related data when necessary.

    [`BookRepository.php`](https://github.com/abela-a/altech-omega-api/blob/main/app/Repositories/BookRepository.php#L39)

-   **Rate Limiting**, to prevent server overload, rate limiting was applied, ensuring fair usage and protecting the application from excessive requests.

    Configured in `RouteServiceProvider` using `->middleware('throttle:60,1')`.

    Automatic in laravel 11.

-   **Truncate Text**, for improved efficiency in displaying large amounts of data, bio and description text fields were truncated to show only a summary, reducing unnecessary data transfer.

    [`AuthorCollection.php`](https://github.com/abela-a/altech-omega-api/blob/main/app/Http/Resources/AuthorCollection.php#L18-L24)

    [`BookCollection.php`](https://github.com/abela-a/altech-omega-api/blob/main/app/Http/Resources/BookCollection.php#L18-L24)

-   **Database Indexing**, was added to frequently queried columns, significantly speeding up data retrieval.

    [`2024_12_09_113746_create_authors_table.php`](https://github.com/abela-a/altech-omega-api/blob/main/database/migrations/2024_12_09_113746_create_authors_table.php#L21)

    [`2024_12_09_144545_create_books_table.php`](https://github.com/abela-a/altech-omega-api/blob/main/database/migrations/2024_12_09_144545_create_books_table.php#L22-L23)

-   **Caching**, caching mechanisms were used to store frequently accessed data, reducing load times and database hits, thus ensuring the application runs smoothly and efficiently. Additionally, route caching was employed to optimize the application’s route loading process.

    [`BookRepository.php - index()`](https://github.com/abela-a/altech-omega-api/blob/main/app/Repositories/BookRepository.php#L24-L32)

    [`BookRepository.php - show()`](https://github.com/abela-a/altech-omega-api/blob/main/app/Repositories/BookRepository.php#L38-L40)

    [`BookRepository.php - authorBooks()`](https://github.com/abela-a/altech-omega-api/blob/main/app/Repositories/BookRepository.php#L80-L88)

    [`AuthorRepository.php - index()`](https://github.com/abela-a/altech-omega-api/blob/main/app/Repositories/AuthorRepository.php#L25-L34)

    [`AuthorRepository.php - show()`](https://github.com/abela-a/altech-omega-api/blob/main/app/Repositories/AuthorRepository.php#L39-L41)

    ```sh
    php artisan route:cache
    ```

---

## ⚡ Getting Started

### Requirements

-   PHP >= 8.0
-   Composer
-   MySQL/Postgresql/SQLite (preferable)

### Installation

1. Clone the repository:

    ```sh
    git clone https://github.com/abela-a/library-management-system-api.git
    ```

2. Navigate to the project directory:

    ```sh
    cd library-management-system-api
    ```

3. Install dependencies:

    ```sh
    composer install
    ```

4. Set up the environment file:

    ```sh
    cp .env.example .env
    ```

    Configure the database and other environment variables as needed.

    ```env
    DB_CONNECTION=sqlite/mysql/postgresql
    # DB_HOST=127.0.0.1
    # DB_PORT=3306
    # DB_DATABASE=technical_test_altech_omega
    # DB_USERNAME=root
    # DB_PASSWORD=
    ```

5. Run migrations and seed the database:

    ```sh
    php artisan migrate --seed
    ```

### Running the Project

To start the project locally, use the following command:

```sh
php artisan serve
```

---

## 🔖 API Documentation

The API documentation is available via Swagger. Once the project is running, visit the following URL to explore the endpoints:

```
http://localhost:8000/api/documentation
```

---

## 📝 Testing

Unit and feature tests are implemented using PHPUnit to ensure the reliability of the API. Run tests using:

```bash
php artisan test
# or
./vendor/bin/phpunit
```

---

## 📧 Contact

I would greatly appreciate any feedback on my work, as it will help me improve and grow as a developer. If you have any comments, suggestions, or questions regarding this project, please feel free to reach out:

-   **Name**: Abel A Simanungkalit
-   **Email**: [abelardhana96@gmail.com](mailto:abelardhana96@gmail.com)
-   **GitHub Profile**: [https://github.com/abela-a](https://github.com/abela-a)

Thank you for taking the time to review this project!
