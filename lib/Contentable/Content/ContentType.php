<?php
namespace Contentable\Content;

use Contentable\Component\ComponentTypeInterface;
use Contentable\Component\Exception\ComponentNotFoundException;

class ContentType implements ContentTypeInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var ComponentTypeInterface[]
     */
    protected $componentTypes;

    /**
     * @return string
     */
    public function getBaseComponentName()
    {
        if (empty($this->componentTypes))
        {
            throw new ComponentNotFoundException(sprintf(
                'Could not determine base component type for content type: %s. There are no component types configured!',
                $this->getName()
            ));
        }

        // by default base component is the first component
        // mentioned in content type configuration
        /* @var $baseComponentType ComponentTypeInterface */
        $baseComponentType = reset($this->componentTypes);
        return $baseComponentType->getName();
    }

    /**
     * @param $typeName
     * @return ComponentTypeInterface
     */
    public function getComponentTypeByName($typeName)
    {
        if (!isset($this->componentTypes[$typeName]))
        {
            throw new ComponentNotFoundException(sprintf(
                'Component type with name "%s" could not be found in content type named: %s. Available types: %s.',
                $typeName, $this->getName(), implode(', ', array_keys($this->componentTypes))
            ));
        }

        return $this->componentTypes[$typeName];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    public function prependBasePath($basePath)
    {
        $this->path = rtrim($basePath, '/') . '/' . ltrim($this->path, '/');
        return $this;
    }

    /**
     * @return ComponentTypeInterface[]
     */
    public function getComponentTypes()
    {
        return $this->componentTypes;
    }

    /**
     * @param \Contentable\ComponentTypeInterface[] $componentTypes
     */
    public function setComponentTypes(array $componentTypes)
    {
        $this->componentTypes = $componentTypes;
    }
}