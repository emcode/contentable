<?php

namespace Contentable\Component;

use Contentable\Component\Storage\ComponentHandlerInterface;
use Contentable\Field\FieldTypeInterface;

interface ComponentTypeInterface
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
     * @return string|null
     */
    public function getHandlerName();

    /**
     * @return ComponentHandlerInterface|null
     */
    public function getHandler();

    /**
     * @return bool
     */
    public function hasAnyHandlerInstance();

    /**
     * @return bool
     */
    public function hasAnyHandlerName();

    /**
     * @return FieldTypeInterface[]
     */
    public function getFieldTypes();

    /**
     * @return FieldTypeInterface
     */
    public function getFieldTypeByName($name);

}