<?php

namespace App\Services\Location;

use Illuminate\Support\Fluent;
use Stevebauman\Location\Drivers\HttpDriver;
use Stevebauman\Location\Position;

class IpApiFull extends HttpDriver
{
    /**
     * Get the URL for the driver.
     */
    public function url(string $ip): string
    {
        return "http://ip-api.com/json/$ip?fields=status,message,continent,continentCode,country,countryCode,region,regionName,city,zip,lat,lon,timezone,currency,isp,org,as,query";
    }

    /**
     * Hydrate the position from the location.
     */
    protected function hydrate(Position $position, Fluent $location): Position
    {
        $position->continentName = $location->continent;
        $position->continentCode = $location->continentCode;
        $position->countryName = $location->country;
        $position->countryCode = $location->countryCode;
        $position->regionCode = $location->region;
        $position->regionName = $location->regionName;
        $position->cityName = $location->city;
        $position->zipCode = $location->zip;
        $position->latitude = (string) $location->lat;
        $position->longitude = (string) $location->lon;
        $position->timezone = $location->timezone;
        $position->currencyCode = $location->currency;

        // Custom property not in base Position class but we can store it
        $position->isp = $location->isp;

        return $position;
    }
}
