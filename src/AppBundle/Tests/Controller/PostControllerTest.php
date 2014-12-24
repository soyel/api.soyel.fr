<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase as WebTestCase;

use AppBundle\Tests\Fixtures\Entity\LoadPostData;

class PostControllerTest extends WebTestCase
{
    /**
     * @var Symfony\Bundle\FrameworkBundle\Client $client
     */
    protected $client;

    public function testGet()
    {
        $this->client = static::createClient();
        $fixtures = array('AppBundle\Tests\Fixtures\Entity\LoadPostData');
        $this->loadFixtures($fixtures);
        $post = array_pop(LoadPostData::$posts);
        $route =  $this->getUrl('api_v1_get_post', array('post' => $post->getId(), '_format' => 'json'));
        $client->request('GET', $route);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, 200);
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['id']));
        $this->assertTrue(isset($decoded['title']));
        $this->assertTrue(isset($decoded['content']));
    }

    public function testJsonPost()
    {
        $this->client = static::createClient();
        $this->client->request(
            'POST',
            '/v1/posts.json',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"title":"title1","content":"content1"}'
        );
        $this->assertJsonResponse($this->client->getResponse(), 201, false);
    }

    public function testJsonPostShouldReturn400WithBadParameters()
    {
        $this->client = static::createClient();
        $this->client->request(
            'POST',
            '/api/v1/pages.json',
            array(),
            array(),
            array('CONTENT_TYPE' => 'application/json'),
            '{"foo":"bar"}'
        );
        $this->assertJsonResponse($this->client->getResponse(), 400, false);
    }

    protected function assertJsonResponse($response, $statusCode = 200)
    {
        $this->assertEquals(
            $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', 'application/json'),
            $response->headers
        );
    }
}
