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
