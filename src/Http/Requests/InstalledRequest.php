<?php

namespace AtlassianConnectLaravel\Http\Requests;

class InstalledRequest extends LifecycleRequest
{
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                'clientKey' => 'required|string|max:255|unique:tenants,client_key',
                'oauthClientId' => 'required|string|max:255',
                'sharedSecret' => 'required|string|max:255',
            ]
        );
    }
}
