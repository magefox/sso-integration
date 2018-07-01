<?php
/******************************************************
 * @package Magento 2 SSO Integration
 * @author http://www.magefox.com
 * @copyright (C) 2018- Magefox.Com
 * @license PHP files are GNU/GPL
 *******************************************************/

namespace Magefox\SSOIntegration\Model\Config\Source\Auth0;

class Algorithm implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve possible customer address types
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            'HS256' => __('HS256'),
            'RS256' => __('RS256')
        ];
    }
}
