<?php
/**
 * Calendarize plugin for Craft CMS 3.x
 *
 * Calendar element types
 *
 * @link      https://union.co
 * @copyright Copyright (c) 2018 Franco Valdes
 */

namespace unionco\calendarize\migrations;

use craft\db\Migration;
use unionco\calendarize\records\CalendarizeRecord;

class Install extends Migration
{

	public function safeUp ()
	{
		// 1. Create new table
		// ---------------------------------------------------------------------

		// Table

		$this->createTable(
			CalendarizeRecord::$tableName,
			[
				'id'          => $this->primaryKey(),
				'ownerId'     => $this->integer()->notNull(),
				'ownerSiteId' => $this->integer()->notNull(),
				'fieldId'     => $this->integer()->notNull(),

				'startDate' => $this->dateTime(),
				'endDate' => $this->dateTime(),
				'allDay' => $this->boolean()->defaultValue(false),
				'repeats' => $this->boolean()->defaultValue(false),
				'days' => $this->text(),
				'months' => $this->text(),
				'endRepeat' => $this->string(255),
				'endRepeatDate' => $this->dateTime(),
				'exceptions' => $this->text(),
				'timeChanges' => $this->text(),
				'repeatType' => $this->string(255),

				'dateCreated' => $this->dateTime()->notNull(),
				'dateUpdated' => $this->dateTime()->notNull(),
				'uid'         => $this->uid()->notNull(),
			]
		);

		// Indexes

		$this->createIndex(
			null,
			CalendarizeRecord::$tableName,
			['ownerId', 'ownerSiteId', 'fieldId'],
			true
		);

		// Relations

		$this->addForeignKey(
			null,
			CalendarizeRecord::$tableName,
			['ownerId'],
			'{{%elements}}',
			['id'],
			'CASCADE',
			null
		);

		$this->addForeignKey(
			null,
			CalendarizeRecord::$tableName,
			['ownerSiteId'],
			'{{%sites}}',
			['id'],
			'CASCADE',
			'CASCADE'
		);

		$this->addForeignKey(
			null,
			CalendarizeRecord::$tableName,
			['fieldId'],
			'{{%fields}}',
			['id'],
			'CASCADE',
			'CASCADE'
		);

		return true;
	}

	public function safeDown ()
	{
		$this->dropTableIfExists(CalendarizeRecord::$tableName);

		// TODO: Should we handle moving the data back to the old table, or
		// TODO(cont.): will Craft handle that using a backup?

		return true;
	}

}