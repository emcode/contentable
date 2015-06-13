<?php

namespace Contentable\Component;

use Contentable\Component\Exception\FieldNotFoundException;
use Contentable\Field\Field;

class Component
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
     * @var Field[]
     */
    protected $fields;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return \Contentable\Field\Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param \Contentable\Field\Field[] $fields
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param $fieldName
     * @return Field
     * @throws FieldNotFoundException
     */
    public function getFieldByName($fieldName)
    {
        if (!isset($this->fields[$fieldName]))
        {
            throw new FieldNotFoundException(sprintf(
                'Field with name "%s" could not be found in component named: "%s". Available fields: %s.',
                $fieldName, $this->getName(), implode(', ', array_keys($this->fields))
            ));
        }

        return $this->fields[$fieldName];
    }

    public function addField(Field $field)
    {
        $name = $field->getName();

        if (empty($name))
        {
            throw new \InvalidArgumentException(
                'Trying add Field instance with empty name which is impossible. Occurred on component with name: "%s"',
                $this->name
            );
        }

        if (isset($this->fields[$name]))
        {
            throw new \RuntimeException(sprintf(
                'Field with name "%s" already exists in entity instance! Occurred on component with type: "%s"',
                $name, $this->type
            ));
        }

        $this->fields[$name] = $field;
        return $this;
    }
}