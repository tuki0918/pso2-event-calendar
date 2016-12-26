<?php

namespace App\GoogleApi;

use Google_Service_Calendar_Event;

interface GoogleCalendarApiInterface
{
    /**
     * 指定したカレンダーに登録されている予定を削除する
     * @param string $calendarId
     * @param string $calendarCreator
     */
    public function clear(string $calendarId, string $calendarCreator): void;

    /**
     * @param string $calendarId
     * @param Google_Service_Calendar_Event $event
     * @return Google_Service_Calendar_Event
     */
    public function insert(string $calendarId, Google_Service_Calendar_Event $event): Google_Service_Calendar_Event;
}
