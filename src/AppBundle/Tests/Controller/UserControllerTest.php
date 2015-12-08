<?php

namespace AppBundle\Tests\Controller;

use FOS\UserBundle\Propel\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use JMS\Serializer\SerializerBuilder as JMSBuilder;

class UserControllerTest extends WebTestCase
{
    private $repo;
    private $serializer;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel -> boot();
        $this->repo = static::$kernel->getContainer()->get('doctrine')->getRepository('AppBundle:User');

        $this->serializer = JMSBuilder::create()->build();
    }

    public function testGetUsers()
    {
        $users = $this->repo->findAll();

        $client = static::createClient();
        $client->request('GET', '/api/users');

        $this->jsonContentType($client);
        $this->assertCode($client, 200);

        $users = $this->serializer->serialize($users, 'json');

        $this->assertContains('"username":"Admin"', $client->getResponse()->getContent(), "Get Users : Content not equal");
        $this->assertContains('"username":"JoelDan192"', $client->getResponse()->getContent(), "Get Users : Content not equal");
        $this->assertContains('"username":"maxpremi"', $client->getResponse()->getContent(), "Get Users : Content not equal");
        $this->assertContains('"username":"YanetGarcia"', $client->getResponse()->getContent(), "Get Users : Content not equal");
        $this->assertContains('"username":"AmandaCerny"', $client->getResponse()->getContent(), "Get Users : Content not equal");
        $this->assertContains('"username":"JuliaStegner"', $client->getResponse()->getContent(), "Get Users : Content not equal");
        $this->assertContains('"username":"ChristelleBurrus"', $client->getResponse()->getContent(), "Get Users : Content not equal");

    }

    public function testGetUser()
    {
        $user = $this->repo->findOneBy(array('facebookId' => 1271175799));

        $client = static::createClient();
        $client->request('GET', '/api/users/1271175799');

        $this->jsonContentType($client);
        $this->assertCode($client, 200);

        $user = $this->serializer->serialize($user, 'json');

        $this->assertContains('"username":"Admin"', $client->getResponse()->getContent(), "Get User : Content not equal");
    }

    public function testGetUser404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/errorId');

        $this->jsonContentType($client);
        $this->assertCode($client, 404);
    }

    public function testDeleteUser()
    {
        //TODO
    }

    public function testDeleteUser404()
    {
        $client = static::createClient();
        $client->request('DELETE', '/api/users/errorUsername');

        $this->jsonContentType($client);
        $this->assertCode($client, 404);
    }

    public function testGetUserLocations()
    {
        $user = $this->repo->find(1);

        $client = static::createClient();
        $client->request('GET', '/api/users/Admin/locations');

        $this->jsonContentType($client);
        $this->assertCode($client, 200);

        $locations = $this->serializer->serialize($user->getLocationsOfInterest(), 'json');

        $this->assertContains($locations, $client->getResponse()->getContent(), "Get User locations : Content not equal");
    }

    public function testGetUserLocations404()
    {
        $client = static::createClient();
        $client->request('GET', '/api/users/errorUsername/locations');

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
