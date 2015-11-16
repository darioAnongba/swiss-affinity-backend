<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use JMS\Serializer\SerializerBuilder as JMSBuilder;

class LocationControllerTest extends WebTestCase
{
    private $repo;
    private $serializer;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel -> boot();
        $this->repo = static::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:Location');

        $this->serializer = JMSBuilder::create()->build();
    }

    public function testGetLocations()
    {
        $locations = $this->repo->findBy(array(), array('name' => 'ASC'));
        $client = static::createClient();
        $client->request('GET', '/api/locations');

        $this->jsonContentType($client);
        $this->assertCode($client, 200);

        $locations = $this->serializer->serialize($locations, 'json');
        $this->assertEquals($locations, $client->getResponse()->getContent(), "Get Locations : Content not equal");
    }

    public function testGetLocationsEvents()
    {
        $location = $this->repo->find(3);

        $events = static::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:Event')
            ->findBy(array('location' => $location, "state" => "Pending"), array('dateStart' => 'DESC'));

        $client = static::createClient();
        $client->request('GET', '/api/locations/3/events');

        $this->jsonContentType($client);
        $this->assertCode($client, 200);

        $this->assertContains('Xmas Dating', $client->getResponse()->getContent(), "Get Events : Content not equal");
        $this->assertContains('Let\'s diner Meeting', $client->getResponse()->getContent(), "Get Events : Content not equal");
        $this->assertContains('First Speed Dating Event !', $client->getResponse()->getContent(), "Get Events : Content not equal");
        $this->assertContains('Speed dating', $client->getResponse()->getContent(), "Get Events : Content not equal");
    }

    public function testGetLocationEvents404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/locations/errorId/events');

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
