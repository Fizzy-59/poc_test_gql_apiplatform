<?php


namespace App\Tests;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Product;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ProductsTest extends ApiTestCase
{
    // This trait provided by HautelookAliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

    public function testGetCollection(): void
    {
        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClient()->request('POST', '/api/graphql',
            [
                'headers' => ['Content-Type: application/json'],
                'body' =>  json_encode(['query' =>
                    "query Products {
                      products {
                        edges {
                          node {
                            _id
                            name
                            price
                          }
                        }
                      }
                    }"])
            ]);

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/json');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            'data' => [
                'products' => [
                    'edges' => [
                        1 => [
                            'node' => [
                                '_id' => 2
                            ]
                        ]
                    ]
                ]
            ]

        ]);

        // Because test fixtures are automatically loaded between each test, you can assert on them
        $this->assertCount(30, $response->toArray()['data']['products']['edges']);

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
        // $this->assertMatchesResourceCollectionJsonSchema(Product::class);
    }

}