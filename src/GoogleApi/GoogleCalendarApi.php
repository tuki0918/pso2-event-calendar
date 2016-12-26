<?php

namespace App\GoogleApi;

use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class GoogleCalendarApi extends GoogleApi implements GoogleCalendarApiInterface
{
    /** @var Google_Service_Calendar */
    private $service;

    /**
     * GoogleCalendarApi constructor.
     * @param Google_Client $client
     */
    public function __construct(Google_Client $client)
    {
        parent::__construct($client);
        $this->service = new Google_Service_Calendar($this->client());
    }

    /**
     * 指定したカレンダーに登録されている予定を削除する
     * @param string $calendarId
     * @param string $calendarCreator
     */
    public function clear(string $calendarId, string $calendarCreator): void
    {
        $events = $this->service->events->listEvents($calendarId);
        while (true) {
            /** @var Google_Service_Calendar_Event $event */
            foreach ($events->getItems() as $event) {
                // イベント作成者が指定した者と等しい場合のみ削除する
                if ($event->getCreator()->email === $calendarCreator) {
                    $this->service->events->delete($calendarId, $event->id);
                }
            }

            $pageToken = $events->getNextPageToken();
            if ($pageToken) {
                $optParams = ['pageToken' => $pageToken];
                $events = $this->service->events->listEvents($calendarId, $optParams);
            } else {
                break;
            }
        }
    }

    /**
     * @param string $calendarId
     * @param Google_Service_Calendar_Event $event
     * @return Google_Service_Calendar_Event
     */
    public function insert(string $calendarId, Google_Service_Calendar_Event $event): Google_Service_Calendar_Event
    {
        return $this->service->events->insert($calendarId, $event);
    }
}
