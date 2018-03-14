<?php
namespace Magefox\SSOIntegration\Controller\Auth0;

use Auth0\SDK\Auth0;
use Magento\Framework\App\Action\Context;
use Magefox\SSOIntegration\Model\Auth0Factory;

class Callback extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magefox\SSOIntegration\Model\Auth0
     */
    protected $auth0;

    public function __construct(
        Context $context,
        Auth0Factory $auth0Factory
    ) {
        $this->auth0 = $auth0Factory->create();
        parent::__construct($context);
    }

    public function execute()
    {
        $auth0 = new Auth0([
            'domain'                => $this->auth0->getDomain(),
            'client_id'             => $this->auth0->getClientId(),
            'client_secret'         => $this->auth0->getClientSecret(),
            'redirect_uri'          => $this->auth0->getCallbackUrl(),
            'audience'              => 'https://magefox.auth0.com/userinfo',
            'scope'                 => 'openid profile',
            'persist_id_token'      => true,
            'persist_access_token'  => true,
            'persist_refresh_token' => true,
        ]);

        var_dump($userInfo = $auth0->getUser());die();
    }
}