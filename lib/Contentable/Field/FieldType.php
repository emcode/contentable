<?php

namespace Contentable\Field;

use Contentable\Field\Handler\FieldHandlerInterface;

class FieldType
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $nullable;

    /**
     * @var string
     */
    protected $handlerName;

    /**
     * @var FieldHandlerInterface
     */
    protected $handler;

    /**
     * @return mixed
     */
    public function getNullable()
    {
        return $this->nullable;
    }

    /**
     * @param mixed $nullable
     * @return $this
     */
    public function setNullable($nullable)
    {
        $this->nullable = $nullable;
        return $this;
    }

    /**
     * @return bool
     */
    public function isNullable()
    {
        return (bool) $this->nullable;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
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
     * @return FieldHandlerInterface
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param FieldHandlerInterface $handler
     * @return $this
     */
    public function setHandler(FieldHandlerInterface $handler)
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