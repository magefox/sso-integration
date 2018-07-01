<?php
/******************************************************
 * @package Magento 2 SSO Integration
 * @author http://www.magefox.com
 * @copyright (C) 2018- Magefox.Com
 * @license PHP files are GNU/GPL
 *******************************************************/

namespace Magefox\SSOIntegration\Controller\Auth0;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magefox\SSOIntegration\Model\Auth0\ApiFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Session;

class Callback extends \Magento\Framework\App\Action\Action
{
    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var Validator
     */
    protected $formValidator;

    /**
     * @var Auth0
     */
    protected $apiFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var CustomerInterfaceFactory
     */
    protected $customerDataFactory;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var Session
     */
    protected $customerSession;

    public function __construct(
        Context $context,
        DataObjectHelper $dataObjectHelper,
        Validator $formValidator,
        ApiFactory $apiFactory,
        StoreManagerInterface $storeManager,
        CustomerFactory $customerFactory,
        CustomerInterfaceFactory $customerDataFactory,
        AccountManagementInterface $accountManagement,
        Session $customerSession
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->apiFactory = $apiFactory;
        $this->storeManager = $storeManager;
        $this->customerFactory = $customerFactory;
        $this->customerDataFactory = $customerDataFactory;
        $this->formValidator = $formValidator;
        $this->accountManagement = $accountManagement;
        $this->customerSession = $customerSession;

        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $this->getRequest()->setParams(
            [
                'form_key' => $this->getRequest()->getParam('state')
            ]
        );

        /**
         * Validate form key
         */
        if ($this->formValidator->validate($this->getRequest())) {
            try {
                /**
                 * @var $api \Magefox\SSOIntegration\Model\Auth0\Api
                 */
                $api = $this->apiFactory->create();
                $user = $api->getUser($this->getRequest()->getParam('code'));

                /**
                 * @var $customer \Magento\Customer\Model\Customer
                 */
                $customer = $this->customerFactory
                    ->create()
                    ->setWebsiteId($this->storeManager->getStore()->getWebsiteId())
                    ->loadByEmail($user['email']);

                /**
                 * Customer register
                 */
                if (!$customer->getId()) {
                    $customer = $this->customerDataFactory->create();
                    $this->dataObjectHelper->populateWithArray(
                        $customer,
                        [
                            'firstname' => $user['user_metadata']['firstname'],
                            'lastname' => $user['user_metadata']['lastname'],
                            'email' => $user['email']
                        ],
                        \Magento\Customer\Api\Data\CustomerInterface::class
                    );

                    $customer = $this->accountManagement
                        ->createAccount($customer, $user['user_id'], '');

                    $this->_eventManager->dispatch(
                        'customer_register_success',
                        ['account_controller' => $this, 'customer' => $customer]
                    );
                }

                /**
                 * Process login
                 */
                if (!$user['email_verified']) {
                    $this->messageManager->addWarningMessage(
                        __('You must confirm your account. Please check your email for the confirmation link.')
                    );
                    $this->_redirect('/');
                    return;
                } else {
                    $this->customerSession->loginById($customer->getId());
                }

                $this->messageManager->addSuccessMessage(__('Login successful.'));
                $this->_redirect('/');
                return;
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->_redirect('/');
                return;
            }
        } else {
            $this->messageManager->addErrorMessage(__('Invalid form key, please try again.'));
            $this->_redirect('/');
            return;
        }
    }
}
