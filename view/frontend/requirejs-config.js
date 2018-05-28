var config = {
    paths: {
        'Auth0Lock': 'Magefox_SSOIntegration/js/libs/auth0-lock.min'
    },
    shim: {
        'Auth0Lock': {
            "exports": "Auth0Lock"
        }
    },
    map: {
        '*': {
            'magefox.auth0': 'Magefox_SSOIntegration/js/magefox.auth0'
        }
    }
};
