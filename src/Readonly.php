<?php

namespace codemonauts\readonly;

use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\services\Fields;
use yii\base\Event;
use codemonauts\readonly\fields\Readonly as ReadonlyField;
use codemonauts\readonly\feedme\Readonly as ReadonlyFeedme;
use craft\feedme\events\RegisterFeedMeFieldsEvent;
use craft\feedme\services\Fields as FeedMeFields;

class Readonly extends Plugin
{
    public function init()
    {
        parent::init();

        Event::on(Fields::class, Fields::EVENT_REGISTER_FIELD_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = ReadonlyField::class;
        });

        // Register field for feed-me plugin if installed
        if (\Craft::$app->plugins->isPluginEnabled('feed-me')) {
            Event::on(FeedMeFields::class, FeedMeFields::EVENT_REGISTER_FEED_ME_FIELDS, function(RegisterFeedMeFieldsEvent $e) {
                    $e->fields[] = ReadonlyFeedme::class;
                }
            );
        }
    }
}
