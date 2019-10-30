<?php

declare(strict_types = 1);

namespace App\Service;

use App\Entity\Booking;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Cache\Adapter\PdoAdapter;

class GoogleCalendarService
{
    public const CALENDAR_ID = 'primary';
    /**
     * @var \Google_Client
     */
    protected $client;
    /**
     * @var PdoAdapter
     */
    protected $cache;

    /**
     * @var \Google_Service_Calendar
     */
    protected $service;

    /**
     * @var string
     */
    protected $scopes;
    /**
     * @var string
     */
    protected $redirectUri;
    /**
     * @var array
     */
    protected $parameters = [];
    /**
     * @var CacheItemInterface
     */
    protected $accessToken;
    /**
     * @var CacheItemInterface
     */
    protected $refreshToken;
    /**
     * @var bool
     */
    protected $fromFile = true;
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $authUrl;

    /**
     */
    public function __construct(EntityManagerInterface $em)
    {
        $connection = $em->getConnection();
        $this->cache = new PdoAdapter($connection, 'calendar', 0);
        $this->accessToken = $this->cache->getItem('access_token');
        $this->refreshToken = $this->cache->getItem('refresh_token');

    }

    /**
     * @param null $authCode
     *
     * @return \Google_Client|null
     */
    public function getClient($authCode = null): ?\Google_Client
    {
        if (!$this->client) {
//        configuration
            $this->client = new \Google_Client();
            $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/booking/code/';
            $this->client->setAccessType('offline');
            $this->client->setAuthConfig(__DIR__ . '/../../credentials.json');
            $this->client->setRedirectUri($redirect_uri);
            $this->client->addScope(\Google_Service_Calendar::CALENDAR);
            $this->client->setIncludeGrantedScopes(true);
//         getting access token
            if (!$this->accessToken->isHit()) {
                // Request authorization from the user.
                if ($this->redirectUri) {
                    $this->client->setRedirectUri($this->redirectUri);
                }
                if ($authCode !== null) {
                    $this->accessToken->set($this->client->fetchAccessTokenWithAuthCode($authCode));
                    $this->cache->save($this->accessToken);
                } else {
                    $this->authUrl = $this->client->createAuthUrl();
                    return null;
                }
            }
            $this->client->setAccessToken($this->accessToken->get());
            // getting refresh token
            if ($this->client->getRefreshToken()) {
                $this->refreshToken->set($this->client->getRefreshToken());
                $this->cache->save($this->refreshToken);
            }
        }
        // Refresh the token if it's expired.
        if ($this->client->isAccessTokenExpired()) {
            if (!$this->refreshToken->isHit()) {
                $this->refreshToken->set($this->client->getRefreshToken());
                $this->cache->save($this->refreshToken);
            }
            if ($this->refreshToken->isHit()) {
                $res = $this->client->fetchAccessTokenWithRefreshToken($this->refreshToken->get());
                if (!isset($res['access_token'])) {
                    $this->authUrl = $this->client->createAuthUrl();
                    return null;
                }
                $this->accessToken->set($this->client->getAccessToken());
                $this->cache->save($this->accessToken);
            } else {
                $this->accessToken->set(null);
                $this->cache->save($this->accessToken);
                $this->authUrl = $this->client->createAuthUrl();
                return null;
            }
        }
        $this->service = new \Google_Service_Calendar($this->client);
        return $this->client;
    }

    /**
     * @return array|null
     */
    public function getAccessToken(): ?array
    {
        if (!$this->accessToken->isHit()) {
            return null;
        }
        return $this->accessToken->get();
    }

    /**
     * @return string
     */
    public function getAuthUrl(): string
    {
        return $this->authUrl;
    }

    /**
     * @param Booking $booking
     *
     * @return \Google_Service_Calendar_Event
     */
    public function addEvent(Booking $booking): \Google_Service_Calendar_Event
    {
        $event = new \Google_Service_Calendar_Event([
            'summary' => $booking->getSummary(),
            'location' => $booking->getLocation(),
            'description' => $booking->getDescription(),
            'start' => [
                'dateTime' => $booking->getStart()->format('Y-m-d\TH:i:sP'),
            ],
            'end' => [
                'dateTime' => $booking->getEnd()->format('Y-m-d\TH:i:sP'),
            ],
        ]);
        return $this->getService()->events->insert(self::CALENDAR_ID, $event, ['sendNotifications' => true]);
    }

    /**
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $endDate
     *
     * @return array
     */
    public function getEventsBetweenDates(\DateTimeInterface $startDate, \DateTimeInterface $endDate): array

    {
        $optParams = [
            'orderBy' => 'startTime',
            'singleEvents' => true,
            'timeMin' => $startDate->format('Y-m-d\TH:i:sP'),
            'timeMax' => $endDate->format('Y-m-d\TH:i:sP'),
        ];
        $results = $this->getService()->events->listEvents(self::CALENDAR_ID, $optParams);

        return $results->getItems();
    }

    /**
     * @param Booking $booking
     */
    public function editEvent(Booking $booking): void
    {
        $event = new \Google_Service_Calendar_Event([
            'summary' => $booking->getSummary(),
            'location' => $booking->getLocation(),
            'description' => $booking->getDescription(),
            'start' => [
                'dateTime' => $booking->getStart()->format('Y-m-d\TH:i:sP'),
            ],
            'end' => [
                'dateTime' => $booking->getEnd()->format('Y-m-d\TH:i:sP'),
            ],
        ]);
        $this->getService()->events->update(self::CALENDAR_ID, $booking->getGoogleId(), $event);
    }

    /**
     * @param $eventId
     */
    public function deleteEvent($eventId): void
    {
        $this->getService()->events->delete(self::CALENDAR_ID, $eventId);
    }

    /**
     * @return \Google_Service_Calendar
     */
    public function getService(): \Google_Service_Calendar
    {
        if (!$this->service) {
            $this->getClient();
        }
        return $this->service;
    }

    /**
     * @param mixed $service
     */
    public function setService($service): void
    {
        $this->service = $service;
    }
}
