<?php

require_once __DIR__ . '/../vendor/autoload.php';
use Contentable\Driver\PhpGlobDriver;
use Contentable\Component\Storage;
use Contentable\Service;
use Contentable\Config;
use Contentable\EntityRepository;

if (!isset($argv[1]))
{
    echo 'Type in content type name as first argument of this command' . PHP_EOL;
    return;
}

$contentTypeName = (string) $argv[1];

$componentHandlers = [
    'text_file_handler' =>  new Storage\TextFileHandler(),
    'yml_file_handler' => new Storage\YmlFileHandler()
];

$extensionHandlers = [
    'md' => 'text_file_handler',
    'yml' => 'yml_file_handler'
];

$example = 'article-ex-01';
$contentPath = __DIR__ . '/' . $example;
$configPath = __DIR__ . '/' . $example;

$contentLoader = new Storage\BasicLoader();
$contentLoader->setHandlers($componentHandlers);
// $configLoader = new Config\YamlConfigLoader($configPath, $extensionHandlers);
// $typeService = new Service\TypeService($configLoader);
// $typeService->setContentBasePath($contentPath);
// $contentType = $typeService->getContentTypeByName($contentTypeName);

$typeName = 'article';
$contentType = new \Contentable\Content\ContentType();
$contentType->setPath($contentPath . '/' . $typeName);

$content = new \Contentable\Component\ComponentType();
$content->setName('content');
$content->getHandler(new Storage\TextFileHandler());
$content->getPath('%slug%/content.md');

$meta = new \Contentable\Component\ComponentType();
$meta->setName('meta');
$meta->getHandler(new Storage\YmlFileHandler());
$meta->getPath('%slug%/meta.yml');

$contentType->setComponentTypes([
    'content' => $content,
    'meta' => $meta
]);
$repo = new EntityRepository(new PhpGlobDriver($contentType, $contentLoader));

/* @var $entities \Contentable\Entity[] */
$entities = $repo->findAll();

echo $contentType->getName() . ':' . PHP_EOL;

foreach($entities as $entity)
{
    echo $entity->get('meta.published') . ' - ' . $entity->get('meta.title') . PHP_EOL;
}

echo 'command complete' . PHP_EOL;