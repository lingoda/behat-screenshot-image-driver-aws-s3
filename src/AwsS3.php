<?php

namespace Bex\Behat\ScreenshotExtension\Driver;

use Aws\S3\S3Client;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AwsS3 implements ImageDriverInterface
{
    const CONFIG_PARAM_BUCKET = 'bucket';
    const CONFIG_PARAM_VERSION = 'version';
    const CONFIG_PARAM_REGION = 'region';
    const CONFIG_PARAM_CREDENTIALS_KEY = 'credentials_key';
    const CONFIG_PARAM_CREDENTIALS_SECRET = 'credentials_secret';
    const CONFIG_PARAM_CREDENTIALS_TOKEN = 'credentials_token';
    const CONFIG_PARAM_CLIENT_FACTORY = 'client_factory';
    /**
     * @var S3Client
     */
    private $api;
    /**
     * @var string
     */
    private $bucket;

    /**
     * @param  ArrayNodeDefinition $builder
     */
    public function configure(ArrayNodeDefinition $builder)
    {
        $builder
            ->children()
                ->scalarNode(self::CONFIG_PARAM_BUCKET)->isRequired()->end()
                ->scalarNode(self::CONFIG_PARAM_VERSION)->defaultValue('latest')->end()
                ->scalarNode(self::CONFIG_PARAM_REGION)->isRequired()->end()
                ->scalarNode(self::CONFIG_PARAM_CREDENTIALS_KEY)->end()
                ->scalarNode(self::CONFIG_PARAM_CREDENTIALS_SECRET)->end()
                ->scalarNode(self::CONFIG_PARAM_CREDENTIALS_TOKEN)->end()
                ->scalarNode(self::CONFIG_PARAM_CLIENT_FACTORY)->defaultNull()->end()
            ->end();
    }

    /**
     * @param  ContainerBuilder $container
     * @param  array $config
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->bucket = $config[self::CONFIG_PARAM_BUCKET];

        $version = $config[self::CONFIG_PARAM_VERSION];
        $region = $config[self::CONFIG_PARAM_REGION];
        $credentials = null;
        if ($config[self::CONFIG_PARAM_CREDENTIALS_KEY] && $config[self::CONFIG_PARAM_CREDENTIALS_SECRET]) {
            $credentials = [
                'key' => $config[self::CONFIG_PARAM_CREDENTIALS_KEY],
                'secret' => $config[self::CONFIG_PARAM_CREDENTIALS_SECRET],
                'token' => $config[self::CONFIG_PARAM_CREDENTIALS_TOKEN],
            ];
        }
        $clientFactory = $config[self::CONFIG_PARAM_CLIENT_FACTORY] ?: [$this, 'createClient'];
        if (!is_callable($clientFactory)) {
            throw new \RuntimeException('Invalid S3 API client factory callback');
        }

        $args = [
            'version' => $version,
            'region' => $region,
            'credentials' => $credentials,
        ];

        $this->api = call_user_func($clientFactory, $args);
    }

    /**
     * @param string $binaryImage
     * @param string $filename
     *
     * @return string URL to the image
     */
    public function upload($binaryImage, $filename)
    {
        $result = $this->api->upload($this->bucket, $filename, $binaryImage, 'public-read');

        return $result['ObjectURL'];
    }

    /**
     * @param array $args
     *
     * @return S3Client
     */
    public function createClient($args)
    {
        return new S3Client($args);
    }
}
