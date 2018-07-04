/******************************************************
 * @package Magento 2 SSO Integration
 * @author http://www.magefox.com
 * @copyright (C) 2018- Magefox.Com
 * @license MIT
 *******************************************************/

var config = {
    paths: {
        'auth0Lock': 'Magefox_SSOIntegration/js/libs/auth0-lock.min'
    },
    shim: {
        'auth0Lock': {
            "exports": "auth0Lock"
        }
    },
    map: {
        '*': {
            'magefox.auth0': 'Magefox_SSOIntegration/js/magefox.auth0'
        }
    }
};
