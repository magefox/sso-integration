define([
    'jquery',
    'Auth0Lock',
    'jquery/ui',
    'mage/translate'
], function($, Auth0Lock) {
    "use strict";

    $.widget('magefox.auth0', {
        options: {
            "clientId": '',
            "domain": '',
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
                            scope: 'openid email' // Learn about scopes: https://auth0.com/docs/scopes
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
