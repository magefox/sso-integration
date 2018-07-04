<?php
/******************************************************
 * @package Magento 2 SSO Integration
 * @author http://www.magefox.com
 * @copyright (C) 2018- Magefox.Com
 * @license MIT
 *******************************************************/

namespace Magefox\SSOIntegration\Observer\Auth0;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RemoveAuthorizationLinks implements ObserverInterface
{
    /**
     * @var \Magefox\SSOIntegration\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Magefox\SSOIntegration\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Fires when layout_generate_blocks_after is dispatched
     *
     * @param Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(Observer $observer)
    {
        /**
         * @var $layout \Magento\Framework\View\Layout
         */
        $layout = $observer->getLayout();
        $active = $this->helper->isActive();
        $loginLink = $layout->getBlock('authorization-link-login');
        $registerLink = $layout->getBlock('register-link');
        $accountInformationLink = $layout->getBlock('customer-account-navigation-account-edit-link');

        if ($loginLink && $active) {
            $layout->unsetElement('authorization-link-login');
        }

        if ($registerLink && $active) {
            $layout->unsetElement('register-link');
        }

        if ($accountInformationLink && $accountInformationLink) {
            $layout->unsetElement('customer-account-navigation-account-edit-link');
        }
    }
}
