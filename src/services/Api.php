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
     * Normalize data for an event
     * 
     * @param array $event The raw event data from Tito's API
     * 
     * @return array
     */
    private function _formatEvent($event, $removeHidden = true)
    {
        $data = array_merge(
            [
                'id' => $event['id'],
                'url' => $this->_eventUrl($event['id']),
            ],
            $event['attributes']
        );

        if ($removeHidden && ($data['private'] ||!$data['live'])) {
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
        $events = json_decode($response->getBody()->getContents(), true);
        $events = array_filter(array_map([$this, '_formatEvent'], $events['data']));
        usort(
            $events, function ($a, $b) {
                return strtotime($a['start-date']) - strtotime($b['start-date']);
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
        $events = $this->events();
        $events = array_reduce(
            $events, function ($result, $event) {
                $result[$event['id']] = $event['title'] . ' (' . $event['start-date'] . ')';
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
    public function event()
    {
        $client = $this->_getApiClient();

        $response = $client->get('event');
        $event = json_decode($response->getBody()->getContents(), true);
        $event = $this->_formatEvent($event['data'], false);

        return $event;
    }
}
