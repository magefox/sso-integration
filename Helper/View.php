<?php
namespace Magefox\SSOIntegration\Helper;

use Magento\Framework\App\Helper\Context;

class View extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Data
     */
    protected $helper;

    public function __construct(
        Context $context,
        Data $helper
    ) {
        $this->helper = $helper;

        parent::__construct($context);
    }

    /**
     * Get authorization link template
     *
     * @return string
     */
    public function getAuthorizationLinkTemplate() {
        $template = 'Magento_Customer::account/link/authorization.phtml';

        if($this->helper->isActive()) {
            $template = 'Magefox_SSOIntegration::account/link/authorization.phtml';
        }

        return $template;
    }
}