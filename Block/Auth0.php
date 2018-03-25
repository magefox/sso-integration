<?php
namespace Magefox\SSOIntegration\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\Form\FormKey;
use Magefox\SSOIntegration\Model\Auth0Factory;

class Auth0 extends Template
{
    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * @var \Magefox\SSOIntegration\Model\Auth0
     */
    protected $auth0;

    public function __construct(
        Template\Context $context,
        FormKey $formKey,
        Auth0Factory $auth0Factory,
        array $data = []
    ) {
        $this->formKey = $formKey;
        $this->auth0 = $auth0Factory->create();
        parent::__construct($context, $data);
    }

    /**
     * @return \Magefox\SSOIntegration\Model\Auth0
     */
    public function getAuth0() {
        return $this->auth0;
    }

    /**
     * @return string
     */
    public function getFormKey() {
        return $this->formKey->getFormKey();
    }
}