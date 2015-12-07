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
        $this->repo = static::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:Registration');
    }

    public function testGetRegistration()
    {
        $client = static::createClient();
        $client->request('GET', '/api/registrations/1');

        $this->jsonContentType($client);
        $this->assertCode($client, 200);

        $this->assertContains('Let\'s be class', $client->getResponse()->getContent(), "Get Registration 1 : Content not equal");
    }

    public function testGetRegistration404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/registrations/errorId');

        $this->jsonContentType($client);

        $this->assertCode($client, 404);
    }

    public function testGetUserRegistrations()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/Admin/registrations');

        $this->jsonContentType($client);
        $this->assertCode($client, 200);

        $this->assertContains('Let\'s celebrate Oktoberfest', $client->getResponse()->getContent(), "Get Registration for Admin : Content not equal");
        $this->assertContains('Let\'s be class', $client->getResponse()->getContent(), "Get Registration for Admin : Content not equal");
        $this->assertContains('Speed dating', $client->getResponse()->getContent(), "Get Registration for Admin : Content not equal");
    }

    public function testGetUserRegistrations404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/errorUsername/registrations');

        $this->jsonContentType($client);

        $this->assertCode($client, 404);
    }

    public function testNewRegistration()
    {
        $client = static::createClient();
        $client->request('GET', '/api/registrations/new');

        $this->jsonContentType($client);

        $this->assertCode($client, 200);
    }

    public function testPostUserRegistrationsAR()
    {
        $client = static::createClient();
        $client->request('POST',
            'api/users/registrations/',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"rest_event_registration":{"username":"Admin","eventId":"9"}}');

        $this->jsonContentType($client);

        $this->assertCode($client, 400);
        $this->assertEquals('You are already registered to this event', $client->getResponse()->getContent(), "Post Registration for Admin already registered: Content not equal");
    }

    public function testPostUserRegistrationsAG()
    {
        $client = static::createClient();
        $client->request('POST',
            'api/users/registrations/',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"rest_event_registration":{"username":"Admin","eventId":"4"}}');

        $this->jsonContentType($client);

        $this->assertCode($client, 400);
        $this->assertEquals("You are not in the age range of this Event. The age range is: 25 - 45 and you are 22", $client->getResponse()->getContent(), "Post Registration for Admin age range: Content not equal");
    }

    public function testDeleteUserRegistrations404()
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/registrations/errorId');

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
