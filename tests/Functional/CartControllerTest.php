<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerTest extends WebTestCase
{
    private $client;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);

        $schemaTool = new SchemaTool($this->em);
        $metadata = $this->em->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        $this->loadProduct();
    }

    private function loadProduct(): void
    {
        $product = new Product('AGAN0001', 'Test Product', 49.99);
        $this->em->persist($product);
        $this->em->flush();
    }

    private function createCart(): array
    {
        $this->client->request('POST', '/carts');
        $this->assertResponseStatusCodeSame(201);

        return \json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }

    private function addItem(int $cartId, string $sku, int $quantity): int
    {
        $this->client->request('POST', "/carts/{$cartId}/items", server: ['CONTENT_TYPE' => 'application/json'], content: \json_encode([
            'sku' => $sku,
            'quantity' => $quantity,
        ], JSON_THROW_ON_ERROR));
        $this->assertResponseStatusCodeSame(201);
        $item = \json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $item['id'];
    }

    public function testFullyFunctionalHappyPath(): void
    {
        // 1. Create a cart
        $this->client->request('POST', '/carts');
        $this->assertResponseStatusCodeSame(201);

        $cart = \json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $cartId = $cart['id'];

        $this->assertNotNull($cartId);
        $this->assertSame([], $cart['items']);
        $this->assertSame(0, $cart['total']);

        // 2. Add a cart item
        $this->client->request('POST', "/carts/{$cartId}/items", server: ['CONTENT_TYPE' => 'application/json'], content: \json_encode(['sku' => 'AGAN0001', 'quantity' => 2], JSON_THROW_ON_ERROR));
        $this->assertResponseStatusCodeSame(201);

        $item = \json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $itemId = $item['id'];

        $this->assertSame('AGAN0001', $item['sku']);
        $this->assertSame('Test Product', $item['name']);
        $this->assertSame(2, $item['quantity']);
        $this->assertSame(49.99, $item['price']);

        // 3. View the cart
        $this->client->request('GET', "/carts/{$cartId}");
        $this->assertResponseStatusCodeSame(200);

        $cart = \json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertCount(1, $cart['items']);
        $this->assertSame(99.98, $cart['total']);

        // 4. Update cart item quantity
        $this->client->request('PATCH', "/carts/{$cartId}/items/{$itemId}", server: ['CONTENT_TYPE' => 'application/json'], content: \json_encode([
            'quantity' => 5,
        ], JSON_THROW_ON_ERROR));
        $this->assertResponseStatusCodeSame(200);

        $item = \json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(5, $item['quantity']);

        // 5. Delete the cart item
        $this->client->request('DELETE', "/carts/{$cartId}/items/{$itemId}");
        $this->assertResponseStatusCodeSame(204);

        // 6. Verify cart is empty
        $this->client->request('GET', "/carts/{$cartId}");
        $cart = \json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame([], $cart['items']);
        $this->assertSame(0, $cart['total']);
    }

    public function testGetNonExistentCartIdReturns404(): void
    {
        $this->client->request('GET', '/carts/1947');

        $this->assertResponseStatusCodeSame(404);
    }

    public function testAddItemWithUnknownSkuReturns404(): void
    {
        $cartId = $this->createCart()['id'];

        $this->client->request('POST', "/carts/{$cartId}/items", server: ['CONTENT_TYPE' => 'application/json'], content: \json_encode([
            'sku' => 'UNKNOWN-SKU',
            'quantity' => 1,
        ], JSON_THROW_ON_ERROR));

        $this->assertResponseStatusCodeSame(404);
    }

    public function testAddItemWithInvalidDataReturns422(): void
    {
        $cartId = $this->createCart()['id'];

        $this->client->request('POST', "/carts/{$cartId}/items", server: ['CONTENT_TYPE' => 'application/json'], content: \json_encode([
            'sku' => '',
            'quantity' => -1,
        ], JSON_THROW_ON_ERROR));

        $this->assertResponseStatusCodeSame(422);
    }

    public function testAddSameSkuTwiceIncrementsQuantity(): void
    {
        $cartId = $this->createCart()['id'];

        $this->addItem($cartId, 'AGAN0001', 2);
        $this->addItem($cartId, 'AGAN0001', 3);

        $this->client->request('GET', "/carts/{$cartId}");
        $cart = \json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->assertCount(1, $cart['items']);
        $this->assertSame(5, $cart['items'][0]['quantity']);
    }

    public function testUpdateItemWithInvalidQuantityReturns422(): void
    {
        $cartId = $this->createCart()['id'];
        $itemId = $this->addItem($cartId, 'AGAN0001', 1);

        $this->client->request('PATCH', "/carts/{$cartId}/items/{$itemId}", server: ['CONTENT_TYPE' => 'application/json'], content: \json_encode([
            'quantity' => 0,
        ], JSON_THROW_ON_ERROR));

        $this->assertResponseStatusCodeSame(422);
    }
}
