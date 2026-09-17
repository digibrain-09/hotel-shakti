<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

require_once app_path('GuzzleHttp/src/Client.php');
use GuzzleHttp\Client; // Load Guzzle manually

class ZomatoController extends Controller
{
    public function getCategories()
    {
        $client = new Client();

        $response = $client->request('GET', 'https://developers.zomato.com/api/v2.1/categories', [
            'headers' => [
                'user-key' => 'ipsum sunt labore ex',
                'Accept' => 'application/json',
            ],
        ]);

        $menu = json_decode($response->getBody(), true);
        return response()->json($menu);
    }
}
