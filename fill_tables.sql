
INSERT INTO users (first_name, last_name, email, password, salt, token)
VALUES ('Loh', 'Loh', 'loh@gmail.com', 'loshara123', 'mephedrone', 'sometoken');

INSERT INTO products (name, price, description, image_url, stars)
VALUES ('Дилдо резиновое', 100, 'Lalal', '/public/img/img_placeholder.webp', 4.7),
('БДСМ набор', 1000, 'Наслаждайтесь', '/public/img/img_placeholder.webp', 4.9),
('Ещё что-то', 1000, 'Стараюсь быть оригинальным', '/public/img/img_placeholder.webp', 3.33);

INSERT INTO cart (user_id, product_id) VALUES (1, 1), (1, 3);
