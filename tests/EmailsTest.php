<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmailsTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $client->enableProfiler();
        $crawler = $client->request('GET', '/mail');

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertSame(1, $mailCollector->getMessageCount());
        $messagesCollector = $mailCollector->getMessages();
        $message = $messagesCollector[0];
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame('send@example.com', key($message->getFrom()));
        $this->assertSame('recipient@example.com', key($message->getTo()));
        $this->assertStringContainsString('You did it! You registered!', $message->getBody());
    }
}
