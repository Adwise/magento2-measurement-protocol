## Adwise Analytics 2.0 Upgrade Guide

This guide will help you upgrade from Adwise Analytics 1.x to 2.x. This guide assumes you already have a working 1.x version of the module installed.

### Upgrade the module

```bash
composer require adwise/magento2-gamp:^2.0
```

### Run the upgrade script

```bash
php bin/magento setup:upgrade
```

### Update your data providers

GA4 has made changes to the way data should be passed into the Measurement Protocol, this means you need to update your data providers to match the new format.

First, make sure your data providers extend the new `Adwise\Analytics\Api\MeasurementProtocol` interfaces instead of the deprecated `Adwise\Analytics\Api` interfaces.

* `Adwise\Analytics\Api\OrderDataProviderInterface` has become `Adwise\Analytics\Api\MeasurementProtocol\OrderDataProviderInterface`
  * **Watch out** You can no longer send custom dimensions in these providers. You need to use the new `UserPropertyProviderInterface` for sending custom data to GA4
* `Adwise\Analytics\Api\CreditMemoProviderInterface` has become `Adwise\Analytics\Api\MeasurementProtocol\CreditMemoProviderInterface`
  * **Watch out** You can no longer send custom dimensions in these providers. You need to use the new `UserPropertyProviderInterface` for sending custom data to GA4


### Update your di.xml configurations

The places where you need to hook your data providers into the module have changed. Below is a list of the changed hooks, new hooks can be found in etc/di.xml

* `Adwise\Analytics\Model\OrderDataProviders` has become `Adwise\Analytics\Model\MP\OrderDataProviders`
* `Adwise\Analytics\Model\CreditMemoDataProviders` has become `Adwise\Analytics\Model\MP\CreditMemoDataProviders`

### Update your configuration

A number of configuration options have been moved or removed. Please reconfigure the module according to the instructions in the README

