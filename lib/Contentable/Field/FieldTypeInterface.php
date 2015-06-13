<?php

namespace Contentable\Field;

interface FieldTypeInterface
{
    /**
     * @return mixed
     */
    public function getName();
    public function extractValue($rawData);
}