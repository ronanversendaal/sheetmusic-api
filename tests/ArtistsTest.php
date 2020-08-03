<?php

declare(strict_types=1);

namespace App\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Artist;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class ArtistsTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    public function testGetCollection(): void
    {
        $response = static::createClient()->request('GET', '/api/artists');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Artist',
            '@id' => '/api/artists',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/artists?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/artists?page=1',
                'hydra:last' => '/api/artists?page=4',
                'hydra:next' => '/api/artists?page=2',
            ],
        ]);

        $this->assertCount(30, $response->toArray()['hydra:member']);

        $this->assertMatchesResourceCollectionJsonSchema(Artist::class);
    }

    public function testCreateArtist(): void
    {
        $response = static::createClient()->request('POST', '/api/artists', ['json' => [
            'name' => 'SiR',
            'dateOfBirth' => '1986-11-05',
            'origin' => 'USA, California',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Artist',
            '@type' => 'Artist',
            'name' => 'SiR',
            'dateOfBirth' => '1986-11-05T00:00:00+00:00',
            'origin' => 'USA, California',
        ]);
        $this->assertRegExp('~^/api/artists/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Artist::class);
    }

    public function testCreateInvalidArtist(): void
    {
        static::createClient()->request('POST', '/api/artists', ['json' => [
            'name' => '', // Empty title
            'dateOfBirth' => '1970-01-01',
            'origin' => 'nowhere',
        ]]);

        $this->assertResponseStatusCodeSame(400);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'name: This value should not be blank.',
        ]);
    }

    public function testUpdateArtist(): void
    {
        $client = static::createClient();

        $iri = $this->findIriBy(Artist::class, ['id' => '50']);

        $client->request('PUT', $iri, ['json' => [
            'name' => 'SiR',
        ]]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'SiR',
        ]);
    }

    public function testDeleteArtist(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(Artist::class, ['id' => '50']);

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
        // Through the container, you can access all your services from the tests, including the ORM, the mailer, remote API clients...
            static::$container->get('doctrine')->getRepository(Artist::class)->findOneBy(['id' => '50'])
        );
    }
}
