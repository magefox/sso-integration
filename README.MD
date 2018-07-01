# Magento 2 SSO Integration
[![Build Status](https://img.shields.io/travis/magefox/sso-integration.svg?style=flat)](https://travis-ci.org/magefox/sso-integration)
[![Latest Stable Version](https://poser.pugx.org/magefox/sso-integration/v/stable)](https://packagist.org/packages/magefox/sso-integration)
[![Total Downloads](https://poser.pugx.org/magefox/sso-integration/downloads)](https://packagist.org/packages/magefox/sso-integration)
[![Software License](https://img.shields.io/badge/license-GPL-green.svg?style=flat)](http://opensource.org/licenses/gpl-2.0.php)
## Description
Single Sign On (SSO) Integrations which includes [Auth0](https://auth0.com), SAML2 protocol(Coming Soon) enable the use of external services for single sign-on.
<!--- [Try Demo](http://demo.magefox.com) --->

## Features
- 100% open source.
- Easily switch On/Off the Module.
- Supports Single Sign On (IdP and SP initiated)
- Supports Magento Multi-stores.
- Supported 3 party services: [Auth0](https://auth0.com), SAML2 Protocol (Coming soon)

### Auth0
- Use auth0-lock js framework to login.
- Command line to sync existed customers to Auth0 service.
```
bin/magento sso:auth0:sync
```

## Installation
### Manual (without composer)
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

### With Composer
In the Magento root directory
- Install the module
```
composer require magefox/sso-integration
php bin/magento module:enable Magefox_SSOIntegration 
php bin/magento setup:upgrade
```

### Cleaning
- Upgrade Magento setup
```
php bin/magento setup:upgrade
```
- Clear cache
```
php bin/magento cache:flush
```

## Settings
The Settings of the extension are available at Stores > Configuration. At the Magefox tab, the "SSO integration" link.

There you will be able to fill several sections:
- **Status**. To enable or disable the extension.
- **Provider**. Select provider Which you will be use (ex: [Auth0](https://auth0.com)).
- And another options depend your provider you selected.  

## Release Notes
### 1.0.0
* Initial version.
