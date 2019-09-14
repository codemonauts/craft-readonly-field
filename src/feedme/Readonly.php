<?php

namespace codemonauts\readonly\feedme;

use craft\feedme\base\Field;
use craft\feedme\base\FieldInterface;

class Readonly extends Field implements FieldInterface
{
    public static $name = 'Read-only Field';
    public static $class = 'codemonauts\readonly\fields\Readonly';

    public function getMappingTemplate(): string
    {
        return 'readonly/feedme';
    }

    public function parseField(): string
    {
        return (string)$this->fetchValue();
    }
}
