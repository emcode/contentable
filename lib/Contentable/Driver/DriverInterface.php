<?php

namespace Contentable\Driver;

use Contentable\Content\ContentTypeInterface;
use Contentable\Entity;

interface DriverInterface
{
    public function setContentType(ContentTypeInterface $contentType);

    /**
     * @return string[]
     */
    public function findSlugs($limit = null, $offset = 0);

    /**
     * @return Entity
     */
    public function findEntity($slug);
}