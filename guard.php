<?php

class guard
{
    private $base_url;
    private $token;

    public function __construct($panel)
    {
        $this->base_url = rtrim($panel['panel_url'], '/');
        $this->token    = $panel['panel_key'];
    }

    private function request($method, $endpoint, $data = null)
    {
        $ch = curl_init($this->base_url . $endpoint);

        $headers = [
            "Authorization: Bearer {$this->token}",
            "Content-Type: application/json"
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_HTTPHEADER     => $headers,
        ]);

        if ($data !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($status >= 400) {
            return [
                'status' => false,
                'msg' => $response
            ];
        }

        return json_decode($response, true);
    }

    public function create($user)
    {
        return $this->request("POST", "/api/subscriptions", [
            "username"   => $user['username'],
            "service_id" => $user['service_id'],
            "expire"     => $user['expire']
        ]);
    }

    public function renew($user)
    {
        return $this->request("PUT", "/api/subscriptions/" . $user['username'], [
            "expire" => $user['expire']
        ]);
    }

    public function delete($user)
    {
        return $this->request("DELETE", "/api/subscriptions", [
            "username" => $user['username']
        ]);
    }

    public function usage($user)
    {
        return $this->request(
            "GET",
            "/api/subscriptions/" . $user['username'] . "/usages"
        );
    }
}