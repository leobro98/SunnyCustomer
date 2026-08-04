# SunnyCustomer

SunnyCustomer is a small CRUD application created as a PHP backend test assignment.

Features:

- create customer
- edit customer
- delete customer
- list customers

## Assignment text

1. Intention is to make PHP application which communicates to some relational database (MySQL, MariaDB etc.). Frontend can be made using any frontend development framework, but Vue.JS is preferred since we are using Vue.JS at work.
2. Please select your favourite database.
    - Create new database called CUSTOMER with attributes (ID, FirstName, Lastname, DateofBirth, Username, Password).
    - Add possibility to manipulate (add/modify/create) with customer data in user interface. There must be form in UI where you can add data; already added data must be displayed in UI in table form.
3. Add some unit tests if and when you will find them to be useful.
4. Add some validations to user interface before save operation and some warnings before performing delete operation. Choose the CSS according to your taste.
5. Add some documentation with your application, so that some other developer or tester is able to understand how to run/test/develop the application.

If you have some other technologies or skills to demonstrate (e.g. usage of authentication system), then it gives extra points.

## Architecture

The application follows a layered architecture:

```
HTTP
 ├── Router
 ├── Controllers
 ├── ExceptionHandler
 │
Service layer (business logic)
 │
Persistence layer
 ├── CustomerRepository PDO implementation
 │
MySQL / SQLite
```

Main principles:

- strict typing (`declare(strict_types=1)`);
- dependency injection;
- repository pattern;
- service layer;
- centralized exception handling;
- unit and integration tests.

## Requirements

- PHP 8.2+
- Composer
- MySQL 8 (tests can be run without it)

The following PHP extensions must be enabled:

- PDO MySQL
- PDO SQLite (used by integration tests)

Install dependencies before running the application:

```bash
composer install
```

## Installation

```bash
composer install
```

### Database

MySQL:
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

Edit

```
config/database.php
```

and configure:

- host
- database
- user
- password

Integration tests use SQLite and do not require MySQL.

The SQLite database is created automatically in

```
var/test.sqlite
```

## Running application

```bash
php -S localhost:8000 -t public
```

Open the application in browser:

```
http://localhost:8000
```

## HTTP API

### List customers

```http
GET /customers
```

Response:

```json
[
  {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "birth_date": "1990-01-01",
    "user_name": "jdoe"
  }
]
```

---

### Create customer

```http
POST /customers
```

Request body:

```json
{
  "first_name": "John",
  "last_name": "Doe",
  "birth_date": "1990-01-01",
  "user_name": "jdoe",
  "password": "secret"
}
```

Response:

```
201 Created
```

---

### Update customer

```http
PUT /customers/{id}
```

Request body:

```json
{
  "first_name": "Johnny",
  "last_name": "Doe",
  "birth_date": "1992-02-02",
  "user_name": "johnny"
}
```

Response:

```
204 No Content
```

---

### Delete customer

```http
DELETE /customers/{id}
```

Response:

```
204 No Content
```

## Tests

The project contains:

- unit tests for service layer;
- integration tests for repository layer;
- HTTP integration tests for controllers.

### Running tests

```bash
vendor/bin/phpunit
```


### HTTP request examples

Sample requests are available in

```
requests.http
```

The file can be executed directly from PhpStorm.

## Frontend

The frontend is implemented with:

- Vue 3
- Vite
- JavaScript

### Architecture

```
frontend/
├── src/
│   ├── components/
│   │   ├── CustomerTable.vue
│   │   └── CustomerForm.vue
│   └── services/
│       └── CustomerApi.js
└── vite.config.js
```

- CustomerTable — displays the list of customers and initiates Edit and Delete operations.
- CustomerForm — creation and editing of a customer, client-side validation.
- CustomerApi — HTTP communication with REST API;
- App.vue — components orchestration, holding of the application state.

### Running the frontend

From the `frontend/` directory:

```bash
npm install
npm run dev
```

The Vite development server starts the frontend application. The Vite development server is configured to proxy API requests to the PHP backend running at:

http://localhost:8000

Start the backend separately:

```bash
php -S localhost:8000 -t public
```

For full-fledged application work you will need both these servers and also running MySQL server with ```sunny``` database.

### Frontend functionality

The UI provides:

- customer list displayed in a table;
- customer creation;
- customer editing;
- customer deletion with confirmation;
- client-side form validation;
- error messages returned by the REST API;
- automatic table refresh after successful create, update or delete operations.

When editing an existing customer, the password field is not displayed and the password is not sent with the update request.

### Frontend verification

1. Start the PHP backend.
2. Start the Vite development server.
3. Open the application in a browser on http://localhost:5173.
4. Verify that existing customers are displayed.
5. Create a customer and verify that the new customer appears in the table.
6. Edit a customer and verify that the changes are displayed.
7. Try submitting the form with missing required fields.
8. Try creating a customer with an existing username.
9. Delete a customer and confirm the deletion dialog.
10. Verify that the customer disappears from the table.
