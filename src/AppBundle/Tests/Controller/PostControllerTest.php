<?php

namespace AppBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase as WebTestCase;

use AppBundle\Tests\Fixtures\Entity\LoadPostData;

class PostControllerTest extends WebTestCase
{
    public function testGet()
    {
        $client = static::createClient();
        $fixtures = array('AppBundle\Tests\Fixtures\Entity\LoadPostData');
        $this->loadFixtures($fixtures);
        $post = array_pop(LoadPostData::$posts);
        $route =  $this->getUrl('api_v1_get_post', array('post' => $post->getId(), '_format' => 'json'));
        $client->request('GET', $route);
        $response = $client->getResponse();
        $this->assertJsonResponse($response, 200);
        $content = $response->getContent();
        $decoded = json_decode($content, true);
        $this->assertTrue(isset($decoded['id']));
        $this->assertTrue(isset($decoded['title']));
        $this->assertTrue(isset($decoded['content']));
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
