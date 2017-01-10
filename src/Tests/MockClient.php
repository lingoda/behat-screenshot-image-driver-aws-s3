<?php

namespace Bex\Behat\ScreenshotExtension\Driver\Tests;

use Aws\MockHandler;
use Aws\Result;
use Aws\S3\S3Client;

/**
 * Class MockClient
 */
class MockClient extends S3Client
{
    /**
     * MockClient constructor.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        $handler = new MockHandler();
        $handler->append(new Result(['ObjectURL' => 'https://example.com/test.png']));

        $args['handler'] = $handler;

        parent::__construct($args);
    }
}
