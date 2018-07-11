<?php

namespace Trikoder\JsonApiBundle\Tests\Functional\Controller\Demo;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Trikoder\JsonApiBundle\Tests\Functional\JsonapiWebTestCase;

/**
 * Class SimpleFileUploadControllerTest
 */
class SimpleFileUploadControllerTest extends JsonapiWebTestCase
{
    public function testCreateUpload()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/api/simple-file-upload',
            [],
            ['simpleFileBinary' => $this->getTestFile()],
            [],
            json_encode([
                'data' => [
                    'type' => 'simple-file',
                    'attributes' => [
                        'title' => 'simple file test name',
                    ],
                ],
            ])
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'id' => 'README.md',
            'type' => 'simple-file',
            'attributes' => [
                'name' => 'README',
                'title' => 'simple file test name',
                'extension' => 'md',
            ],
            'links' => [
                'self' => '/simple-file/README.md', // TODO - this should same url sa request?
            ],
        ], $data['data']);
    }

    public function testUpdateUpload()
    {
        $client = static::createClient();

        $client->request(
            'PUT',
            '/api/simple-file-upload/test',
            [],
            ['simpleFileBinary' => $this->getTestFile()],
            [],
            json_encode([
                'data' => [
                    'type' => 'simple-file',
                    'attributes' => [
                        'title' => 'simple file test name',
                    ],
                ],
            ])
        );

        $response = $client->getResponse();

        $this->assertIsJsonapiResponse($response);

        $data = $this->getResponseContentJson($response);

        $this->assertEquals([
            'id' => 'README.md',
            'type' => 'simple-file',
            'attributes' => [
                'name' => 'README',
                'title' => 'simple file test name',
                'extension' => 'md',
            ],
            'links' => [
                'self' => '/simple-file/README.md', // TODO - this should same url sa request?
            ],
        ], $data['data']);
    }

    /**
     * @return UploadedFile
     */
    private function getTestFile()
    {
        return new UploadedFile(
            'README.md',
            'README.md',
            'application/octe',
            123
        );
    }
}
