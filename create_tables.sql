CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(64) NOT NULL,
    last_name VARCHAR(64) NOT NULL,
    email VARCHAR(128) NOT NULL,
    password VARCHAR(128) NOT NULL,
    salt VARCHAR(64) NOT NULL,
    token VARCHAR(128) NOT NULL,
    register_time DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL,
    last_login DATETIME DEFAULT NULL,
    is_verified BOOLEAN DEFAULT(TRUE) NOT NULL
);

CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(128) NOT NULL,
    price DECIMAL(20, 2) NOT NULL,
    description TEXT DEFAULT NULL,
    image_url VARCHAR(256) NOT NULL,
    stars DECIMAL(2, 1) NOT NULL
);

CREATE TABLE IF NOT EXISTS cart (
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);


CREATE INDEX idx_cart_user_id ON cart (user_id);
CREATE INDEX idx_cart_product_id ON cart (product_id);
