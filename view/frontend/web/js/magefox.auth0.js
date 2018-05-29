define([
    'jquery',
    'auth0Lock',
    'jquery/ui',
    'mage/translate'
], function($, Auth0Lock) {
    "use strict";

    $.widget('magefox.auth0', {
        options: {
            "clientId": '',
            "domain": '',
            "state": '',
            "redirectUrl": ''
        },

        _create: function () {
            this.lock = new Auth0Lock(
                this.options.clientId,
                this.options.domain,
                {
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
