<?php
namespace Magefox\SSOIntegration\Controller\Auth0;

class Callback extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        var_dump($this->getRequest()->getParams());die();
    }
}