<?php

namespace codemonauts\readonly;

use \craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use yii\base\Event;
use codemonauts\readonly\fields\Readonly as ReadonlyField;

class Readonly extends Plugin
{
    public function init()
    {
        parent::init();

        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = ReadonlyField::class;
        });
    }
}
