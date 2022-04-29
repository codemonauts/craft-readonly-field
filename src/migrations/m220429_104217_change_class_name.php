<?php

namespace codemonauts\readonly\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Table;

/**
 * m220429_104217_change_class_name migration.
 */
class m220429_104217_change_class_name extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $this->getDb()->createCommand()
            ->update(Table::FIELDS, ['type' => 'codemonauts\readonly\fields\ReadonlyField'], ['type' => 'codemonauts\readonly\fields\Readonly'])
            ->execute();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m220429_104217_change_class_name cannot be reverted.\n";
        return false;
    }
}
