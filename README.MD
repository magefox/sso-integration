# Magento 2 SSO Integration
## Description
Single Sign On (SSO) Integrations which includes [Auth0](https://auth0.com), SAML2 protocol(Coming Soon) enable the use of external services for single sign-on.
<!--- [Try Demo](http://demo.magefox.com) --->

## Features
- Auth0 integration
- SAML2 protocol integration (Coming Soon)

# Installation
## Manual (without composer)
- Download zip file of thelast version of this extension under release tab
- Extract files in the Magento root directory in the folder app/code/Magefox/SSOIntegration
- Enable the extension
```
php bin/magento --clear-static-content module:enable Magefox_SSOIntegration
```
- Upgrade Magento setup
```
php bin/magento setup:upgrade
```

## With Composer
In the Magento root directory
- Install the module
```
composer require magefox/sso-integration
php bin/magento module:enable Magefox_SSOIntegration 
php bin/magento setup:upgrade
```

## Cleaning
- Upgrade Magento setup
```
php bin/magento setup:upgrade
```
- Clear cache
```
php bin/magento cache:flush
```

## Changelog
### 1.0.0
* Initial version.