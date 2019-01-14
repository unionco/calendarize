<?php

namespace unionco\calendarize\migrations;

use Craft;
use craft\db\Migration;
use unionco\calendarize\records\CalendarizeRecord;

/**
 * m190114_215609_updateColumnMigration migration.
 */
class m190114_215609_updateColumnMigration extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Place migration code here...
        $this->alterColumn(CalendarizeRecord::$tableName, 'startDate', $this->dateTime());
        $this->alterColumn(CalendarizeRecord::$tableName, 'endDate', $this->dateTime());
        $this->alterColumn(CalendarizeRecord::$tableName, 'endRepeatDate', $this->dateTime());
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190114_215609_updateColumnMigration cannot be reverted.\n";
        return false;
    }
}
