<?php

/**
 * Lab-Seeder für readonly.
 */

use codemonauts\readonly\fields\ReadonlyField;
use craft\console\Controller;
use craft\elements\Entry;
use craft\fieldlayoutelements\CustomField;
use craft\models\FieldLayout;
use craft\models\FieldLayoutTab;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use yii\console\ExitCode;

return function (Controller $c): int {
    $fieldsService = Craft::$app->getFields();

    // 1) Create new readonly field
    $field = $fieldsService->getFieldByHandle('testField');
    if (!$field) {
        $groups = $fieldsService->getAllGroups();
        $field = new ReadonlyField();
        $field->groupId = $groups[0]->id ?? null;
        $field->name = 'Test Field';
        $field->handle = 'testField';
        $field->instructions = 'Test Instruction';
        $field->searchable = true;
        $field->columnType = 'string';
        $field->template = 'Prefix {{ value }} of {{ element.title }} Suffix.';
        if (!$fieldsService->saveField($field)) {
            $c->stderr('Failed to save field: ' . print_r($field->getErrors(), true) . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $c->stdout("Created field testField\n");
    } else {
        $c->stdout("Field testField already exists\n");
    }

    // Create section with _playground.twig
    $sectionsService = Craft::$app->getSections();
    $section = $sectionsService->getSectionByHandle('test');
    if (!$section) {
        $primarySite = Craft::$app->getSites()->getPrimarySite();

        $section = new Section([
            'name' => 'Test',
            'handle' => 'test',
            'type' => Section::TYPE_CHANNEL,
            'enableVersioning' => true,
            'siteSettings' => [
                new Section_SiteSettings([
                    'siteId' => $primarySite->id,
                    'enabledByDefault' => true,
                    'hasUrls' => true,
                    'uriFormat' => 'test/{slug}',
                    'template' => '_playground.twig',
                ]),
            ],
        ]);

        if (!$sectionsService->saveSection($section)) {
            $c->stderr('Failed to save section: ' . print_r($section->getErrors(), true) . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $entryType = $sectionsService->getEntryTypesBySectionId($section->id)[0];
        $layout = new FieldLayout(['type' => Entry::class]);
        $tab = new FieldLayoutTab(['name' => 'Content', 'layout' => $layout]);
        $tab->setElements([new CustomField($field)]);
        $layout->setTabs([$tab]);
        $entryType->setFieldLayout($layout);

        if (!$sectionsService->saveEntryType($entryType)) {
            $c->stderr('Failed to save entry type: ' . print_r($entryType->getErrors(), true) . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $c->stdout("Created section test\n");
    } else {
        $c->stdout("Section test already exists\n");
    }

    // 3) Create entry with field value
    $existing = Entry::find()->section('test')->title('Seed Entry')->one();
    if (!$existing) {
        $entryType = $sectionsService->getEntryTypesBySectionId($section->id)[0];
        $entry = new Entry();
        $entry->sectionId = $section->id;
        $entry->typeId = $entryType->id;
        $entry->title = 'Seed Entry';
        $entry->slug = 'seed-entry';
        $entry->setFieldValue('testField', 'Seed value');
        if (!Craft::$app->getElements()->saveElement($entry)) {
            $c->stderr('Failed to save entry: ' . print_r($entry->getErrors(), true) . "\n");
            return ExitCode::UNSPECIFIED_ERROR;
        }
        $c->stdout("Created entry 'Seed Entry' with testField value\n");
    } else {
        $c->stdout("Entry 'Seed Entry' already exists\n");
    }

    $c->stdout("Seed complete.\n");
    return ExitCode::OK;
};
