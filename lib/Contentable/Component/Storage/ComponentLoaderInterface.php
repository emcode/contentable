<?php

namespace Contentable\Component\Storage;

use Contentable\Component\Component;
use Contentable\Component\ComponentTypeInterface;

interface ComponentLoaderInterface
{
    /**
     * @param string $pathToFile
     * @param ComponentTypeInterface $type
     * @return Component
     */
    public function loadComponent($pathToFile, ComponentTypeInterface $type);
}