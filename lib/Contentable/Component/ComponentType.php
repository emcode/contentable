<?php
namespace Contentable\Component;

use Contentable\Component\Storage\ComponentHandlerInterface;
use Contentable\FieldTypeInterface;

class ComponentType implements ComponentTypeInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $handlerName;

    /**
     * @var ComponentHandlerInterface
     */
    protected $handler;

    /**
     * @var FieldTypeInterface[]
     */
    protected $fieldTypes;

    /**
     * @return string
     */
    public function getHandlerName()
    {
        return $this->handlerName;
    }

    /**
     * @param string $handlerName
     * @return $this
     */
    public function setHandlerName($handlerName)
    {
        $this->handlerName = $handlerName;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasAnyHandlerName()
    {
        return null !== $this->handlerName;
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
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
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
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param \Contentable\FieldTypeInterface[] $fieldTypes
     * @return $this
     */
    public function setFieldTypes(array $fieldTypes)
    {
        $this->fieldTypes = $fieldTypes;
        return $this;
    }

    /**
     * @return FieldTypeInterface[]
     */
    public function getFieldTypes()
    {
        return $this->fieldTypes;
    }

    /**
     * @return FieldTypeInterface[]
     */
    public function getFieldTypeByName($name)
    {
        return $this->fieldTypes[$name];
    }

    /**
     * @return ComponentHandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param ComponentHandlerInterface $handler
     * @return $this
     */
    public function setHandler(ComponentHandlerInterface $handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasAnyHandlerInstance()
    {
        return null !== $this->handler;
    }
}