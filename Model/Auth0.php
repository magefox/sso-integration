<?php
namespace Magefox\SSOIntegration\Model;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Auth0
{
    protected $curl;

    /**
     * @var ScopeConfigInterface
     * */
    protected $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

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

    public function __construct(
        Curl $curl,
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        UrlInterface $urlBuilder
    ) {
        $this->curl = $curl;
        $this->scopeConfig = $scopeConfig;
        $this->encryptor = $encryptor;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get auth0 domain
     *
     * @return string
     */
    public function getDomain() {
        if(!$this->domain) {
            $account = $this->scopeConfig->getValue('sso_integration/general/auth0_account');
            $this->domain = "$account.auth0.com";
        }

        return $this->domain;
    }

    /**
     * Get auth0 client id
     *
     * @return string
     */
    public function getClientId() {
        if(!$this->clientId) {
            $this->clientId = $this->encryptor->decrypt($this->scopeConfig->getValue('sso_integration/general/auth0_client_id'));
        }

        return $this->clientId;
    }

    /**
     * Get auth0 client secret
     *
     * @return string
     */
    public function getClientSecret() {
        if(!$this->clientSecret) {
            $this->clientSecret = $this->encryptor->decrypt($this->scopeConfig->getValue('sso_integration/general/auth0_client_secret'));
        }

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

    public function getAccessToken($code) {
        $url = "https://$this->domain/oauth/token";

        $this->curl->post($url,
            [
                'client_id'     => $this->getClientId(),
                'client_secret' => $this->getClientSecret(),
                'code'          => $code,
                'grant_type'    => 'authorization_code'
            ]
        );

        $response = json_decode($this->curl->getBody());

        var_dump($response);die();
    }

    public function getUserInfo($accessToken) {
        $url = "https://$this->domain/userinfo/?access_token=$accessToken";

        $this->curl->get($url);
        $response = json_decode($this->curl->getBody());

        var_dump($response);die();
    }
}