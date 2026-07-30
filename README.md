# SunnyCustomer

Test assignment written in PHP 8.2.

## Assignment text

1. Intention is to make PHP application which communicates to some relational database (MySQL, MariaDB etc.). Frontend can be made using any frontend development framework, but Vue.JS is preferred since we are using Vue.JS at work.
2. Please select your favourite database.
    - Create new database called CUSTOMER with attributes (ID, FirstName, Lastname, DateofBirth, Username, Password).
    - Add possibility to manipulate (add/modify/create) with customer data in user interface. There must be form in UI where you can add data; already added data must be displayed in UI in table form.
3. Add some unit tests if and when you will find them to be useful.
4. Add some validations to user interface before save operation and some warnings before performing delete operation. Choose the CSS according to your taste.
5. Add some documentation with your application, so that some other developer or tester is able to understand how to run/test/develop the application.

If you have some other technologies or skills to demonstrate (e.g. usage of authentication system), then it gives extra points.

## Requirements

- PHP 8.2
- Composer
- MySQL 8 (tests can be run without it)

PHP extensions:

- pdo_mysql
- pdo_sqlite
- zip

## Installation

```bash
composer install
vendor/bin/phpunit
```

## Database

```mysql
CREATE DATABASE sunny;
CREATE USER 'sunny'@'localhost' IDENTIFIED BY 'StrongPassword';
GRANT ALL PRIVILEGES ON sunny.* TO 'sunny'@'localhost';
FLUSH PRIVILEGES;
CREATE TABLE customer (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name    VARCHAR(100) NOT NULL,
    last_name     VARCHAR(100) NOT NULL,
    birth_date    DATE NOT NULL,
    user_name     VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uk_customer_user_name UNIQUE (user_name)
);
```

## Configure

```
config/database.php
```

## Running tests

```bash
vendor/bin/phpunit
```

## Running application

```bash
composer install
php -S localhost:8000 -t public
```

## Test request

Use `requests.http`.
