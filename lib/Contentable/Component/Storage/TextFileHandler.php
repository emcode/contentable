<?php

namespace Contentable\Component\Storage;

use Contentable\Component\Component;
use Contentable\Component\ComponentTypeInterface;
use Contentable\Field\Field;

class TextFileHandler implements ComponentHandlerInterface
{
    public function load($sourcePath, ComponentTypeInterface $componentType)
    {
        $componentName = $fieldName = $componentType->getName();
        $component = new Component();
        $component->setName($componentName);

        $value = file_get_contents($sourcePath);

        $baseField = new Field();
        $baseField->setName($fieldName);
        $baseField->setValue($value);

        $component->setFields(array(
            $fieldName => $baseField
        ));

        return $component;
    }

    public function save(Component $value, $targetPath)
    {
        $result = file_put_contents($targetPath, $value);
        return $result;
    }
}