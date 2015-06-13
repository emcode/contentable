<?php

namespace Contentable\Component\Storage;

use Contentable\Component\Component;
use Contentable\Component\ComponentTypeInterface;
use Contentable\Field\Handler\HandlerInterface;

class BasicLoader implements ComponentLoaderInterface
{
    /**
     * @var HandlerInterface[]
     */
    protected $initializedHandlers;

    /**
     * @var \ArrayAccess
     */
    protected $serviceLocator;

    /**
     * @param \ArrayAccess $serviceLocator
     */
    public function __construct(\ArrayAccess $serviceLocator = null)
    {
        $this->serviceLocator = $serviceLocator;
        $this->initializedHandlers = [];
    }

    /**
     * @param $componentFilePath
     * @param ComponentTypeInterface $type
     * @return Component
     */
    public function loadComponent($componentFilePath, ComponentTypeInterface $type)
    {
        /* @var $handler ComponentHandlerInterface */
        $handler = $this->getStorageHandler($type);
        /* @var $component Component */
        $component = $handler->load($componentFilePath, $type);

        if (!($component instanceof Component))
        {
            throw new \RuntimeException(sprintf(
                'Unexpected type received from component loader, expected Component instance, received: %s',
                is_object($component) ? get_class($component) : gettype($component)
            ));
        }

        return $component;
    }

    /**
     * @param ComponentTypeInterface $type
     * @return HandlerInterface
     */
    protected function getStorageHandler(ComponentTypeInterface $type)
    {
        if ($type->hasAnyHandlerInstance())
        {
            return $type->getHandler();
        }

        if ($type->hasAnyHandlerName())
        {
            return $this->getStorageHandlerByName($type->getHandlerName());
        }

        throw new \RuntimeException(sprintf(
            'Either handler instance or handler name should be set on component type "%s", class: %s, none found.',
            $type->getName(), get_class($type)
        ));
    }

    /**
     * @param string $handlerName
     * @return HandlerInterface
     */
    protected function getStorageHandlerByName($handlerName)
    {
        if (isset($this->initializedHandlers[$handlerName]))
        {
            return $this->initializedHandlers[$handlerName];
        }

        $handlerInstance = $this->initializeHandler($handlerName);

        if (!($handlerInstance instanceof ComponentHandlerInterface))
        {
            throw new \RuntimeException(sprintf(
                'Handler instance is expected to implement HandlerInterface, instance of %s received',
                get_class($handlerInstance)
            ));
        }

        $this->initializedHandlers[$handlerName] = $handlerInstance;
        return $handlerInstance;
    }

    protected function initializeHandler($handlerName)
    {
        if (null === $this->serviceLocator)
        {
            throw new \RuntimeException(sprintf(
                'Could not initialize "%s" handler. Service locator is not set in Repository instance.',
                $handlerName
            ));
        }

        $handlerInstance = $this->serviceLocator[$handlerName];
        return $handlerInstance;
    }

    /**
     * @return ComponentHandlerInterface[]
     */
    public function getInitializedHandlers()
    {
        return $this->initializedHandlers;
    }

    /**
     * @param ComponentHandlerInterface[] $initializedHandlers
     * @return $this
     */
    public function setInitializedHandlers(array $initializedHandlers)
    {
        $this->initializedHandlers = $initializedHandlers;
        return $this;
    }

    /**
     * @param ComponentHandlerInterface[] $handlers
     * @return $this
     */
    public function setHandlers(array $handlers)
    {
        return $this->setInitializedHandlers($handlers);
    }

    /**
     * @param $name
     * @param ComponentHandlerInterface $handler
     * @return $this
     */
    public function addHandler($name, ComponentHandlerInterface $handler)
    {
        $this->initializedHandlers[$name] = $handler;
        return $this;
    }
}