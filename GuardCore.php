<?php
namespace Panels;

class GuardCore
{
    private $baseUrl;
    private $token;

    public function __construct($config)
    {
        $this->baseUrl = rtrim($config['base_url'], '/');
        $this->token   = $config['api_token'];
    }

    private function request($method, $endpoint, $payload = [])
    {
        $url = "{$this->baseUrl}{$endpoint}";
        $headers = [
            "Authorization: Bearer {$this->token}",
            "Content-Type: application/json",
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        if (!empty($payload)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $response = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code >= 400) {
            throw new \Exception("Guard API Error ({$code}): {$response}");
        }

        return json_decode($response, true);
    }

    public function createSubscription($data)
    {
        return $this->request('POST', '/api/subscriptions', $data);
    }

    public function getSubscription($username)
    {
        return $this->request('GET', "/api/subscriptions/{$username}");
    }

    public function updateSubscription($username, $data)
    {
        return $this->request('PUT', "/api/subscriptions/{$username}", $data);
    }

    public function deleteSubscription($username)
    {
        return $this->request('DELETE', "/api/subscriptions", ["username" => $username]);
    }

    public function enableSubscription($username)
    {
        return $this->request('POST', "/api/subscriptions/enable", ["username" => $username]);
    }

    public function disableSubscription($username)
    {
        return $this->request('POST', "/api/subscriptions/disable", ["username" => $username]);
    }

    public function getUsages($username)
    {
        return $this->request('GET', "/api/subscriptions/{$username}/usages");
    }
}