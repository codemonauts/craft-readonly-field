<?php

namespace codemonauts\readonly\feedme;

use craft\feedme\base\Field;
use craft\feedme\base\FieldInterface;

class ReadonlyField extends Field implements FieldInterface
{
    public static $name = 'Read-only Field';
    public static $class = 'codemonauts\readonly\fields\ReadonlyField';

    public function getMappingTemplate(): string
    {
        return 'readonly/feedme';
    }

    public function parseField(): string
    {
        return (string)$this->fetchValue();
    }
}
