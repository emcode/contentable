<?php

namespace Contentable\Driver;

use Contentable\Component\ComponentTypeInterface;
use Contentable\Component\LoaderInterface;
use Contentable\Component\Storage\ComponentLoaderInterface;
use Contentable\Content\ContentTypeInterface;
use Contentable\Entity;

class PhpGlobDriver implements DriverInterface
{
    /**
     * @var string
     */
    protected $tokenDelimiter = '%';

    /**
     * @var string
     */
    protected $tokenValueRegex = '[0-9a-z\-]+';

    /**
     * @var ContentTypeInterface
     */
    protected $contentType;

    /**
     * @var ComponentTypeInterface
     */
    protected $baseComponent;

    /**
     * @var string[]
     */
    protected $baseComponentPathPattern;

    /**
     * @var string[]
     */
    protected $baseComponentPathTokens;

    /**
     * @var string
     */
    protected $tokenDetectionRegex = '/(\%s[a-zA-Z0-9\_]+\%s)/';

    /**
     * @var ComponentLoaderInterface
     */
    protected $loader;

    public function __construct(ContentTypeInterface $contentType, ComponentLoaderInterface $componentLoader)
    {
        $this->loader = $componentLoader;
        $this->setContentType($contentType);
    }

    public function find(array $predicate = null, $limit = null, $offset = 0)
    {
        $predicateTokens = $predicate ? $this->tokenize($predicate, $this->tokenDelimiter) : [];

        $defaultGlobs = [];

        foreach($this->baseComponentPathTokens as $token)
        {
            $defaultGlobs[$token] = '*';
        }

        $globs = array_merge($defaultGlobs, $predicateTokens);
        $globPattern = strtr($this->baseComponentPathPattern, $globs);
        $regexPattern = $this->prepareComponentRegex($this->baseComponentPathPattern);
        $scanResult = glob($globPattern);

        $findingResult = [];

        foreach($scanResult as $someFilePath)
        {
            $matchingResult = [];
            $regexResult = preg_match($regexPattern, $someFilePath, $matchingResult);

            if (!$regexResult) continue;

            $resultItem = [];

            foreach($this->baseComponentPathTokens as $tokenName => $tokenPattern)
            {
                $resultItem[$tokenName] = $matchingResult[$tokenName];
            }

            $findingResult[] = $resultItem;
        }

        return $findingResult;
    }

    protected function tokenize(array $someArray, $tokenPrefix, $tokenPostfix = null)
    {
        $tokenPostfix = (null === $tokenPostfix) ? $tokenPrefix : $tokenPostfix;
        $tokens = [];

        foreach($someArray as $index => $value)
        {
            $currentToken = $tokenPrefix . $index . $tokenPostfix;
            $tokens[$currentToken] = $value;
        }

        return $tokens;
    }

    public function loadEntity(array $predicate)
    {
        $predicateTokens = $this->tokenize($predicate, $this->tokenDelimiter);
        $missingTokens = array_diff($this->baseComponentPathTokens, array_keys($predicateTokens));

        if (!empty($missingTokens))
        {
            throw new \RuntimeException(sprintf(
                'Cannot load entity because of missing path token(s): %s. Pass missing key(s) in predicate array!',
                implode(', ', $missingTokens)
            ));
        }

        $entity = new Entity($this->contentType->getName(), $predicate);
        $componentTypes = $this->contentType->getComponentTypes();

        foreach($componentTypes as $componentType)
        {
            $pathPattern = $this->prepareComponentPathPattern($componentType);
            $componentPath = strtr($pathPattern, $predicateTokens);
            $component = $this->loader->loadComponent($componentPath, $componentType);
            $entity->addComponent($component);
        }

        return $entity;
    }

    public function setContentType(ContentTypeInterface $contentType)
    {
        $this->contentType = $contentType;
        $this->baseComponent = $contentType->getComponentTypeByName($contentType->getBaseComponentName());
        $this->baseComponentPathPattern = $this->prepareComponentPathPattern($this->baseComponent);
        $this->baseComponentPathTokens = $this->discoverTokens($this->baseComponentPathPattern, $this->tokenDelimiter);
    }

    protected function prepareComponentPath(array $variables, ComponentTypeInterface $component, $lang = null)
    {
        $pathPattern = $this->prepareComponentPathPattern($component);

        $path = strtr($pathPattern, $variables);
        return $path;
    }

    protected function prepareComponentPathPattern(ComponentTypeInterface $component)
    {
        $componentPath = sprintf('%s/%s', $this->contentType->getPath(), $component->getPath());
        return $componentPath;
    }

    protected function prepareComponentRegex($componentPath)
    {
        $cleanedPath = str_replace(array('/', '.'), array('\/', '\.'), $componentPath);

        foreach($this->baseComponentPathTokens as $tokenName => $tokenPattern)
        {
            $cleanedPath = str_replace($tokenPattern, sprintf('(?<%s>%s)', $tokenName, $this->tokenValueRegex), $cleanedPath);
        }

        $regexPattern = sprintf('/%s/', $cleanedPath);
        return $regexPattern;
    }

    /**
     * @param $pattern string
     * @param $tokenPrefix string
     * @param $tokenPostfix string
     * @return string[] Tokens that are within pattern
     */
    public function discoverTokens($pattern, $tokenPrefix, $tokenPostfix = null)
    {
        $tokenPostfix = (null === $tokenPostfix) ? $tokenPrefix : $tokenPostfix;
        $regex = sprintf($this->tokenDetectionRegex, $tokenPrefix, $tokenPostfix);
        $matchingResult = [];
        $result = preg_match_all($regex, $pattern, $matchingResult);

        if (false === $result)
        {
            throw new \RuntimeException(sprintf(
                'Could not proceed with token detection! Regex matching failed. Regex pattern: "%s". Regex subject: "%s"',
                $regex, $pattern
            ));
        }

        $detectedTokens = $matchingResult[0];
        $prefixLength = strlen($tokenPrefix);
        $postfixLength = strlen($tokenPostfix);

        $result = [];
        foreach($detectedTokens as $tokenWithDelimiter)
        {
            $tokenWithoutDelimiter = substr($tokenWithDelimiter, $prefixLength, $postfixLength * -1);
            $result[$tokenWithoutDelimiter] = $tokenWithDelimiter;
        }

        return $result;
    }

    /**
     * @return string[]
     */
    public function getBaseComponentPathTokens()
    {
        return $this->baseComponentPathTokens;
    }
}