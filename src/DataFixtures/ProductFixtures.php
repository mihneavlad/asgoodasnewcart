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
            ['sku' => 'AGAN0001', 'name' => 'Smartphone Pro 15', 'price' => 999.99],
            ['sku' => 'AGAN0002', 'name' => 'Smartphone Lite 15', 'price' => 599.99],
            ['sku' => 'AGAN0003', 'name' => 'Ultrabook 14" 16GB', 'price' => 1299.99],
            ['sku' => 'AGAN0004', 'name' => 'Gaming Laptop 15" RTX', 'price' => 1799.99],
            ['sku' => 'AGAN0005', 'name' => 'Wireless Noise-Cancelling Headphones', 'price' => 249.99],
            ['sku' => 'AGAN0006', 'name' => 'True Wireless Earbuds', 'price' => 129.99],
            ['sku' => 'AGAN0007', 'name' => 'Smartwatch Series 9', 'price' => 399.99],
            ['sku' => 'AGAN0008', 'name' => 'Tablet 11" 256GB', 'price' => 749.99],
        ];

        foreach ($products as $data) {
            $product = new Product($data['sku'], $data['name'], $data['price']);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
