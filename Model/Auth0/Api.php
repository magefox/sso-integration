<?php
/******************************************************
 * @package Magento 2 SSO Integration
 * @author http://www.magefox.com
 * @copyright (C) 2018- Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\SSOIntegration\Model\Auth0;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Logger\Monolog;
use Magento\Framework\Serialize\Serializer\Json;
use Firebase\JWT\JWT;

class Api
{
    /**
     * @var \Magefox\SSOIntegration\Model\Auth0\Config
     */
    protected $config;

    /**
     * @var Monolog
     */
    protected $logger;

    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $serializer;

    /**
     * @var string
     */
    protected $appToken;

    /**
     * @var array
     */
    protected $jwtKey;

    /**
     * Api constructor.
     *
     * @param Config $config
     * @param Curl $curlClient
     * @param Logger $logger
     * @param Json $serializer
     */
    public function __construct(
        Config $config,
        Curl $curlClient,
        Monolog $logger,
        Json $serializer
    ) {
        $this->config = $config;
        $this->curlClient = $curlClient;
        $this->logger = $logger;
        $this->serializer = $serializer;
    }

    /**
     * Init client access token
     *
     * @return string
     */
    public function getAppToken()
    {
        if (!$this->appToken) {
            try {
                $this->curlClient->post(
                    "https://{$this->config->getDomain()}/oauth/token",
                    [
                        'client_id' => $this->config->getClientId(),
                        'client_secret' => $this->config->getClientSecret(),
                        'audience' => "https://{$this->config->getDomain()}/api/v2/",
                        'grant_type' => 'client_credentials'
                    ]
                );

                $response = $this->serializer->unserialize($this->curlClient->getBody());

                $this->appToken = $response['access_token'];
            } catch (\Exception $e) {
                $this->logger->debug("getClientToken error: {$e->getMessage()}");
            }
        }

        return $this->appToken;
    }

    /**
     * Convert a certificate to PEM format
     *
     * @param string $cert - certificate, like from .well-known/jwks.json
     * @return string
     */
    protected function convertCertToPem($cert)
    {
        return '-----BEGIN CERTIFICATE-----' . PHP_EOL
            . chunk_split($cert, 64, PHP_EOL)
            . '-----END CERTIFICATE-----' . PHP_EOL;
    }

    /**
     * Get JWT key
     *
     * @return array
     */
    protected function getJWTKey()
    {
        if (!$this->jwtKey) {
            $endpoint = "https://{$this->config->getDomain()}/.well-known/jwks.json";
            $jwtKey = [];

            try {
                $this->curlClient->get($endpoint);
                $jwks = $this->serializer->unserialize($this->curlClient->getBody());
                foreach ($jwks['keys'] as $key) {
                    $jwtKey[$key['kid']] = self::convertCertToPem($key['x5c'][0]);
                }
            } catch (\Exception $e) {
                $this->logger->debug("getJWTKey error: {$e->getMessage()}");
            }

            $this->jwtKey = $jwtKey;
        }

        return $this->jwtKey;
    }

    /**
     * Get token
     *
     * @param string $code
     * @return \stdClass|bool
     */
    protected function getRequestToken($code)
    {
        try {
            $this->curlClient->post(
                "https://{$this->config->getDomain()}/oauth/token",
                [
                    'client_id' => $this->config->getClientId(),
                    'client_secret' => $this->config->getClientSecret(),
                    'redirect_uri' => $this->config->getCallbackUrl(),
                    'code' => $code,
                    'grant_type' => 'authorization_code'
                ]
            );
        } catch (\Exception $e) {
            $this->logger->debug("getRequestToken error: {$e->getMessage()} with code \"{$code}\"");
        }

        $token = $this->serializer->unserialize($this->curlClient->getBody());

        if (isset($token['id_token'])) {
            /**
             * Allows a 5 second tolerance on timing checks.
             *
             * Fix slight skew between the clock on the server that mints the tokens
             * and the clock on the server that's validating the token.
             */
            JWT::$leeway = 5;

            $clientSecret = $this->config->getClientSecret();

            if ($this->config->isClientSecretBase64Encoded()) {
                $clientSecret = JWT::urlsafeB64Decode($this->config->getClientSecret());
            }

            $key = $this->config->getClientSigningAlgorithm() === 'RS256' ? $this->getJWTKey() : $clientSecret;

            // Decode the incoming ID token for the Auth0 user.
            return JWT::decode(
                $token['id_token'],
                $key,
                [$this->config->getClientSigningAlgorithm()]
            );
        }

        return false;
    }

    /**
     * @param string $code
     * @return array
     */
    public function getUser($code)
    {
        $token = $this->getRequestToken($code);
        $headers = [
            "Authorization" => "Bearer {$this->getAppToken()}",
        ];

        try {
            if ($token !== false) {
                $this->curlClient->setHeaders($headers);
                $this->curlClient->get("https://{$this->config->getDomain()}/api/v2/users/{$token->sub}");

                return $this->serializer->unserialize($this->curlClient->getBody());
            } else {
                return [];
            }
        } catch (\Exception $e) {
            $this->logger->debug("getUser error: {$e->getMessage()} with user_id \"{$token->sub}\"");
        }
    }

    /**
     * Create auth0 user
     *
     * @param array $data
     * @return array
     */
    public function createUser(array $data)
    {
        $headers = [
            "Authorization" => "Bearer {$this->getAppToken()}",
            "content-type" => "application/json"
        ];

        try {
            $this->curlClient->setHeaders($headers);
            $this->curlClient->post(
                "https://{$this->config->getDomain()}/api/v2/users",
                $this->serializer->serialize($data)
            );

            return $this->serializer->unserialize($this->curlClient->getBody());
        } catch (\Exception $e) {
            $this->logger->debug(
                "createUser error: {$e->getMessage()} " .
                "with data \"{$this->serializer->serialize($data)}\""
            );
        }
    }
}
