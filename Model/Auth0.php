<?php
namespace Magefox\SSOIntegration\Model;

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
        Curl $curl,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        Json $serializer,
        UrlInterface $urlBuilder
    ) {
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
                $this->token = [
                    'error'             => 'curl_exception',
                    'error_description' => $e->getMessage()
                ];
            }

            $this->token = $this->serializer->unserialize($this->curlClient->getBody());
        }

        return $this->token;
    }

    /**
     * Get user information
     *
     * @param string $accessToken
     * @return array
     */
    public function getUserInfo($accessToken) {
        if(!$this->userInfo) {
            try {
                $this->curlClient->get("https://{$this->getDomain()}/userinfo/?access_token=$accessToken");
            } catch (\Exception $e) {
                $this->userInfo = [
                    'error'             => 'curl_exception',
                    'error_description' => $e->getMessage()
                ];
            }

            $this->userInfo = $this->serializer->unserialize($this->curlClient->getBody());
        }

        return $this->userInfo;
    }
}