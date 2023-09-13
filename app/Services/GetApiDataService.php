<?php

namespace App\Services;

use GuzzleHttp\Client;
use SimpleXMLElement;

class GetApiDataService
{
    public function ApiData(string $limit): array
    {
        $getData = new Client();
        $user_details = [];
        $activity_details = [];

        for ($i = 0; $i < $limit; $i++) {
            $response = $getData->get('https://randomuser.me/api/');
            $data = json_decode($response->getBody(), true);
            if(isset($data['results'])){
                $user = $data['results'][0];
                $fullName = $user['name']['first'] . ' ' . $user['name']['last'];
                $user_details[] = [
                    'full_name' => $fullName ?? $fullName,
                    'phone' => $user['phone'] ?? $user['phone'],
                    'email' => $user['email'] ?? $user['email'],
                    'country' => $user['location']['country'] ?? $user['location']['country'],
                ];
            }
            else {
                $response = $getData->get('https://www.boredapi.com/api/activity');
                $activity_details[] = json_decode($response->getBody(), true);
            }
        }
        return ['userDetails' => $user_details, 'activityDetails' => $activity_details];
    }
}
