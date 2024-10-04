<?php

namespace App\Services;

use Google\Client;
use Google\Service\Calendar;
use Google\Service\Exception;
use Carbon\Carbon;

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

    /**
     * @throws Exception
     */
    public function getEvents(int $maxResults = 32, int $days = 1)
    {
        $calendarId = config('services.google_calendar.calendar_id');

        $service = new Calendar($this->client);

        if($days > 1) {
            $start = Carbon::now()->subMonth()->firstOfMonth()->startOfDay()->toRfc3339String();
            $end = Carbon::now()->endOfDay()->toRfc3339String();
        } else {
            $start = date('c', strtotime('yesterday 00:00:00'));
            $end = date('c', strtotime('yesterday 23:59:59'));
        }

        $optParams = [
            'maxResults' => $maxResults,
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $start,
            'timeMax' => $end,
        ];
        $results = $service->events->listEvents($calendarId, $optParams);

        return $results->getItems();
    }
}
