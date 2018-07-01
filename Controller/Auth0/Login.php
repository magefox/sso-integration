<?php
/******************************************************
 * @package Magento 2 SSO Integration
 * @author http://www.magefox.com
 * @copyright (C) 2018- Magefox.Com
 * @license PHP files are GNU/GPL
 *******************************************************/

namespace Magefox\SSOIntegration\Controller\Auth0;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\ForwardFactory;

class Login extends \Magento\Customer\Controller\Account\Login
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Login constructor.
     *
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        ScopeConfigInterface $scopeConfig,
        ForwardFactory $resultForwardFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->resultForwardFactory = $resultForwardFactory;

        parent::__construct($context, $customerSession, $resultPageFactory);
    }

    /**
     * Forward to 404
     *
     * @return \Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $active = $this->scopeConfig->getValue(
            'sso_integration/general/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );

        if ($active) {
            $forwarder = $this->resultForwardFactory->create();
            $forwarder->forward('noroute');

            return $forwarder;
        } else {
            return parent::execute();
        }
    }
}
