<?php

namespace BlogBundle\Tests\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase as WebTestCase,
    FOS\RestBundle\Util\Codes;

use BlogBundle\Tests\Fixtures\Entity\LoadPostData;

class PostControllerTest extends WebTestCase
{
    public function testGet()
    {
        $this->client = static::createClient();
        $fixtures = array('BlogBundle\Tests\Fixtures\Entity\LoadPostData');
        $this->loadFixtures($fixtures);
        $post = array_pop(LoadPostData::$posts);
        $route =  $this->getUrl('api_v1_get_post', array('id' => $post->getId(), '_format' => 'json'));
        $this->client->request('GET', $route);
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
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
            '{"title":"foo","content":"bar"}'
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_CREATED, false);
    }

    public function testJsonPostBadParameters()
    {
        $this->client = static::createClient();
        $this->client->request(
            'POST',
            '/v1/posts.json',
            array(),
            array(),
            array('CONTENT_TYPE'  => 'application/json'),
            '{"foo":"bar"}'
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_BAD_REQUEST);
    }

    public function testJsonPutShouldModify()
    {
        $this->client = static::createClient();
        $fixtures = array('BlogBundle\Tests\Fixtures\Entity\LoadPostData');
        $this->loadFixtures($fixtures);
        $posts = LoadPostData::$posts;
        $post = array_pop($posts);
        $this->client->request(
            'GET',
            sprintf('/v1/posts/%d.json', $post->getId()),
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(Codes::HTTP_OK, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
        $this->client->request(
            'PUT',
            sprintf('/v1/posts/%d.json', $post->getId()),
            array(),
            array(),
            array('CONTENT_TYPE'  => 'application/json'),
            '{"title":"foobar","content":"foobar"}'
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_SEE_OTHER, false);
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Location',
                sprintf('http://localhost/v1/posts/%d.json', $post->getId())
            ),
            $this->client->getResponse()->headers
        );
    }

    public function testJsonPutShouldCreate()
    {
        $id = 0;
        $this->client = static::createClient();
        $this->client->request(
            'GET',
            sprintf('/v1/posts/%d.json', $id),
            array('ACCEPT' => 'application/json')
        );
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode(), $this->client->getResponse()->getContent());
        $this->client->request(
            'PUT',
            sprintf('/v1/posts/%d.json', $id),
            array(),
            array(),
            array('CONTENT_TYPE'  => 'application/json'),
            '{"title":"barfoo","content":"barfoo"}'
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_CREATED, false);
    }

    public function testJsonPutBadParameters()
    {
        $id = 0;
        $this->client = static::createClient();
        $this->client->request(
            'PUT',
            sprintf('/v1/posts/%d.json', $id),
            array(),
            array(),
            array('CONTENT_TYPE'  => 'application/json'),
            '{"bar":"foo"}'
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_BAD_REQUEST);
    }

    public function testJsonPatch()
    {
        $this->client = static::createClient();
        $fixtures = array('BlogBundle\Tests\Fixtures\Entity\LoadPostData');
        $this->loadFixtures($fixtures);
        $posts = LoadPostData::$posts;
        $post = array_pop($posts);
        $this->client->request(
            'PATCH',
            sprintf('/v1/posts/%d.json', $post->getId()),
            array(),
            array(),
            array('CONTENT_TYPE'  => 'application/json', 'ACCEPT' => 'application/json'),
            '{"content":"def"}'
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_SEE_OTHER, false);
        $this->assertTrue(
            $this->client->getResponse()->headers->contains(
                'Location',
                sprintf('http://localhost/v1/posts/%d.json', $post->getId())
            ),
            $this->client->getResponse()->headers
        );
    }

    public function testJsonPatchBadParameters()
    {
        $this->client = static::createClient();
        $fixtures = array('BlogBundle\Tests\Fixtures\Entity\LoadPostData');
        $this->loadFixtures($fixtures);
        $posts = LoadPostData::$posts;
        $post = array_pop($posts);
        $this->client->request(
            'PATCH',
            sprintf('/v1/posts/%d.json', $post->getId()),
            array(),
            array(),
            array('CONTENT_TYPE'  => 'application/json'),
            '{"foobar":"foobar"}'
        );
        $this->assertJsonResponse($this->client->getResponse(), Codes::HTTP_BAD_REQUEST);
    }

    protected function assertJsonResponse($response, $statusCode = Codes::HTTP_OK, $checkValidJson =  true, $contentType = 'application/json')
    {
        $this->assertEquals(
        $statusCode, $response->getStatusCode(),
            $response->getContent()
        );
        $this->assertTrue(
            $response->headers->contains('Content-Type', $contentType),
            $response->headers
        );
        if ($checkValidJson) {
            $decode = json_decode($response->getContent());
            $this->assertTrue(($decode != null && $decode != false),
                'is response valid json: [' . $response->getContent() . ']'
            );
        }
    }
}
