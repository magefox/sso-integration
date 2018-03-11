<?php
namespace Magefox\SSOIntegration\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Auth0
{
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
        ScopeConfigInterface $scopeConfig,
        EncryptorInterface $encryptor,
        UrlInterface $urlBuilder
    ) {
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
}