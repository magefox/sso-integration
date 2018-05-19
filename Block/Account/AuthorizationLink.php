<?php
namespace Magefox\SSOIntegration\Block\Account;

class AuthorizationLink extends \Magento\Customer\Block\Account\AuthorizationLink
{
    /**
     * @var \Magefox\SSOIntegration\Model\Auth0
     */
    protected $auth0;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magefox\SSOIntegration\Model\Auth0 $auth0,
        array $data = []
    ) {
        $this->auth0 = $auth0;

        parent::__construct($context, $httpContext, $customerUrl, $postDataHelper, $data);
    }

    /**
     * @return \Magefox\SSOIntegration\Model\Auth0
     */
    public function getAuth0() {
        return $this->auth0;
    }
}