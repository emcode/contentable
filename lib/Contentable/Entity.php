<?php

namespace Contentable;

use Contentable\Component\Component;
use Contentable\Component\Exception\ComponentNotFoundException;
use Contentable\Component\Exception\FieldNotFoundException;
use Contentable\Field\Field;

class Entity
{
    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var Component[]
     */
    protected $components;

    /**
     * @param string $type
     */
    public function __construct($slug, $type)
    {
        $this->slug = $slug;
        $this->type = $type;
        $this->components = array();
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    public function addComponent(Component $component)
    {
        $name = $component->getName();

        if (empty($name))
        {
            throw new \InvalidArgumentException(
                'Trying add Component instance with empty name which is impossible. Occurred on entity with slug: "%s"',
                $this->slug
            );
        }

        if (isset($this->components[$name]))
        {
            throw new \RuntimeException(sprintf(
                'Component with name "%s" already exists in entity instance! Occurred on entity with slug: "%s"',
                $name, $this->slug
            ));
        }

        $this->components[$name] = $component;
        return $this;
    }

    public function getComponentByName($name)
    {
        if (!isset($this->components[$name]))
        {
            throw new ComponentNotFoundException(sprintf(
                'Component with name "%s" does not exist on "%s" entity instance. Available components: %s',
                $name, $this->getType(), implode(', ', array_keys($this->components))
            ));
        }

        $component = $this->components[$name];
        return $component;
    }

    /**
     * @return Component[]
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * @param Component[] $components
     */
    public function setComponents($components)
    {
        $this->components = $components;
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
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    public function getFieldByName($pathToField)
    {
        $parts = explode('.', $pathToField);
        $componentName = $fieldName = $parts[0];

        /* @var $component Component */
        $component = $this->getComponentByName($componentName);

        if (isset($parts[1]))
        {
            $fieldName = $parts[1];
        }

        /* @var $field Field */
        $field = $component->getFieldByName($fieldName);
        return $field;
    }

    public function get($pathToValue)
    {
        $field = $this->getFieldByName($pathToValue);
        $value = $field->getValue();
        return $value;
    }

    public function isDefined($pathToValue)
    {
        $result = true;

        try
        {
            $this->getFieldByName($pathToValue);

        } catch (FieldNotFoundException $exception)
        {
            $result = false;

        } catch (ComponentNotFoundException $exception)
        {
            $result = false;
        }

        return $result;
    }

    public function isEmpty($pathToValue)
    {
        $isEmpty = null;
        $field = null;

        try
        {
            $field = $this->getFieldByName($pathToValue);

        } catch (FieldNotFoundException $exception)
        {
            $isEmpty = true;

        } catch (ComponentNotFoundException $exception)
        {
            $isEmpty = true;
        }

        if ($isEmpty || null === $field)
        {
            return $isEmpty;
        }

        $isEmpty = $field->hasEmptyValue();
        return $isEmpty;
    }
}
