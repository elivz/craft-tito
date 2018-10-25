<?php
/**
 * Tito plugin for Craft CMS 3.x
 *
 * Get event information from Tito.io.
 *
 * @link      https://elivz.com
 * @copyright Copyright (c) 2018 Eli Van Zoeren
 */

namespace elivz\tito\models;

use elivz\tito\Tito;

use Craft;
use craft\base\Model;

/**
 * @author  Eli Van Zoeren
 * @package Tito
 * @since   1.0.0
 */
class TitoEvent extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $eventId = '';

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $slug;

    /**
     * @var string
     */
    public $accountId;
    
    /**
     * @var string
     */
    public $description;
    
    /**
     * @var Date
     */
    public $startDate;
    
    /**
     * @var Date
     */
    public $endDate;

    /**
     * @var bool
     */
    public $live;

    /**
     * @var bool
     */
    public $private;

    /**
     * @var bool
     */
    public $testMode;

    /**
     * @var string
     */
    public $location;

    /**
     * @var string
     */
    public $bannerUrl;

    /**
     * @var string
     */
    public $logoUrl;

    /**
     * @var array
     */
    public $releases;

    // Public Methods
    // =========================================================================

    /**
     * Get all the data about the event
     */
    public function __construct($eventId)
    {
        $this->eventId = $eventId;
        $eventData = Tito::getInstance()->api->event($eventId);

        foreach ($eventData as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Use the event id (slug) as the string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->eventId;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['eventId', 'string'],
            ['title', 'string'],
            ['slug', 'string'],
            ['accountId', 'string'],
            ['description', 'string'],
            ['startDate', 'date'],
            ['endDate', 'date'],
            ['live', 'bool'],
            ['private', 'bool'],
            ['testMode', 'bool'],
            ['location', 'string'],
            ['bannerUrl', 'string'],
            ['logoUrl', 'string'],
            ['releases', 'array'],
        ];
    }
}
