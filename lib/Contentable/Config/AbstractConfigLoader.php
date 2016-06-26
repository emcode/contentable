<?php

namespace Contentable\Config;

use Contentable\Component;
use Contentable\Content\ContentTypeInterface;
use Contentable\Content\ContentType;
use Contentable\Component\ComponentType;
use Contentable\Field\FieldType;

abstract class AbstractConfigLoader implements ConfigLoaderInterface
{
    /**
     * @var string
     */
    protected $configPath;

    /**
     * @var array
     */
    protected $defaultHandlerMapping;

    /**
     * @param $configPath
     * @param array $defaultComponentHandlers
     */
    public function __construct($configPath, array $defaultComponentHandlers = array())
    {
        $this->configPath = $configPath;
        $this->defaultHandlerMapping = $defaultComponentHandlers;
    }

    /**
     * @param string $contentTypeName
     * @return array
     */
    abstract protected function loadConfigData($contentTypeName);

    /**
     * @param string $contentTypeName
     * @return ContentTypeInterface
     */
    public function loadContentType($contentTypeName)
    {
        $configData = $this->loadConfigData($contentTypeName);
        $contentType = $this->parseContentTypeConfig($configData, $contentTypeName);
        return $contentType;
    }

    public function parseContentTypeConfig(array $rawData, $contentTypeName)
    {
        $type = new ContentType();
        $type->setName($contentTypeName);
        $type->setPath($this->getKey($rawData, 'path', 'string'));
        $rawComponentsData = $this->getKey($rawData, 'components', 'array');
        $components = $this->parseConfigItems($rawComponentsData, array($this, 'parseComponentTypeConfig'));
        $type->setComponentTypes($components);
        return $type;
    }

    public function parseComponentTypeConfig(array $rawData, $componentName)
    {
        $typeName = $this->getOptionalKey($rawData, 'type', 'string');
        $component = new ComponentType();
        $component->setType($typeName);
        $component->setName($componentName);
        $component->setPath($this->getKey($rawData, 'path', 'string'));

        $handlerName = $this->getOptionalKey($rawData, 'handler', 'string', null);

        if (null === $handlerName)
        {
            // this will throw exception if handler cannot be guessed based on file extension
            $handlerName = $this->resolveContentTypeHandler($component->getPath());
        }

        $component->setHandlerName($handlerName);

        $rawFieldsData = $this->getOptionalKey($rawData, 'fields', 'array', array());
        $fieldTypes = $this->parseConfigItems($rawFieldsData, array($this, 'parseFieldTypesConfig'));
        $component->setFieldTypes($fieldTypes);
        return $component;
    }

    public function resolveContentTypeHandler($somePath)
    {
       if (empty($somePath))
       {
           throw new \InvalidArgumentException(
               'Could not resolve handler name based on received file path! Received file path is empty!'
           );
       }

       $extension = pathinfo($somePath, PATHINFO_EXTENSION);

       if (empty($extension))
       {
           throw new \RuntimeException(sprintf(
               'Could not infer file loading handler based on file extension - extension is empty. Received path: "%s"',
               $somePath
           ));
       }

       if (isset($this->defaultHandlerMapping[$extension]))
       {
           return $this->defaultHandlerMapping[$extension];
       }

       return $extension;
    }


    public function parseFieldTypesConfig(array $rawData, $fieldTypeName)
    {
        $field = new FieldType();
        $field->setName($fieldTypeName);
        $field->setHandlerName($this->getOptionalKey($rawData, 'handler', 'string', 'string'));
        $field->setNullable($this->getOptionalKey($rawData, 'nullable', 'boolean', false));
        return $field;
    }

    public function parseConfigItems(array $rawData, array $callback)
    {
        $parsedItems = array();

        foreach($rawData as $configKey => & $configItem)
        {
            $parsedItems[$configKey] = call_user_func($callback, $configItem, $configKey);
        }

        return $parsedItems;
    }

    protected function getKey(array $config, $key, $expectedType = null)
    {
        if (!isset($config[$key]))
        {
            throw new \RuntimeException(sprintf(
                'Required key "%s" is not set in config! Received keys: %s',
                $key, implode(', ', array_keys($config))
            ));
        }

        $value = & $config[$key];

        if (null !== $expectedType)
        {
            if (!$this->isValueTypeValid($value, $expectedType))
            {
                $realType = is_object($value) ? get_class($value) : gettype($value);
                throw new \RuntimeException(sprintf(
                    'Configuration key "%s" contains value of unexpected type: %s. Expected %s.',
                    $key, $realType, is_array($expectedType) ? 'one of: ' . implode(', ', $expectedType) : $expectedType
                ));
            }
        }

        return $value;
    }

    protected function getOptionalKey(array $config, $key, $expectedType = null, $defaultValue = null)
    {
        if (!isset($config[$key]))
        {
            return $defaultValue;
        }

        $value = & $config[$key];

        if (null !== $expectedType)
        {
            if (!$this->isValueTypeValid($value, $expectedType))
            {
                $realType = is_object($value) ? get_class($value) : gettype($value);
                throw new \RuntimeException(sprintf(
                    'Optional configuration key "%s" contains value of unexpected type: %s. Expected %s.',
                    $key, $realType, is_array($expectedType) ? 'one of: ' . implode(', ', $expectedType) : $expectedType
                ));
            }
        }

        return $value;
    }

    protected function isValueTypeValid($value, $expectedType)
    {
        $type = is_object($value) ? get_class($value) : gettype($value);

        if (is_array($expectedType))
        {
            $isValid = in_array($type, $expectedType);

        } else
        {
            $isValid = $type === $expectedType;
        }

        return $isValid;
    }

    /**
     * @return mixed
     */
    public function getConfigPath()
    {
        return $this->configPath;
    }

    /**
     * @param mixed $configPath
     */
    public function setConfigPath($configPath)
    {
        $this->configPath = $configPath;
    }
}