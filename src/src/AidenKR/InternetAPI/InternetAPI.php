<?php

declare(strict_types=1);

namespace AidenKR\InternetAPI;

use function strlen;
use function in_array;
use function str_contains;
use function strtoupper;
use function curl_init;
use function curl_setopt;
use function curl_exec;
use function curl_getinfo;
use function curl_error;
use function curl_errno;
use function curl_setopt_array;
use function json_encode;
use function json_decode;
use function http_build_query;
use const CURLOPT_URL;
use const CURLOPT_RETURNTRANSFER;
use const CURLOPT_POSTFIELDS;
use const CURLOPT_TIMEOUT;
use const CURLOPT_FOLLOWLOCATION;
use const CURLOPT_CUSTOMREQUEST;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_UNICODE;
use const CURLOPT_HTTPHEADER;


final class InternetAPI
{
    public const INTERNET_POST = "POST";
    public const INTERNET_GET = "GET";
    public const INTERNET_DELETE = "DELETE";
    public const INTERNET_PUT = "PUT";
    public const INTERNET_PATCH = "PATCH";

    private static function request(string $method, string $url, array $params = [], array $headers = [], ?InternetOptions $options = null): array
    {
        $curl = curl_init();
        $method = strtoupper($method);

        if ($method === self::INTERNET_GET && !empty($params)) {
            $url .= (str_contains($url, "?") ? "&" : "?") . http_build_query($params);
        }

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_TIMEOUT => $options?->getTimeout() ?? 50,
        ]);

        if (in_array($method, [self::INTERNET_POST, self::INTERNET_DELETE, self::INTERNET_PUT, self::INTERNET_PATCH])) {
            $data = json_encode($params, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $headers[] = "Content-Type: application/json";
            $headers[] = "Content-Length: " . strlen($data);
        }

        if (!empty($headers)) {
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }
        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new \Exception(curl_error($curl));
        }
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        return [
            'status' => $status,
            'body' => json_decode($response, true),
            "raw_response" => $response,
        ];
    }

    public static function get(string $url, array $params = [], array $headers = [], ?InternetOptions $options = null): array
    {
        return self::request(self::INTERNET_GET, $url, $params, $headers, $options);
    }

    public static function post(string $url, array $data = [], array $headers = []): array
    {
        return self::request("POST", $url, $data, $headers);
    }

    public static function put(string $url, array $data = [], array $headers = []): array
    {
        return self::request("PUT", $url, $data, $headers);
    }

    public static function patch(string $url, array $data = [], array $headers = []): array
    {
        return self::request("PATCH", $url, $data, $headers);
    }

    public static function delete(string $url, array $data = [], array $headers = []): array
    {
        return self::request("DELETE", $url, $data, $headers);
    }
}