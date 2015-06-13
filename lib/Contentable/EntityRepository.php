<?php

namespace Contentable;

use Contentable\Content\ContentTypeInterface;
use Contentable\Driver\DriverInterface;

class EntityRepository
{
    /**
     * @var ContentTypeInterface
     */
    protected $contentType;

    /**
     * @var \ArrayAccess
     */
    protected $serviceLocator;

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @param ContentTypeInterface $contentType
     * @param DriverInterface $driver
     */
    public function __construct(ContentTypeInterface $contentType, DriverInterface $driver)
    {
        $this->driver = $driver;
        $this->setContentType($contentType);
    }

    public function setContentType(ContentTypeInterface $contentType)
    {
        $this->driver->setContentType($contentType);
        $this->contentType = $contentType;
    }

    public function findAll()
    {
        $allSlugs = $this->driver->findSlugs();
        $allEntities = array();

        foreach($allSlugs as $currentSlug)
        {
            $allEntities[] = $this->driver->findEntity($currentSlug);
        }

        return $allEntities;
    }

    public function findSlugs()
    {
        return $this->driver->findSlugs();
    }

    public function findEntity($slug)
    {
        return $this->driver->findEntity($slug);
    }
}