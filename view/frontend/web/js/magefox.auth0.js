define([
    'jquery',
    'auth0Lock',
    'jquery/ui',
    'mage/translate'
], function($, Auth0Lock) {
    "use strict";

    $.widget('magefox.auth0', {
        options: {
            "container": false,
            "clientId": '',
            "domain": '',
            "state": '',
            "logo": '',
            "redirectUrl": ''
        },

        _create: function () {
            this.lock = new Auth0Lock(
                this.options.clientId,
                this.options.domain,
                {
                    container: this.options.container === false ? '' : this.options.container,
                    theme: {
                        logo: this.options.logo
                    },
                    additionalSignUpFields: [
                        {
                            name: "firstname",
                            placeholder: $.mage.__("First Name"),
                            validator: function(firstname) {
                                return {
                                    valid: firstname.length > 0,
                                    hint: $.mage.__("This is a required field.")
                                };
                            }
                        },
                        {
                            name: "lastname",
                            placeholder: $.mage.__("Last Name"),
                            validator: function(firstname) {
                                return {
                                    valid: firstname.length > 0,
                                    hint: $.mage.__("This is a required field.")
                                };
                            }
                        },
                        {
                            name: "newsletter",
                            type: "checkbox",
                            placeholder: $.mage.__("Sign Up for Newsletter")
                        }
                    ],
                    auth: {
                        redirectUrl: this.options.redirectUrl,
                        responseType: 'code',
                        params: {
                            state: this.options.state,
                            scope: 'openid' // Learn about scopes: https://auth0.com/docs/scopes
                            // scope: 'openid profile email' // Learn about scopes: https://auth0.com/docs/scopes
                        }
                    }
                })
            ;

            $(this.element).find('a').click(function (e) {
                e.preventDefault();
                this.lock.show();
            }.bind(this));
        }
    });

    return $.magefox.auth0;
});
