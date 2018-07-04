<?php
/******************************************************
 * @package Magento 2 SSO Integration
 * @author http://www.magefox.com
 * @copyright (C) 2018- Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\SSOIntegration\Block;

class Auth0 extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magefox\SSOIntegration\Model\Auth0\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magefox\SSOIntegration\Model\Auth0\Config $config,
        \Magento\Framework\Data\Form\FormKey $formKey,
        array $data = []
    ) {
        $this->config = $config;
        $this->formKey = $formKey;

        parent::__construct($context, $data);
    }

    /**
     * Get auth0 configurations
     *
     * @return \Magefox\SSOIntegration\Model\Auth0\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * Get logo src
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getLogoSrc()
    {
        return $this->getLayout()->getBlock('logo')->getLogoSrc();
    }
}
