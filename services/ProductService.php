<?php

declare(strict_types=1);


require_once __DIR__.'/../models/Product.php';
require_once __DIR__.'/../repositories/ProductRepository.php';


class ProductService {

    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository) {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts() {
        return $this->productRepository->getAll();
    }

}
