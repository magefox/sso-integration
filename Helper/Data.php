<?php
namespace Magefox\SSOIntegration\Helper;

use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;

        parent::__construct($context);
    }

    /**
     * Is module active
     *
     * @return bool
     */
    public function isActive() {
        return (bool) $this->scopeConfig->getValue('sso_integration/general/active', \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }
}