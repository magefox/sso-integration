<?php
/******************************************************
 * @package Magento 2 SSO Integration
 * @author http://www.magefox.com
 * @copyright (C) 2018- Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\SSOIntegration\Model\Auth0;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\UrlInterface;

class Config
{
    const XML_PATH_ACCOUNT = 'sso_integration/general/auth0_account';
    const XML_PATH_CLIENT_ID = 'sso_integration/general/auth0_client_id';
    const XML_PATH_CLIENT_SECRET = 'sso_integration/general/auth0_client_secret';
    const XML_PATH_CLIENT_SECRET_BASE64_ENCODED = 'sso_integration/general/auth0_client_secret_base64_encoded';
    const XML_PATH_CLIENT_SIGNING_ALGORITHM = 'sso_integration/general/auth0_client_signing_algorithm';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

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
     * @var bool|null
     */
    protected $clientSecretBase64Encoded = null;

    /**
     * @var string
     */
    protected $clientSigningAlgorithm;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param EncryptorInterface $encryptor
     * @param UrlInterface $urlBuilder
     */
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
    public function getDomain()
    {
        if (!$this->domain) {
            $this->domain = "{$this->scopeConfig->getValue(self::XML_PATH_ACCOUNT)}.auth0.com";
        }

        return $this->domain;
    }

    /**
     * Get auth0 client id
     *
     * @return string
     */
    public function getClientId()
    {
        if (!$this->clientId) {
            $this->clientId = $this->encryptor->decrypt($this->scopeConfig->getValue(self::XML_PATH_CLIENT_ID));
        }

        return $this->clientId;
    }

    /**
     * Get auth0 client secret
     *
     * @return string
     */
    public function getClientSecret()
    {
        if (!$this->clientSecret) {
            $this->clientSecret = $this->encryptor->decrypt($this->scopeConfig->getValue(self::XML_PATH_CLIENT_SECRET));
        }

        return $this->clientSecret;
    }

    /**
     * Get callback url
     *
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->urlBuilder->getUrl('sso/auth0/callback');
    }

    /**
     * Is client secret base64 encoded
     *
     * @return bool
     */
    public function isClientSecretBase64Encoded()
    {
        if ($this->clientSecretBase64Encoded === null) {
            $this->clientSecretBase64Encoded = boolval(
                $this->scopeConfig->getValue(self::XML_PATH_CLIENT_SECRET_BASE64_ENCODED)
            );
        }

        return $this->clientSecretBase64Encoded;
    }

    /**
     * Get client signing Algorithm
     *
     * @return string
     */
    public function getClientSigningAlgorithm()
    {
        if (!$this->clientSigningAlgorithm) {
            $this->clientSigningAlgorithm = $this->scopeConfig->getValue(self::XML_PATH_CLIENT_SIGNING_ALGORITHM);
        }

        return $this->clientSigningAlgorithm;
    }
}
