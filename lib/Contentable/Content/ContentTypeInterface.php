<?php

namespace Contentable\Content;

use Contentable\Component\ComponentTypeInterface;

interface ContentTypeInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getPath();


    /**
     * @param string $path
     */
    public function setPath($path);

    /**
     * @param string $pathPrefix
     */
    public function prependPath($pathPrefix);
    
    /**
     * @return ComponentTypeInterface[]
     */
    public function getComponentTypes();

    /**
     * @return ComponentTypeInterface
     */
    public function getComponentTypeByName($typeName);

    /**
     * @return string
     */
    public function getBaseComponentName();
}