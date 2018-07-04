/******************************************************
 * @package Magento 2 SSO Integration
 * @author http://www.magefox.com
 * @copyright (C) 2018- Magefox.Com
 * @license MIT
 *******************************************************/

define([
    'jquery',
    'auth0Lock',
    'jquery/ui',
    'mage/translate'
], function ($, Auth0Lock) {
    "use strict";

    $.widget('magefox.auth0', {
        options: {
            "clientId": '',
            "domain": '',
            "state": '',
            "logo": '',
            "redirectUrl": '',
            "allowLogin": true,
            "allowSignUp": true,
            "allowForgotPassword": true
        },

        _create: function () {
            this.lock = new Auth0Lock(
                this.options.clientId,
                this.options.domain,
                {
                    theme: {
                        logo: this.options.logo
                    },
                    additionalSignUpFields: [
                        {
                            name: "firstname",
                            placeholder: $.mage.__("First Name"),
                            validator: function (firstname) {
                                return {
                                    valid: firstname.length > 0,
                                    hint: $.mage.__("This is a required field.")
                                };
                            }
                        },
                        {
                            name: "lastname",
                            placeholder: $.mage.__("Last Name"),
                            validator: function (firstname) {
                                return {
                                    valid: firstname.length > 0,
                                    hint: $.mage.__("This is a required field.")
                                };
                            }
                        }
                    ],
                    auth: {
                        redirectUrl: this.options.redirectUrl,
                        responseType: 'code',
                        params: {
                            state: this.options.state,
                            scope: 'openid' // Learn about scopes: https://auth0.com/docs/scopes
                        }
                    },
                    allowLogin: this.options.allowLogin,
                    allowSignUp: this.options.allowSignUp,
                    allowForgotPassword: this.options.allowForgotPassword
                }
            );

            $(this.element).click(function (e) {
                e.preventDefault();
                this.lock.show();
            }.bind(this));
        }
    });

    return $.magefox.auth0;
});
