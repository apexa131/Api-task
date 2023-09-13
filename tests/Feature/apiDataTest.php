<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use SimpleXMLElement;
use Tests\TestCase;

class apiDataTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_api_data_and_call_other_api_if_one_is_down()
    {
        $response = Http::get('https://randomuser.me/api/' );
        if($response->status() === 200){
            $this->assertTrue($response->status() === 200);
        } else {
            $responseData = Http::get('https://www.boredapi.com/api/activity');
            if($responseData->status() === 200){
                $this->assertTrue($responseData->status() === 200);
            } else{
                $this->assertTrue(false);
            }
        }
    }

    public function test_xml_format_data()
    {
        // data prepare
        $response = Http::get('https://randomuser.me/api/?results=10');
        $data = json_decode($response->getBody(), true);
        $xmlData = new SimpleXMLElement('<xml><usersXml></usersXml></xml>');

        foreach ($data['results'] as $result) {
            $userElement = $xmlData->addChild('user');
            $userElement->addChild('full_name', $result['name']['first'] . ' ' . $result['name']['last']);
            $userElement->addChild('phone', $result['phone']);
            $userElement->addChild('email', $result['email']);
            $userElement->addChild('country', $result['location']['country']);
        }

        $testXml = simplexml_load_string($xmlData->asXML());

        if ($testXml !== false && isset($testXml->usersXml)) {
            $this->assertTrue(true);
        } else {
            $this->assertCount(false);
        }
    }

    public function test_make_api_data_sorting()
    {
        $response = Http::get('https://randomuser.me/api/?results=10');
        if($response->status() === 200) {
            $data = json_decode($response->getBody(), true);
            $unSorting_data = $data['results'];
            usort($data['results'], function ($a, $b) {
                return strcasecmp($b['name']['last'], $a['name']['last']);
            });
            $this->assertNotEquals($data['results'], $unSorting_data);
        } else {
            $response2 = Http::get('https://www.boredapi.com/api/activity');
            if($response2->status() === 200) {
                $data = json_decode($response->getBody(), true);
                $unSorting_data = $data['results'];
                usort($data['results'], function ($a, $b) {
                    return strcasecmp($b['type'], $a['type']);
                });
                $this->assertNotEquals($data['results'], $unSorting_data);
            } else {
                $this->assertTrue(false);
            }
        }
    }

    public function test_count_api_data()
    {
        $response = Http::get('https://randomuser.me/api/?results=10');
        $data = json_decode($response->getBody(), true);
        $this->assertCount(10, $data['results']);
    }
}
