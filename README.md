# Mega Bank Payment 1.0.1 for Magento 2.3.0 - 2.4.2

## Documentation
[Magento 2 MegaBank Payment](https://github.com/megabank-development/magento2-payment/blob/master/Magento%202%20MegaBank%20Payment.pdf)

### Install/Update via composer

We recommend you to use composer. It is easy to install, update and maintenance.

Run the following command in Magento 2 root folder.

#### Install

```
php bin/magento maintenance:enable
composer require megabank/module-payment
php bin/magento module:enable MegaBank_Payment
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
php bin/magento maintenance:disable
```

#### Upgrade

```
php bin/magento maintenance:enable
composer update megabank/module-payment
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy
php bin/magento cache:flush
php bin/magento maintenance:disable
```
