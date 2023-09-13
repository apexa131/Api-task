<?php

namespace App\Http\Controllers;

use App\Services\GetApiDataService;
use SimpleXMLElement;

class GetApiDataController extends Controller
{
    public function __construct(protected GetApiDataService $GetApiDataService)
    {
    }

    public function getApiData(){
        $requestApiData = $this->GetApiDataService->ApiData(10);
        $xmlData = $this->apiXmlData($requestApiData);
        return response($xmlData, 200)->header('Content-Type', 'application/xml');
    }

    public function apiXmlData($requestApiData)
    {
        if(!empty($requestApiData['userDetails'])) {
            usort($requestApiData['userDetails'], function ($a, $b) {
                return strcasecmp($b['full_name'], $a['full_name']);
            });

            $xmlData = new SimpleXMLElement('<usersXml></usersXml>');
            foreach ($requestApiData['userDetails'] as $userData) {
                $userElement = $xmlData->addChild('user');
                foreach ($userData as $key => $value) {
                    $userElement->addChild($key, $value);
                }
            }
        }
        elseif(!empty($requestApiData['activityDetails'])) {
            usort($requestApiData['activityDetails'], function ($a, $b) {
                return strcasecmp($b['type'], $a['type']);
            });

            $xmlData = new SimpleXMLElement('<activityXml></activityXml>');
            foreach ($requestApiData['activityDetails'] as $activityData) {
                $activityElement = $xmlData->addChild('activity');
                foreach ($activityData as $key => $value) {
                    $activityElement->addChild($key, $value);
                }
            }
        }
        return $xmlData->asXML();
    }
}
