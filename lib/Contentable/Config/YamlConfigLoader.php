<?php

namespace Contentable\Config;

use Contentable\Content\Exception\ContentTypeConfigNotFoundException;
use Symfony\Component\Yaml\Yaml;

class YamlConfigLoader extends AbstractConfigLoader
{
    protected function loadConfigData($contentTypeName)
    {
        $configPath = sprintf('%s/%s.yml', $this->configPath, $contentTypeName);

        if (!is_file($configPath))
        {
            throw new ContentTypeConfigNotFoundException(sprintf(
                'Config file of "%s" content type could not be found in path: %s (current base path: %s)',
                $contentTypeName, $configPath, realpath('.')
            ));
        }

        $rawData = file_get_contents($configPath);

        if (false === $rawData)
        {
            throw new \RuntimeException(sprintf(
                'Could not load config content from path: "%s"',
                $configPath
            ));
        }

        $ymlData = Yaml::parse($rawData, true);
        return $ymlData;
    }
}