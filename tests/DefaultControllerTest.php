<?php

namespace App\Tests;

use App\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();

        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

        $this->entityManager->beginTransaction();
        $this->entityManager->getConnection()->setAutoCommit(false);
    }

    protected function tearDown(): void
    {
        $this->entityManager->rollback();
        $this->entityManager->close();
        $this->entityManager = null;
    }


//    public function testSomething(): void
//    {
//        $client = static::createClient();
//        $crawler = $client->request('GET', '/home');
//
//        $this->assertSelectorTextContains('h1', 'Hello');
//        $this->assertGreaterThan(
//            0,
//            $crawler->filter('html:contains("Hello")')->count()
//        );
//        // $this->assertGreaterThan(0, $crawler->filter('h1.class')->count());
////        $this->assertCount(1, $crawler->filter('h1'));
////        $this->assertTrue(
////            $client->getResponse()->headers->contains(
////                'Content-Type',
////                'application/json'
////            ),
////            'the "Content-Type" header is "application/json"' // optional message shown on failure
////        );
////        $this->assertContains('foo', $client->getResponse()->getContent());
////        $this->assertRegExp('/foo(bar)?/', $client->getResponse()->getContent());
////        $this->assertTrue($client->getResponse()->isSuccessful(), 'response status is 2xx');
////        $this->assertTrue($client->getResponse()->isNotFound());
////        $this->assertEquals(
////            200, // or Symfony\Component\HttpFoundation\Response::HTTP_OK
////            $client->getResponse()->getStatusCode()
////        );
////        $this->assertTrue(
////            $client->getResponse()->isRedirect('/demo/contact')
////        // if the redirection URL was generated as an absolute URL
////        // $client->getResponse()->isRedirect('http://localhost/demo/contact')
////        );
////        $this->assertTrue($client->getResponse()->isRedirect());
//    }
//
//    public function testClickLink(): void
//    {
//        $client = static::createClient();
//        $crawler = $client->request('GET', '/home');
//
//        $link = $crawler->filter('a:contains("Login")')->link();
//        $crawler = $client->click($link);
//
//        $this->assertStringContainsString('Remember me', $client->getResponse()->getContent());
//    }
//
//    public function testSendAForm(): void
//    {
//        $client = static::createClient();
//        $crawler = $client->request('GET', '/home');
//
//        $form = $crawler->selectButton('Sign in')->form();
//        $form['email'] = 'a@a';
//        $form['password'] = '1';
//
//        $crawler = $client->submit($form);
//        $crawler = $client->followRedirect();
//
//        $this->assertEquals(1, $crawler->filter('a:contains("logout")')->count());
//    }
//
//    /**
//     * @dataProvider provideUrls
//     */
//    public function testDataProvider($url): void
//    {
//        $client = static::createClient();
//        $crawler = $client->request('GET', $url);
//        $this->assertTrue($client->getResponse()->isSuccessful());
//    }
//
//    public function provideUrls()
//    {
//        return [
//            ['/home'],
//            ['/login']
//        ];
//    }

    public function testInIsolation()
    {
        $crawler = $this->client->request('GET', '/home');
        $this->assertTrue($this->client->getResponse()->isSuccessful());

        $video = $this->entityManager
            ->getRepository(Video::class)
            ->find(7);

        $video = $this->entityManager->merge($video);
        $this->entityManager->remove($video);
        $this->entityManager->flush();

        $this->assertNull($this->entityManager
            ->getRepository(Video::class)
            ->find(7));
    }
}
