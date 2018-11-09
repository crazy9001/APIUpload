<?php
/**
 * Created by PhpStorm.
 * User: PC01
 * Date: 11/9/2018
 * Time: 10:01 AM
 */

use Ejarnutowski\LaravelApiKey\Models\ApiKey;
use Illuminate\Support\Str;

function getClientId(\Illuminate\Http\Request $request)
{
    $header = $request->header('X-Authorization');
    $clientId = ApiKey::where('key', $header)->first();
    return $clientId->id;
}

function getClientName(\Illuminate\Http\Request $request)
{
    $header = $request->header('X-Authorization');
    $clientId = ApiKey::where('key', $header)->first();
    return $clientId->name;
}
