<?php
namespace Magefox\SSOIntegration\Block;

use Magento\Framework\View\Element\Template;
use Magefox\SSOIntegration\Model\Auth0Factory;

class Auth0 extends Template
{
    protected $auth0;

    public function __construct(
        Template\Context $context,
        Auth0Factory $auth0Factory,
        array $data = []
    ) {
        $this->auth0 = $auth0Factory->create();
        parent::__construct($context, $data);
    }

    /**
     * @return \Magefox\SSOIntegration\Model\Auth0
     */
    public function getAuth0() {
        return $this->auth0;
    }
}