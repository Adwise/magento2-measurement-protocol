<a href="https://www.adwise.nl/">
    <img src="https://cdn.adwise.nl/hosted_files/github/logo.webp" alt="Adwise" title="Adwise" align="right" height="50" />
</a>

# Magento 2 Analytics Measurement Protocol

This module aims to send transactions server-side to a Google Analytics property using the measurement protocol. In the traditional frontend implementation a lot of transactions are lost due to endusers not reaching the success page. This module aims to fix this problem due to sending the data directly to the property via the measurement protocol.

This module was a private repo at first. By popular demand we made this open source (and to contribute to the open source community). If you use the private repo at the moment you can drop in replace it with this open source variant.

## Features
- Toggle in Admin to enable/disable module
- Send brand information in transation data
- Send categories in transation data
- Exclude unwanted categories
- Customize event data
- Send transactions automaticaly
- Send credit memo automaticaly
- Send transaction / credit via CLI
- Debugging payload to system log

## Module Installation
`composer require adwise/analytics`

`php bin/magento setup:upgrade`

## Configuration

- Go to Stores > Config > Adwise > Analytics
- Set "Enable Measurement Protocol" to Yes
- Add Google Analyics GA Tracking ID to "Tracking ID"
- Enable (or disable) hits under "Hit configuration"

## Changelog

See <a href="changelog.MD">CHANGELOG.MD</a>

## Roadmap

- Google Analytics 4 support
- Changing cookie saving to backend instead of shipping save in checkout

## License

 The Adwise Magento 2 Measurement Protocol module is licensed under the MIT license

 ## Support

 Feel free to open a issue or feature request on Github if you encounter any problems with this module.

 Made with love by <a href="https://www.adwise.nl/">Adwise - Your Digital Brain</a>