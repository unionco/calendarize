<?php
/**
 * Calendarize plugin for Craft CMS 3.x
 *
 * Calendar element types
 *
 * @link      https://union.co
 * @copyright Copyright (c) 2018 Franco Valdes
 */

namespace unionco\calendarize\records;

use craft\db\ActiveRecord;
use craft\records\Element;
use craft\records\Field;
use craft\records\Site;
use yii\db\ActiveQueryInterface;

/**
 * Class CalendarizeRecord
 *
 * @property int    $id             ID
 * @property int    $ownerId        Owner ID
 * @property int    $ownerSiteId    Owner Site ID
 * @property int    $fieldId        Field ID
 * @property float  $lat            Latitude
 * @property float  $lng            Longitude
 * @property int    $zoom           Zoom
 * @property string $address        Address
 * @property array  $parts          Address Parts
 *
 * @package ether\SimpleMap\records
 */
class CalendarizeRecord extends ActiveRecord
{

	// Props
	// =========================================================================

	// Props: Public Static
	// -------------------------------------------------------------------------

	/** @var string */
	public static $tableName = '{{%calendarize}}';

	// Public Methods
	// =========================================================================

	// Public Methods: Static
	// -------------------------------------------------------------------------

	/**
	 * @inheritdoc
	 *
	 * @return string
	 */
	public static function tableName(): string
	{
		return self::$tableName;
	}

	// Public Methods: Instance
	// -------------------------------------------------------------------------

	/**
	 * Returns the map's owner
	 *
	 * @return ActiveQueryInterface - The relational query object
	 */
	public function getOwner(): ActiveQueryInterface
	{
		return $this->hasOne(Element::class, ['id' => 'ownerId']);
	}

	/**
	 * Returns the map's owner's site
	 *
	 * @return ActiveQueryInterface - The relational query object
	 */
	public function getOwnerSite(): ActiveQueryInterface
	{
		return $this->hasOne(Site::class, ['id' => 'ownerSiteId']);
	}

	/**
	 * Returns the map's field
	 *
	 * @return ActiveQueryInterface - The relational query object
	 */
	public function getField(): ActiveQueryInterface
	{
		return $this->hasOne(Field::class, ['id' => 'fieldId']);
	}

}