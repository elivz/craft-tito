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
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $apiToken = '';
    public $accountSlug = '';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['apiToken', 'string'],
            ['apiToken', 'default', 'value' => ''],
            ['accountSlug', 'string'],
            ['accountSlug', 'default', 'value' => ''],
        ];
    }
}
