CREATE DATABASE sunny;

CREATE USER 'sunny'@'localhost' IDENTIFIED BY 'StrongPassword';
GRANT ALL PRIVILEGES ON sunny.* TO 'sunny'@'localhost';
FLUSH PRIVILEGES;

CREATE TABLE customer (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name    VARCHAR(100) NOT NULL,
    last_name     VARCHAR(100) NOT NULL,
    birth_date    DATE         NOT NULL,
    user_name     VARCHAR(100) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uk_customer_user_name UNIQUE (user_name)
);
