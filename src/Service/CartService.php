<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;

class CartService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CartItemRepository $cartItemRepository,
    ) {
    }

    public function createCart(): Cart
    {
        $cart = new Cart();

        $this->em->persist($cart);
        $this->em->flush();

        return $cart;
    }

    public function addItem(Cart $cart, string $sku, string $name, int $quantity, float $price): CartItem
    {
        $existingItem = $this->cartItemRepository->findByCartAndSku($cart, $sku);

        if ($existingItem !== null) {
            $existingItem->setQuantity($existingItem->getQuantity() + $quantity);
            $this->em->flush();

            return $existingItem;
        }

        $item = new CartItem($sku, $name, $quantity, $price);
        $cart->addItem($item);

        $this->em->flush();

        return $item;
    }

    public function updateItem(CartItem $item, int $quantity): CartItem
    {
        $item->setQuantity($quantity);

        $this->em->flush();

        return $item;
    }

    public function removeItem(CartItem $item): void
    {
        $cart = $item->getCart();
        $cart->removeItem($item);

        $this->em->flush();
    }
}
