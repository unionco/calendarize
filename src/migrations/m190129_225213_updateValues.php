<?php

namespace unionco\calendarize\migrations;

use craft\db\Migration;
use unionco\calendarize\records\CalendarizeRecord;

/**
 * m190129_225213_updateValues migration.
 */
class m190129_225213_updateValues extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Place migration code here...
        $records = CalendarizeRecord::find()
            ->all();

        if (count($records)) {
            foreach ($records as $record) {
                if (!$record->repeats) {
                    $record->endRepeat      = null;
                    $record->endRepeatDate  = null;
                    $record->repeatType     = null;
                    $record->days           = null;
                    $record->months         = null;
                    $record->timeChanges    = null;

                    $record->save();
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190129_225213_updateValues cannot be reverted.\n";
        return false;
    }
}
