-- Laboratorio de prueba de base de datos

-- Creamos una nueva base de datos
DROP DATABASE IF EXISTS issueflow_prueba;
CREATE DATABASE issueflow_prueba
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_0900_ai_ci;

-- Seleccionamos la base de datos creada
USE issueflow_prueba;

-- Creamos la tabla users
CREATE TABLE users (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT uq_users_email UNIQUE (email)
);

-- Creamos la tabla categories
CREATE TABLE categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(120) NOT NULL,
    PRIMARY KEY (id),
    CONSTRAINT uq_categories_name UNIQUE (name)
);

CREATE TABLE tickets (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    code CHAR(8) NOT NULL,
    title VARCHAR(180) NOT NULL,
    priority VARCHAR(20) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'open',
    user_id BIGINT UNSIGNED,
    category_id BIGINT UNSIGNED,
    PRIMARY KEY (id),
    CONSTRAINT uq_tickets_code UNIQUE (code),
    CONSTRAINT chk_tickets_priority CHECK (priority IN ('urgent', 'high', 'normal', 'low')),
    CONSTRAINT chk_tickets_status CHECK (status IN ('open', 'in_progress', 'closed')),
    CONSTRAINT fk_tickets_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT fk_tickets_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE SET NULL
);