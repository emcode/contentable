<?php

namespace Contentable\Field\Handler;

interface FieldHandlerInterface
{
    public function decode($rawData);
    public function encode($normalizedData);
}