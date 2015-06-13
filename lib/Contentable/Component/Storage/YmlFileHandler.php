<?php

namespace Contentable\Component\Storage;

use Contentable\Component\Component;
use Contentable\Component\ComponentTypeInterface;
use Contentable\Field\Field;
use Symfony\Component\Yaml\Yaml;

class YmlFileHandler implements ComponentHandlerInterface
{
    /**
     * @var \ArrayAccess
     */
    protected $serviceLocator;

    public function load($sourcePath, ComponentTypeInterface $componentType)
    {
        $component = new Component();
        $component->setName($componentType->getName());

        $rawString = file_get_contents($sourcePath);
        $rawArray = Yaml::parse($rawString);
        $fields = array();

        foreach($componentType->getFieldTypes() as $fieldType)
        {
            $fieldName = $fieldType->getName();

            if (!array_key_exists($fieldName, $rawArray))
            {
                throw new \RuntimeException(sprintf(
                   'Field "%s" of "%s" component could not be retrieved from source file: "%s". Available fields: %s',
                    $fieldName, $componentType->getName(), $sourcePath, implode(', ', array_keys($rawArray))
                ));
            }

            $field = new Field();
            $field->setName($fieldName);
            $field->setValue($rawArray[$fieldName]);
            $fields[$fieldName] = $field;
        }

        $component->setFields($fields);
        return $component;
    }

    public function save(Component $value, $targetPath)
    {
        $result = file_put_contents($targetPath, $value);
        return $result;
    }

    /**
     * @return \ArrayAccess|null
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @param \ArrayAccess $serviceLocator
     * @return $this
     */
    public function setServiceLocator(\ArrayAccess $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        return $this;
    }
}