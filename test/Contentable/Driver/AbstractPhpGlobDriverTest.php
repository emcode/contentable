<?php

namespace Contentable\Driver;

use Contentable\Component\Storage\BasicLoader;
use Contentable\Component\Storage\TextFileHandler;
use Contentable\Component\Storage\YmlFileHandler;
use Contentable\Config\YamlConfigLoader;
use PHPUnit_Framework_TestCase;

abstract class AbstractGlobDriverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PhpGlobDriver
     */
    protected $driver;

    /**
     * Path to fixture configuration and content that will be used during tests
     * @return string
     */
    protected abstract function getFixturePath();

    /**
     * Name of content type that will be tested
     * @return string
     */
    protected abstract function getContentTypeName();

    public static function setupPhpGlobDriver($testingEnvironmentPath, $contentTypeName)
    {
        $contentLoader = new BasicLoader();
        $contentLoader->addHandler('yml', new YmlFileHandler());
        $contentLoader->addHandler('md', new TextFileHandler());
        $configLoader = new YamlConfigLoader($testingEnvironmentPath);
        $contentType = $configLoader->loadContentType($contentTypeName);
        $contentType->setPath($testingEnvironmentPath . '/' . $contentType->getPath());
        $driver = new PhpGlobDriver($contentType, $contentLoader);
        return $driver;
    }

    public function setUp()
    {
        $this->driver = self::setupPhpGlobDriver($this->getFixturePath(), $this->getContentTypeName());
    }
}