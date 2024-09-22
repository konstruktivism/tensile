<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setApplicationName('Google Calendar API Laravel Integration');
        $this->client->setScopes(Calendar::CALENDAR_READONLY);
        $this->client->setAuthConfig(storage_path('app/client_secret_1011594109685-r98negli2lau90uvlkqs63rm972ra0lp.apps.googleusercontent.com.json'));
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');

        $this->loadToken();
    }

    protected function loadToken()
    {
        $tokenPath = storage_path('app/token.json');
        if (file_exists($tokenPath)) {
            $accessToken = json_decode(file_get_contents($tokenPath), true);
            $this->client->setAccessToken($accessToken);
        }

        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->getRefreshToken()) {
                $this->client->fetchAccessTokenWithRefreshToken($this->client->getRefreshToken());
            } else {
                $authUrl = $this->client->createAuthUrl();
                printf("Open the following link in your browser:\n%s\n", $authUrl);
                print 'Enter verification code: ';
                $authCode = config('services.google_calendar.auth_code');

                $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
                $this->client->setAccessToken($accessToken);

                if (!file_exists(dirname($tokenPath))) {
                    mkdir(dirname($tokenPath), 0700, true);
                }
                file_put_contents($tokenPath, json_encode($accessToken));
            }
        }
    }

public function getEvents($calendarId = null, $maxResults = 10)
    {
        $calendarId = $calendarId ?? config('services.google_calendar.calendar_id');

        $service = new Calendar($this->client);
        $optParams = [
            'maxResults' => $maxResults,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => date('c', strtotime('00:00 -1 day')),
            'timeMax' => date('c'),
        ];
        $results = $service->events->listEvents($calendarId, $optParams);
        return $results->getItems();
    }
}
