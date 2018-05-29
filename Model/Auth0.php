<?php
namespace Magefox\SSOIntegration\Model;

use Magefox\SSOIntegration\Logger\Logger;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Serialize\Serializer\Json;

class Auth0
{
    const XML_PATH_ACCOUNT = 'sso_integration/general/auth0_account';
    const XML_PATH_CLIENT_ID = 'sso_integration/general/auth0_client_id';
    const XML_PATH_CLIENT_SECRET = 'sso_integration/general/auth0_client_secret';

    const ALGORITHM = 'RS256';
//    const CLIENT_SECRET_BASE64_ENCODED = false;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var Curl
     */
    protected $curlClient;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var array
     */
    protected $jwtKey;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $clientSecret;

    /**
     * @var array
     */
    protected $token;

    /**
     * @var array
     */
    protected $userInfo;

    public function __construct(
        Logger $logger,
        Curl $curl,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        Json $serializer,
        UrlInterface $urlBuilder
    ) {
        $this->logger = $logger;
        $this->curlClient = $curl;
        $this->serializer = $serializer;
        $this->urlBuilder = $urlBuilder;

        $this->domain = "{$scopeConfig->getValue(self::XML_PATH_ACCOUNT)}.auth0.com";
        $this->clientId = $encryptor->decrypt($scopeConfig->getValue(self::XML_PATH_CLIENT_ID));
        $this->clientSecret = $encryptor->decrypt($scopeConfig->getValue(self::XML_PATH_CLIENT_SECRET));
    }

    /**
     * Get auth0 domain
     *
     * @return string
     */
    public function getDomain() {
        return $this->domain;
    }

    /**
     * Get auth0 client id
     *
     * @return string
     */
    public function getClientId() {
        return $this->clientId;
    }

    /**
     * Get auth0 client secret
     *
     * @return string
     */
    public function getClientSecret() {
        return $this->clientSecret;
    }

    /**
     * Get callback url
     *
     * @return string
     */
    public function getCallbackUrl() {
        return $this->urlBuilder->getUrl('sso/auth0/callback');
    }

    /**
     * Convert a certificate to PEM format
     *
     * @see https://en.wikipedia.org/wiki/Privacy-enhanced_Electronic_Mail
     *
     * @param string $cert - certificate, like from .well-known/jwks.json
     *
     * @return string
     */
    protected function convertCertToPem( $cert ) {
        return '-----BEGIN CERTIFICATE-----' . PHP_EOL
            . chunk_split($cert, 64, PHP_EOL)
            . '-----END CERTIFICATE-----' . PHP_EOL;
    }

    /**
     * Get JWT key
     *
     * @return array
     */
    public function getJWTKey() {
        if(!$this->jwtKey) {
            $endpoint = "https://{$this->getDomain()}/.well-known/jwks.json";
            $jwtKey = [];

            try {
                $this->curlClient->get($endpoint);
                $jwks = $this->serializer->unserialize($this->curlClient->getBody());
                foreach ($jwks['keys'] as $key) {
                    $jwtKey[$key['kid']] = self::convertCertToPem($key['x5c'][0]);
                }
            } catch (\Exception $e) {
                $this->logger->debug(print_r([
                    'action' => 'Magefox\SSOIntegration\Model\Auth0::getJWTKey',
                    'message' => $e->getMessage()
                ], true));
            }

            $this->jwtKey = $jwtKey;
        }

        return $this->jwtKey;
    }

    /**
     * Get token
     *
     * @param string $code
     * @return array
     */
    public function getToken($code) {
        if(!$this->token) {
            try {
                $this->curlClient->post("https://{$this->getDomain()}/oauth/token",
                    [
                        'client_id' => $this->getClientId(),
                        'client_secret' => $this->getClientSecret(),
                        'redirect_uri' => $this->getCallbackUrl(),
                        'code' => $code,
                        'grant_type' => 'authorization_code'
                    ]
                );
            } catch (\Exception $e) {
                $this->logger->debug(print_r([
                    'action'            => 'Magefox\SSOIntegration\Model\Auth0::getToken',
                    'code'              => $code,
                    'message'           => $e->getMessage()
                ], true));
            }

            $this->token = $this->serializer->unserialize($this->curlClient->getBody());
        }

        return $this->token;
    }

    public function getClientToken() {

    }

    /**
     * Get user information
     *
     * @param string $accessToken
     * @return array
     */
    public function getUserInfo($accessToken) {
        if(!$this->userInfo) {
            $headers = [
                "Authorization" => "Bearer $accessToken",
            ];

            try {
                $this->curlClient->setHeaders($headers);
                $this->curlClient->get("https://{$this->getDomain()}/userinfo/");
            } catch (\Exception $e) {
                $this->logger->debug(print_r([
                    'action'            => 'Magefox\SSOIntegration\Model\Auth0::getUserInfo',
                    'access_token'      => $accessToken,
                    'message'           => $e->getMessage()
                ], true));
            }

            $this->userInfo = $this->serializer->unserialize($this->curlClient->getBody());
        }

        return $this->userInfo;
    }

    public function getUsers($accessToken, $q = "", $page = 0, $perPage = 100, $includeTotals = false, $sort = "user_id:1") {
        $endpoint = "https://{$this->getDomain()}/api/v2/users?include_totals=$includeTotals&per_page=$perPage&page=$page&sort=$sort&q=$q&search_engine=v2";

        $headers = [
            "Authorization" => "Bearer $accessToken",
        ];

        try {
            $this->curlClient->setHeaders($headers);
            $this->curlClient->get($endpoint);

            return json_decode($this->curlClient->getBody());
        } catch (\Exception $e) {
            $this->logger->debug(print_r([
                'action'            => 'Magefox\SSOIntegration\Model\Auth0::getUsers',
                'access_token'      => $accessToken,
                'q'                 => $q,
                'page'              => $page,
                'perPage'           => $perPage,
                'includeTotals'     => $includeTotals,
                'sort'              => $sort,
                'message'           => $e->getMessage()
            ], true));
        }
    }

    public function getUser($jwt, $userId) {
        $endpoint = "https://{$this->getDomain()}/api/v2/users/" . urlencode($userId);

        $headers['Authorization'] = "Bearer $jwt";

        try {
            $this->curlClient->setHeaders($headers);
            $this->curlClient->get($endpoint);

            return json_decode($this->curlClient->getBody());
        } catch (\Exception $e) {
            $this->logger->debug(print_r([
                'action'            => 'Magefox\SSOIntegration\Model\Auth0::getUser',
                'jwt'               => $jwt,
                'userId'            => $userId,
                'message'           => $e->getMessage()
            ], true));
        }
    }

//    public static function create_user( $domain, $jwt, $data ) {
//
//        $endpoint = "https://$domain/api/v2/users";
//
//        $headers = self::get_info_headers();
//
//        $headers['Authorization'] = "Bearer $jwt";
//        $headers['content-type'] = 'application/json';
//
//        $response = wp_remote_post( $endpoint  , array(
//            'headers' => $headers,
//            'body' => json_encode( $data )
//        ) );
//
//        if ( $response instanceof WP_Error ) {
//            WP_Auth0_ErrorManager::insert_auth0_error( __METHOD__, $response );
//            error_log( $response->get_error_message() );
//            return false;
//        }
//
//        if ( $response['response']['code'] != 201 ) {
//            WP_Auth0_ErrorManager::insert_auth0_error( __METHOD__, $response['body'] );
//            error_log( $response['body'] );
//            return false;
//        }
//
//        return json_decode( $response['body'] );
//    }
//
//    public static function signup_user( $domain, $data ) {
//
//        $endpoint = "https://$domain/dbconnections/signup";
//
//        $headers = self::get_info_headers();
//
//        $headers['content-type'] = 'application/json';
//
//        $response = wp_remote_post( $endpoint  , array(
//            'headers' => $headers,
//            'body' => json_encode( $data )
//        ) );
//
//        if ( $response instanceof WP_Error ) {
//            WP_Auth0_ErrorManager::insert_auth0_error( __METHOD__, $response );
//            error_log( $response->get_error_message() );
//            return false;
//        }
//
//        if ( $response['response']['code'] !== 200 ) {
//            WP_Auth0_ErrorManager::insert_auth0_error( __METHOD__, $response['body'] );
//            error_log( $response['body'] );
//            return false;
//        }
//
//        return json_decode( $response['body'] );
//    }
}