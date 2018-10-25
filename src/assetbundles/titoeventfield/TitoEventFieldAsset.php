<?php
/**
 * Tito plugin for Craft CMS 3.x
 *
 * Get event information from Tito.io.
 *
 * @link      https://elivz.com
 * @copyright Copyright (c) 2018 Eli Van Zoeren
 */

namespace elivz\tito\assetbundles\titoeventfield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author  Eli Van Zoeren
 * @package Tito
 * @since   1.0.0
 */
class TitoEventFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@elivz/tito/assetbundles/titoeventfield/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/TitoEvent.js',
        ];

        $this->css = [
            'css/TitoEvent.css',
        ];

        parent::init();
    }
}
