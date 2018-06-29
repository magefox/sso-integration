<?php
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
