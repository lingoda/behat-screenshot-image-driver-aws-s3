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
    const CONFIG_PARAM_CREDENTIALS = 'credentials';
    const CONFIG_PARAM_CLIENT = 'client';
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
                ->arrayNode(self::CONFIG_PARAM_CREDENTIALS)
                    ->defaultNull()
                    ->children()
                        ->scalarNode('key')->isRequired()->end()
                        ->scalarNode('secret')->isRequired()->end()
                        ->scalarNode('token')->end()
                    ->end()
                ->end()
                ->scalarNode(self::CONFIG_PARAM_CLIENT)->isRequired()->defaultValue(S3Client::CLASS)->end()
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
        $credentials = $config[self::CONFIG_PARAM_CREDENTIALS];
        $clientClass = $config[self::CONFIG_PARAM_CLIENT];

        $args = [
            'version' => $version,
            'region' => $region,
            'credentials' => $credentials,
        ];

        $this->api = new S3Client($args);
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
}
