<?php
/**
 * Tito plugin for Craft CMS 3.x
 *
 * Get event information from Tito.io.
 *
 * @link      https://elivz.com
 * @copyright Copyright (c) 2018 Eli Van Zoeren
 */

namespace elivz\tito\services;

use elivz\tito\Tito;

use Craft;
use craft\base\Component;

/**
 * @author    Eli Van Zoeren
 * @package   Tito
 * @since     1.0.0
 */
class TitoService extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (Tito::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }
}
