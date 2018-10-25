<?php
/**
 * Tito plugin for Craft CMS 3.x
 *
 * Get event information from Tito.io.
 *
 * @link      https://elivz.com
 * @copyright Copyright (c) 2018 Eli Van Zoeren
 */

namespace elivz\tito\fields;

use elivz\tito\Tito;
use elivz\tito\assetbundles\titoeventfield\TitoEventFieldAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;
use craft\web\assets\selectize\SelectizeAsset;

/**
 * @author  Eli Van Zoeren
 * @package Tito
 * @since   1.0.0
 */
class TitoEvent extends Field
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $eventId;

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('tito', 'Tito Event');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge(
            $rules, [
            ['eventId', 'string'],
            ]
        );
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Register our asset bundle
        Craft::$app->getView()->registerAssetBundle(TitoEventFieldAsset::class);

        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Get all the upcoming events
        $options = array_merge(['' => ''], Tito::getInstance()->api->eventTitles());

        // Initialize the Selectize searchable drop-down
        Craft::$app->getView()->registerJs(
            "$('#{$namespacedId}-field .tito-event-field').selectize({allowEmptyOption:true});"
        );

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'tito/_components/fields/TitoEvent_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'options' => $options,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
            ]
        );
    }
}
