<?php
/******************************************************
 * @package Magento 2 SSO Integration
 * @author http://www.magefox.com
 * @copyright (C) 2018- Magefox.Com
 * @license PHP files are GNU/GPL
 *******************************************************/

namespace Magefox\SSOIntegration\Model\Config\Source;

class Provider implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve possible customer address types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            'auth0' => __('Auth0'),
            'saml2' => __('SAML2 (Coming soon)')
        ];
    }
}
