<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            ['sku' => 'PHONE-001', 'name' => 'Smartphone Pro 15', 'price' => 999.99],
            ['sku' => 'PHONE-002', 'name' => 'Smartphone Lite 15', 'price' => 599.99],
            ['sku' => 'LAPTOP-001', 'name' => 'Ultrabook 14" 16GB', 'price' => 1299.99],
            ['sku' => 'LAPTOP-002', 'name' => 'Gaming Laptop 15" RTX', 'price' => 1799.99],
            ['sku' => 'HEADP-001', 'name' => 'Wireless Noise-Cancelling Headphones', 'price' => 249.99],
            ['sku' => 'HEADP-002', 'name' => 'True Wireless Earbuds', 'price' => 129.99],
            ['sku' => 'WATCH-001', 'name' => 'Smartwatch Series 9', 'price' => 399.99],
            ['sku' => 'TABLET-001', 'name' => 'Tablet 11" 256GB', 'price' => 749.99],
        ];

        foreach ($products as $data) {
            $product = new Product($data['sku'], $data['name'], $data['price']);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
