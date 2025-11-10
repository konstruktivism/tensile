<?php

namespace App\Services;

use Carbon\Carbon;
use Google\Client;
use Google\Service\Calendar;
use Google\Service\Exception;

class GoogleCalendarService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client;
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
                echo 'Enter verification code: ';
                $authCode = config('services.google_calendar.auth_code');

                $accessToken = $this->client->fetchAccessTokenWithAuthCode($authCode);
                $this->client->setAccessToken($accessToken);

                if (! file_exists(dirname($tokenPath))) {
                    mkdir(dirname($tokenPath), 0700, true);
                }
                file_put_contents($tokenPath, json_encode($accessToken));
            }
        }
    }

    /**
     * @throws Exception
     */
    public function getEvents(int $maxResults = 32, int $days = 1, bool $includeToday = false)
    {
        $calendarId = config('services.google_calendar.calendar_id');

        $service = new Calendar($this->client);

        if ($days > 1) {
            $start = Carbon::now()->subDays($days - 1)->startOfDay()->toRfc3339String();
            $end = Carbon::now()->endOfDay()->toRfc3339String();
        } else {
            $startDate = $includeToday ? Carbon::now()->startOfDay() : Carbon::yesterday()->startOfDay();
            $endDate = $includeToday ? Carbon::now()->endOfDay() : Carbon::yesterday()->endOfDay();

            $start = $startDate->toRfc3339String();
            $end = $endDate->toRfc3339String();
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

    /**
     * Get events for a specific date range (supports past and future dates)
     *
     * @throws Exception
     */
    public function getEventsByDateRange(string $startDate, string $endDate, int $maxResults = 1000)
    {
        $calendarId = config('services.google_calendar.calendar_id');

        $service = new Calendar($this->client);

        $start = Carbon::parse($startDate)->startOfDay()->toRfc3339String();
        $end = Carbon::parse($endDate)->endOfDay()->toRfc3339String();

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
