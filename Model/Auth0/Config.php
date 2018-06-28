<?php
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
     * @var bool
     */
    protected $clientSecretBase64Encoded;

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
        $this->urlBuilder = $urlBuilder;
        $this->domain = "{$scopeConfig->getValue(self::XML_PATH_ACCOUNT)}.auth0.com";
        $this->clientId = $encryptor->decrypt($scopeConfig->getValue(self::XML_PATH_CLIENT_ID));
        $this->clientSecret = $encryptor->decrypt($scopeConfig->getValue(self::XML_PATH_CLIENT_SECRET));
        $this->clientSecretBase64Encoded = boolval($scopeConfig->getValue(self::XML_PATH_CLIENT_SECRET_BASE64_ENCODED));
        $this->clientSigningAlgorithm = $scopeConfig->getValue(self::XML_PATH_CLIENT_SIGNING_ALGORITHM);
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
     * Is client secret base64 encoded
     *
     * @return bool
     */
    public function isClientSecretBase64Encoded() {
        return $this->clientSecretBase64Encoded;
    }

    /**
     * Get client signing Algorithm
     *
     * @return mixed|string
     */
    public function getClientSigningAlgorithm() {
        return $this->clientSigningAlgorithm;
    }
}