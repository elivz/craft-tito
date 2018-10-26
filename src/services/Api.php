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
use craft\helpers\StringHelper;
use craft\i18n\Locale;
use \GuzzleHttp\Client;

/**
 * @author  Eli Van Zoeren
 * @package Tito
 * @since   1.0.0
 */
class Api extends Component
{
    // Private Properties
    // =========================================================================

    /**
     * @var string
     */
    var $apiBase = 'https://api.tito.io/v2/';

    // Private Methods
    // =========================================================================

    /**
     * Create the Guzzle instance
     * 
     * @return \GuzzleHttp\Client
     */
    private function _getApiClient()
    {
        $apiToken = Tito::$plugin->getSettings()->apiToken;
        $accountSlug = Tito::$plugin->getSettings()->accountSlug;
        $apiBase = $this->apiBase . $accountSlug . '/';

        return Craft::createGuzzleClient(
            [
                'base_uri' => $apiBase,
                'headers' => [
                    'Accept' => 'application/vnd.api+json',
                    'Authorization' => 'Token token=' . $apiToken,
                    'Cache-Control' => 'no-cache',
                ],
            ]
        );
    }

    /**
     * Generate the URL for an event
     * 
     * @param string $id The event ID 
     * 
     * @return string
     */
    private function _eventUrl($id)
    {
        $accountSlug = Tito::$plugin->getSettings()->accountSlug;
        $url = 'https://ti.to/' . $accountSlug . '/' . $id;
        return $url;
    }

    /**
     * Normalize data for an event or release
     * 
     * @param array $item The raw data from Tito's API
     * 
     * @return array
     */
    private function _formatData($item, $removeHidden = true)
    {
        $data = [
            'id' => $item['id'],
            'url' => $this->_eventUrl($item['id']),
        ];
        
        foreach ($item['attributes'] as $key => $value) {
            $camelKey = StringHelper::camelCase($key);
            if (in_array($camelKey, ['startDate', 'endDate']) || $camelKey === 'startAt') {
                $value = new \DateTime($value);
            }
            $data[$camelKey] = $value;
        }
        
        if ($removeHidden && ((isset($data['private']) && $data['private'] === true)  
        || (isset($data['live']) && $data['live'] === false) 
        || (isset($data['archived']) && $data['archived'] === true) 
        || (isset($data['secret']) && $data['secret'] === true) 
        || (isset($data['state']) && $data['state'] !== 'on_sale')            )
        ) {
            return false;
        }

        return $data;
    }

    // Public Methods
    // =========================================================================

    /**
     * Get an array of all upcoming events
     * 
     * @return array
     */
    public function events()
    {
        $client = $this->_getApiClient();

        $response = $client->get('events');
        $response = json_decode($response->getBody()->getContents(), true);
        $events = array_filter(array_map([$this, '_formatData'], $response['data']));
        usort(
            $events, function ($a, $b) {
                return $a['startDate'] > $b['startDate'] ? 1 : -1;
            }
        );

        return $events;
    }

    /**
     * Get an array of just event IDs and titles
     * 
     * @return array
     */
    public function eventTitles()
    {

        $formatter = Craft::$app->getFormatter();

        $events = $this->events();
        $events = array_reduce(
            $events, function ($result, $event) use ($formatter) {
                $date = $formatter->asDate($event['startDate'], Locale::LENGTH_SHORT);
                $result[$event['id']] = $event['title'] . ' (' . $date . ')';
                return $result;
            }, []
        );

        return $events;
    }

    /**
     * Get a single event
     * 
     * @return array
     */
    public function event($eventId)
    {
        $client = $this->_getApiClient();

        $response = $client->get($eventId . '?include=releases');
        $response = json_decode($response->getBody()->getContents(), true);
        $event = $this->_formatData($response['data'], false);

        if (!empty($response['included'])) {
            $event['releases'] = array_filter(array_map([$this, '_formatData'], $response['included']));
        }

        return $event;
    }
}
