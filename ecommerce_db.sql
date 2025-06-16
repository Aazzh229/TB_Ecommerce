-- CREATE DATABASE
CREATE DATABASE IF NOT EXISTS ecommerce_db;
USE ecommerce_db;

-- TABLE: users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Pembeli', 'Penjual') NOT NULL
);

-- TABLE: categories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL
);

-- TABLE: products
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    description TEXT,
    price DECIMAL(15, 2),
    category_id INT,
    merk VARCHAR(50),
    image VARCHAR(255),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- TABLE: product_variants
CREATE TABLE product_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    variant_name VARCHAR(50),
    price DECIMAL(15,2),
    stock INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- TABLE: product_images
CREATE TABLE product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT,
    image_path VARCHAR(255),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- TABLE: cart_items
CREATE TABLE cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    variant_id INT,
    quantity INT,
    is_checked_out BOOLEAN DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (variant_id) REFERENCES product_variants(id)
);

-- TABLE: transactions
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(15, 2),
    transaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50),
    payment_method VARCHAR(50),
    shipping_address TEXT,
    courier VARCHAR(50)
);

-- TABLE: transaction_details
CREATE TABLE transaction_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT,
    variant_id INT,
    quantity INT,
    price DECIMAL(15, 2),
    subtotal DECIMAL(15, 2),
    FOREIGN KEY (transaction_id) REFERENCES transactions(id),
    FOREIGN KEY (variant_id) REFERENCES product_variants(id)
);

-- TABLE: transaction_history
CREATE TABLE transaction_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT,
    status VARCHAR(50),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id)
);

-- INSERT SAMPLE CATEGORIES
INSERT INTO categories (name) VALUES 
('SKINCARE'),
('HAIRCARE'),
('BODYCARE'),
('MAKEUP');

-- INSERT SAMPLE USER
INSERT INTO users (username, password, role) VALUES 
('Asyifa', '$2y$10$/mB2ZqgmL19tVIqVw32pMO4RKe6b2txsfmXeFwlVEPK.ec04u19xC', 'Penjual');

-- CREATE TRIGGER for transaction_history
DELIMITER $$

CREATE TRIGGER after_transaction_insert
AFTER INSERT ON transactions
FOR EACH ROW
BEGIN
    INSERT INTO transaction_history (
        transaction_id,
        status,
        updated_at
    ) VALUES (
        NEW.id,
        NEW.status,
        NOW()
    );
END$$

DELIMITER ;

ALTER TABLE products ADD COLUMN seller_id INT;

ALTER TABLE cart_items DROP COLUMN id;

ALTER TABLE cart_items DROP FOREIGN KEY cart_items_ibfk_2;

ALTER TABLE cart_items
ADD CONSTRAINT fk_cart_items_variant
FOREIGN KEY (variant_id) REFERENCES product_variants(id)
ON DELETE CASCADE;

ALTER TABLE transaction_details DROP FOREIGN KEY transaction_details_ibfk_2;

ALTER TABLE transaction_details
ADD CONSTRAINT fk_transaction_details_variant
FOREIGN KEY (variant_id) REFERENCES product_variants(id)
ON DELETE CASCADE;

ALTER TABLE cart_items
ADD CONSTRAINT fk_cart_user
FOREIGN KEY (user_id) REFERENCES users(id)
ON DELETE CASCADE;

ALTER TABLE cart_items
ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY FIRST;

insert into users(username, password)
values 
('salma', '$2y$10$TzrCxAZQ3HR6RkHKUcSwXOYCjyQhf7rtZN6h5kMO3zRBOUc5ZGEgm')



