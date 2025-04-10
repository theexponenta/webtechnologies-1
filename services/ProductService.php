<?php

declare(strict_types=1);


require_once __DIR__.'/../models/Product.php';


class ProductService {

    public function getAllProducts() {
        return [
            new Product(1, "Дилдо резиновое", 100, "Lalal", "/public/img/img_placeholder.webp", 4.7),
            new Product(2, "БДСМ набор", 1000, "Наслаждайтесь", "/public/img/img_placeholder.webp", 4.9),
            new Product(3, "Ещё что-то", 666, "Стараюсь быть оригинальным", "/public/img/img_placeholder.webp", 3.33)
        ];
    }

}
