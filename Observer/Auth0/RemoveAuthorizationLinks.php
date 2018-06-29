<?php
namespace Magefox\SSOIntegration\Observer\Auth0;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RemoveAuthorizationLinks implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     *
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /**
         * @var $layout \Magento\Framework\View\Layout
         */
        $layout = $observer->getLayout();
        $active = $this->scopeConfig->getValue(
            'sso_integration/general/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
        $loginLink = $layout->getBlock('authorization-link-login');
        $registerLink = $layout->getBlock('register-link');

        if ($loginLink && $active) {
            $layout->unsetElement('authorization-link-login');
        }

        if ($registerLink && $active) {
            $layout->unsetElement('register-link');
        }
    }
}