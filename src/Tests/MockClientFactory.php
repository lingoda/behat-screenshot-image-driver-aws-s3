<?php

namespace Bex\Behat\ScreenshotExtension\Driver\Tests;

use Aws\MockHandler;
use Aws\Result;
use Aws\S3\S3Client;

/**
 * Class MockClientFactory
 */
class MockClientFactory
{
    /**
     * @param array $args
     *
     * @return S3Client
     */
    public static function getClient($args)
    {
        $handler = new MockHandler();
        $handler->append(new Result(['ObjectURL' => 'https://example.com/test.png']));

        $args['handler'] = $handler;

        return new S3Client($args);
    }
}
