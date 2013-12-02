-- Table structure for "car prices" database.

CREATE TABLE countries (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE
) ENGINE = INNODB, CHARACTER SET = utf8, COLLATE = utf8_general_ci;

CREATE TABLE cities (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    country_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (country_id) REFERENCES countries(id),
    UNIQUE KEY (country_id, name)
) ENGINE = INNODB, CHARACTER SET = utf8, COLLATE = utf8_general_ci;

CREATE TABLE car_types (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE
) ENGINE = INNODB, CHARACTER SET = utf8, COLLATE = utf8_general_ci;

CREATE TABLE car_prices (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    city_id INT NOT NULL,
    car_type_id INT NOT NULL,
    price DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (city_id) REFERENCES cities(id),
    FOREIGN KEY (car_type_id) REFERENCES car_types(id)
) ENGINE = INNODB, CHARACTER SET = utf8, COLLATE = utf8_general_ci;
