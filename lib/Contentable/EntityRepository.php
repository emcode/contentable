<?php

namespace Contentable;

use Contentable\Content\ContentTypeInterface;
use Contentable\Driver\DriverInterface;

class EntityRepository
{
    /**
     * @var \ArrayAccess
     */
    protected $serviceLocator;

    /**
     * @var DriverInterface
     */
    protected $driver;

    /**
     * @param DriverInterface $driver
     */
    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function setContentType(ContentTypeInterface $contentType)
    {
        return $this->driver->setContentType($contentType);
    }

    public function loadEntity(array $predicate)
    {
        return $this->driver->loadEntity($predicate);
    }

    public function loadEntities(array $predicate, $limit = null, $offset = 0)
    {
        $matchingPredicates = $this->driver->find($predicate, $limit, $offset);
        $loadedEntities = [];

        foreach($matchingPredicates as $currentPredicate)
        {
            $loadedEntities[] = $this->driver->loadEntity($currentPredicate);
        }

        return $loadedEntities;
    }

    public function find(array $predicate = null, $limit = null, $offset = 0)
    {
        return $this->driver->find($predicate, $limit, $offset);
    }
}