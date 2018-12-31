<?php
/**
 * Calendarize plugin for Craft CMS 3.x
 *
 * Calendar element types
 *
 * @link      https://union.co
 * @copyright Copyright (c) 2018 Franco Valdes
 */

namespace unionco\calendarize\fields;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use craft\helpers\Json;
use DateTime;
use unionco\calendarize\assetbundles\fieldbundle\FieldAssetBundle;
use unionco\calendarize\Calendarize;
use unionco\calendarize\models\CalendarizeModel;
use yii\db\Schema;

/**
 * @author    Franco Valdes
 * @package   Calendarize
 * @since     1.0.0
 */
class CalendarizeField extends Field implements PreviewableFieldInterface
{
    // Public Properties
    // =========================================================================

    /**
     * @var datetime
     */
    public $startDate;

    /**
     * @var datetime
     */
    public $endDate;

    /**
     * @var boolean
     */
    public $repeats = false;

    /**
     * @var boolean
     */
    public $allDay = false;
    
    /**
     * @var array
     */
    public $days = [];

    /**
     * @var string
     */
    public $endRepeat = 'never';

    /**
     * @var datetime
     */
    public $endRepeatDate;

    /**
     * @var array
     */
    public $exceptions = [];

    /**
     * @var array
     */
    public $timeChanges = [];

    /**
     * @var string
     */
    public $repeatType = 'week';

    /**
     * @var string
     */
    public $months = null;

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('calendarize', 'Calendarize');
    }

    /**
	 * @inheritdoc
	 */
	public static function hasContentColumn(): bool
	{
		return false;
    }
    
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return Calendarize::$plugin->calendar->getField($this, $element, $value);
    }

    /**
	 * @inheritdoc
	 */
	public function modifyElementsQuery(ElementQueryInterface $query, $value)
	{
		// For whatever reason, this function can be
		// run BEFORE Calendarize has been initialized
		if (!Calendarize::$plugin) {
            return null;
        }

		Calendarize::$plugin->calendar->modifyElementsQuery($query, $value);

		return null;
    }

    /**
	 * @inheritdoc
	 */
	public function afterElementSave(ElementInterface $element, bool $isNew)
	{
		Calendarize::$plugin->calendar->saveField($this, $element);
		parent::afterElementSave($element, $isNew);
    }
    
    // /**
    //  * @inheritdoc
    //  */
    // public function getSettingsHtml()
    // {
    //     // Render the settings template
    //     return Craft::$app->getView()->renderTemplate(
    //         'calendarize/_components/fields/CalendarizeField_settings',
    //         [
    //             'field' => $this,
    //         ]
    //     );
    // }

    /**
     * @inheritdoc
     */
    public function getTableAttributeHtml($value, ElementInterface $element): string
    {
        if (empty($value->startDate) && empty($value->endDate)) {
            return '-';
        }
        
        $hr = $value->rrule()->getRRules()[0]->humanReadable();
        $html = "<span title=\"{$hr}\">";
        
        if ($value->hasPassed()) {
            $html .= "<b>Last Occurence:</b>";
        } else {
            $html .= "<b>Next Occurence:</b>";
        }

        $html .= "<br/>" . $value->next()->format('l, m/d/Y @ h:i:s a');

        return $html;
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(FieldAssetBundle::class);

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        // $jsonVars = [
        //     'id' => $id,
        //     'name' => $this->handle,
        //     'namespace' => $namespacedId,
        //     'prefix' => Craft::$app->getView()->namespaceInputId(''),
        //     ];
        // $jsonVars = Json::encode($jsonVars);
        // Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').CalendarizeCalendarizeField(" . $jsonVars . ");");

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'calendarize/_components/fields/CalendarizeField_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
            ]
        );
    }
}
