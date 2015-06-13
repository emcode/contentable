<?php

namespace Contentable\Service;

use Contentable\Config\ConfigLoaderInterface;
use Contentable\Content\ContentTypeInterface;
use Contentable\Type;

class TypeService
{
    /**
     * @var string|null
     */
    protected $contentBasePath;

    /**
     * @var ConfigLoaderInterface
     */
    protected $configLoader;

    /**
     * @var ContentTypeInterface[]
     */
    protected $loadedTypes;

    /**
     * @param ConfigLoaderInterface $configLoader
     */
    public function __construct(ConfigLoaderInterface $configLoader)
    {
        $this->configLoader = $configLoader;
        $this->loadedTypes = array();
    }

    public function getContentTypeByName($name)
    {
        if (!isset($this->loadedTypes[$name]))
        {
            $contentType = $this->configLoader->loadContentType($name);
            $contentType = $this->setupContentType($contentType);
            $this->loadedTypes[$name] = $contentType;
        }

        return $this->loadedTypes[$name];
    }

    protected function setupContentType(ContentTypeInterface $type)
    {
        if (null !== $this->contentBasePath)
        {
            $type->setPath(sprintf('%s/%s', $this->contentBasePath, $type->getPath()));
        }

        return $type;
    }

    /**
     * @return ConfigLoaderInterface
     */
    public function getConfigLoader()
    {
        return $this->configLoader;
    }

    /**
     * @param ConfigLoaderInterface $configLoader
     * @return $this
     */
    public function setConfigLoader(ConfigLoaderInterface $configLoader)
    {
        $this->configLoader = $configLoader;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getContentBasePath()
    {
        return $this->contentBasePath;
    }

    /**
     * @param null|string $contentBasePath
     */
    public function setContentBasePath($contentBasePath)
    {
        $this->contentBasePath = $contentBasePath;
    }
}