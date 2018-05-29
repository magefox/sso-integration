<?php
namespace Magefox\SSOIntegration\Block\Account;

/**
 * Class AuthorizationLink
 * @package Magefox\SSOIntegration\Block\Account
 */
class AuthorizationLink extends \Magento\Customer\Block\Account\AuthorizationLink
{
    /**
     * @var \Magefox\SSOIntegration\Model\Auth0
     */
    protected $auth0;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magefox\SSOIntegration\Model\Auth0 $auth0,
        \Magento\Framework\Data\Form\FormKey $formKey,
        array $data = []
    ) {
        $this->auth0 = $auth0;
        $this->formKey = $formKey;

        parent::__construct($context, $httpContext, $customerUrl, $postDataHelper, $data);
    }

    /**
     * @return \Magefox\SSOIntegration\Model\Auth0
     */
    public function getAuth0() {
        return $this->auth0;
    }

    /**
     * Get form key
     *
     * @return string
     */
    public function getFormKey() {
        return $this->formKey->getFormKey();
    }
}