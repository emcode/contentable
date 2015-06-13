<?php

namespace Contentable\Driver;

use Contentable\Component\ComponentTypeInterface;
use Contentable\Component\LoaderInterface;
use Contentable\Component\Storage\ComponentLoaderInterface;
use Contentable\Content\ContentTypeInterface;
use Contentable\Entity;

class PhpGlobDriver implements DriverInterface
{
    /**
     * @var string
     */
    protected $slugPlaceholder = '%slug%';

    /**
     * @var string
     */
    protected $slugRegex = '[0-9a-z\-]+';

    /**
     * @var ContentTypeInterface
     */
    protected $contentType;

    /**
     * @var ComponentTypeInterface
     */
    protected $baseComponent;

    /**
     * string
     */
    protected $baseComponentPathPattern;

    /**
     * @var ComponentLoaderInterface
     */
    protected $loader;

    public function __construct(ComponentLoaderInterface $componentLoader)
    {
        $this->loader = $componentLoader;
    }

    public function findSlugs($limit = null, $offset = 0)
    {
        $globPattern = str_replace($this->slugPlaceholder, '*', $this->baseComponentPathPattern);
        $regexPattern = $this->prepareComponentRegex($this->baseComponentPathPattern);
        $scanResult = glob($globPattern);
        $slugs = [];

        foreach($scanResult as $someFilePath)
        {
            $matchingResult = [];
            $regexResult = preg_match($regexPattern, $someFilePath, $matchingResult);
            if ($regexResult) $slugs[] = $matchingResult[1];
        }

        return $slugs;
    }

    public function findEntity($slug)
    {
        $entity = new Entity($slug, $this->contentType->getName());
        $componentTypes = $this->contentType->getComponentTypes();

        foreach($componentTypes as $componentType)
        {
            $componentPath = $this->prepareComponentPath($slug, $componentType);
            $component = $this->loader->loadComponent($componentPath, $componentType);
            $entity->addComponent($component);
        }

        return $entity;
    }

    public function setContentType(ContentTypeInterface $contentType)
    {
        $this->contentType = $contentType;
        $this->baseComponent = $contentType->getComponentTypeByName($contentType->getBaseComponentName());
        $this->baseComponentPathPattern = $this->prepareComponentPathPattern($this->baseComponent);
    }

    protected function prepareComponentPath($slug, ComponentTypeInterface $component)
    {
        $pathPattern = $this->prepareComponentPathPattern($component);
        $path = strtr($pathPattern, array($this->slugPlaceholder => $slug));
        return $path;
    }

    protected function prepareComponentPathPattern(ComponentTypeInterface $component)
    {
        $componentPath = sprintf('%s/%s', $this->contentType->getPath(), $component->getPath());
        return $componentPath;
    }

    protected function prepareComponentRegex($componentPath)
    {
        $cleanedPath = str_replace(array('/', '.'), array('\/', '\.'), $componentPath);
        $regexPattern = str_replace($this->slugPlaceholder, sprintf('(%s)', $this->slugRegex), $cleanedPath);
        $regexPattern = sprintf('/%s/', $regexPattern);
        return $regexPattern;
    }
}