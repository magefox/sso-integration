<?php
namespace Magefox\SSOIntegration\Controller\Auth0;

use Magento\Framework\App\Action\Context;
use Magefox\SSOIntegration\Model\Auth0Factory;

class Callback extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Auth0Factory
     */
    protected $auth0Factory;

    public function __construct(
        Context $context,
        Auth0Factory $auth0Factory
    ) {
        $this->auth0Factory = $auth0Factory;
        parent::__construct($context);
    }

    public function execute()
    {
        /**
         * @var \Magefox\SSOIntegration\Model\Auth0
         */
        $auth0 = $this->auth0Factory
            ->create();

        $user = $auth0->getAccessToken($this->getRequest()->getParam('code'));
    }
}