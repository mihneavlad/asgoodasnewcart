<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\AddItemRequest;
use App\DTO\UpdateItemRequest;
use App\Entity\Cart;
use App\Entity\CartItem;
use App\Repository\CartItemRepository;
use App\Repository\CartRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/carts', name: 'cart_')]
class CartController extends AbstractController
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly CartRepository $cartRepository,
        private readonly CartItemRepository $cartItemRepository,
    ) {
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(): JsonResponse
    {
        $cart = $this->cartService->createCart();

        return $this->json($this->serializeCart($cart), Response::HTTP_CREATED);
    }

    #[Route('/{cartId}', name: 'show', methods: ['GET'])]
    public function show(int $cartId): JsonResponse
    {
        $cart = $this->cartRepository->find($cartId);

        if ($cart === null) {
            throw $this->createNotFoundException('Cart not found.');
        }

        return $this->json($this->serializeCart($cart));
    }

    #[Route('/{cartId}/items', name: 'add_item', methods: ['POST'])]
    public function addItem(
        int $cartId,
        #[MapRequestPayload] AddItemRequest $request,
    ): JsonResponse {
        $cart = $this->cartRepository->find($cartId);

        if ($cart === null) {
            throw $this->createNotFoundException('Cart not found.');
        }

        try {
            $item = $this->cartService->addItem($cart, $request->sku, $request->quantity);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }

        return $this->json($this->serializeItem($item), Response::HTTP_CREATED);
    }

    #[Route('/{cartId}/items/{itemId}', name: 'update_item', methods: ['PATCH'])]
    public function updateItem(
        int $cartId,
        int $itemId,
        #[MapRequestPayload] UpdateItemRequest $request,
    ): JsonResponse {
        $cart = $this->cartRepository->find($cartId);

        if ($cart === null) {
            throw $this->createNotFoundException('Cart not found.');
        }

        $item = $this->cartItemRepository->find($itemId);

        if ($item === null || $item->getCart()->getId() !== $cartId) {
            throw $this->createNotFoundException('Product not found.');
        }

        $item = $this->cartService->updateItem($item, $request->quantity);

        return $this->json($this->serializeItem($item));
    }

    #[Route('/{cartId}/items/{itemId}', name: 'delete_item', methods: ['DELETE'])]
    public function deleteItem(int $cartId, int $itemId): JsonResponse
    {
        $cart = $this->cartRepository->find($cartId);

        if ($cart === null) {
            throw $this->createNotFoundException('Cart not found.');
        }

        $item = $this->cartItemRepository->find($itemId);

        if ($item === null || $item->getCart()->getId() !== $cartId) {
            throw $this->createNotFoundException('Product not found.');
        }

        $this->cartService->removeItem($item);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    private function serializeCart(Cart $cart): array
    {
        $items = [];

        foreach ($cart->getItems() as $item) {
            $items[] = $this->serializeItem($item);
        }

        return [
            'id' => $cart->getId(),
            'items' => $items,
            'total' => round($cart->getTotal(), 2),
            'createdAt' => $cart->getCreatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }

    private function serializeItem(CartItem $item): array
    {
        return [
            'id' => $item->getId(),
            'sku' => $item->getSku(),
            'name' => $item->getName(),
            'quantity' => $item->getQuantity(),
            'price' => $item->getPrice(),
            'createdAt' => $item->getCreatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
