<?php

namespace AtlassianConnectLaravel\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LifecycleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'key' => 'required|string|max:255',
            'clientKey' => 'required|string|max:255',
            'publicKey' => 'required|string|max:255',
            'serverVersion' => 'required|string|max:255',
            'pluginsVersion' => 'required|string|max:255',
            'baseUrl' => 'required|string|max:255',
            'productType' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'eventType' => 'required|string|max:255',
        ];
    }
}
