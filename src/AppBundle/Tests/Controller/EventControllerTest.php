<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
    private $repo;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel -> boot();
        $this->repo = static::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:Event');
    }

    public function testGetEvents()
    {
        $client = static::createClient();
        $client->request('GET', '/api/events');

        $this->jsonContentType($client);
        $this->assertCode($client, 200);

        $this->assertContains('Xmas Dating', $client->getResponse()->getContent(), "Get Events : Content not equal");
        $this->assertContains('Let\'s celebrate Oktoberfest', $client->getResponse()->getContent(), "Get Events : Content not equal");
        $this->assertContains('Let\'s be class', $client->getResponse()->getContent(), "Get Events : Content not equal");
        $this->assertContains('Let\'s diner Meeting', $client->getResponse()->getContent(), "Get Events : Content not equal");
        $this->assertContains('First Speed Dating Event !', $client->getResponse()->getContent(), "Get Events : Content not equal");
        $this->assertContains('Speed dating', $client->getResponse()->getContent(), "Get Events : Content not equal");
    }

    public function testGetEvent()
    {
        $event = $this->repo->find(8);
        $client = static::createClient();
        $client->request('GET', '/api/events/8');

        $this->jsonContentType($client);
        $this->assertCode($client, 200);

        $this->assertContains('Let\'s diner Meeting', $client->getResponse()->getContent(), "Get Event 8 : Content not equal");
    }

    public function testGetEvent404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/events/errorId');

        $this->jsonContentType($client);

        $this->assertCode($client, 404);
    }

    private function jsonContentType($client) {
        $this->assertTrue($client->getResponse()->headers->contains(
            'Content-Type',
            'application/json'));
    }

    private function assertCode($client, $code) {
        $this->assertEquals($code, $client->getResponse()->getStatusCode());
    }
}
