<?php
namespace Magefox\SSOIntegration\Model\Config\Source;

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