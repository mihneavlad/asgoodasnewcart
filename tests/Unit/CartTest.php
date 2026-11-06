<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Entity\Cart;
use App\Entity\CartItem;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    public function testGetTotalReturnsZeroForEmptyCart(): void
    {
        $cart = new Cart();

        $this->assertSame(0.0, $cart->getTotal());
    }

    public function testGetTotalSumsItemPricesAndQuantities(): void
    {
        $cart = new Cart();
        $cart->addItem(new CartItem('AGAN0001', 'Product A', 2, 10.00));
        $cart->addItem(new CartItem('AGAN0002', 'Product B', 3, 5.00));

        $this->assertSame(35.0, $cart->getTotal());
    }

    public function testRemoveOnlyItemEmptiesCart(): void
    {
        $cart = new Cart();
        $item = new CartItem('AGAN0001', 'Product A', 1, 10.00);
        $cart->addItem($item);
        $cart->removeItem($item);

        $this->assertCount(0, $cart->getItems());
        $this->assertSame(0.0, $cart->getTotal());
    }

    public function testRemoveOneItemKeepsTheOther(): void
    {
        $cart = new Cart();
        $item1 = new CartItem('AGAN0001', 'Product A', 1, 10.00);
        $item2 = new CartItem('AGAN000', 'Product B', 2, 5.00);
        $cart->addItem($item1);
        $cart->addItem($item2);
        $cart->removeItem($item1);

        $this->assertCount(1, $cart->getItems());
        $this->assertSame(10.0, $cart->getTotal());
    }
}
