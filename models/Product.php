<?php

declare(strict_types=1);


class Product {
    private int $id;
    private string $name;
    private float $price;
    private string $description;
    private string $imageUrl;
    private float $stars; 

    public function __construct(int $id, string $name, float $price, string $description, string $imageUrl, float $stars) {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->imageUrl = $imageUrl;
        $this->stars = $stars;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function getPrice() {
        return $this->price;
    }

    public function getDescription() {
        return $this->description;
    }
    public function getImageUrl() {
        return $this->imageUrl;
    }

    public function getStars() {
        return $this->stars;
    }
}
