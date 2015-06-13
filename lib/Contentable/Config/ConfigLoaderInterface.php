<?php

namespace Contentable\Config;

use Contentable\Content\ContentTypeInterface;

interface ConfigLoaderInterface
{
    /**
     * @param string $contentTypeName
     * @return ContentTypeInterface
     */
    public function loadContentType($contentTypeName);
}