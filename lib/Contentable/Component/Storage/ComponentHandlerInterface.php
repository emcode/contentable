<?php

namespace Contentable\Component\Storage;

use Contentable\Component\Component;
use Contentable\Component\ComponentTypeInterface;

interface ComponentHandlerInterface
{
    /**
     * @param $sourcePath
     * @param ComponentTypeInterface $componentType
     * @return Component
     */
    public function load($sourcePath, ComponentTypeInterface $componentType);

    /**
     * @param Component $component
     * @param $targetPath
     * @return mixed
     */
    public function save(Component $component, $targetPath);
}