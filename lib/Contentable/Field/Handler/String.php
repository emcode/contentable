<?php
namespace Contentable\Field\Handler;

class String implements FieldHandlerInterface
{
    public function decode($rawData)
    {
        return (string) $rawData;
    }

    public function encode($normalizedData)
    {
        return (string) $normalizedData;
    }
}