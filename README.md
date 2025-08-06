# Distribution API client

## About

Download & upgrade PrestaShop's native modules from your backoffice
Display Github contributors in the new community section

## Compatibility

PrestaShop: `9.0.0` or later

## Reporting issues

You can report issues with this module in the main PrestaShop repository. [Click here to report an issue][report-issue].

## Requirements

Required only for development:

- composer
- Node 20 or later

## Installation

Install composer dependencies.
```bash
composer install --no-dev
composer dump-autoload -o
```

Build assets (for local development)

```bash
cd vue-app
pnpm install
pnpm  build
```

This will build the Vue app in the `views/js/vue` folder.

## Contributing

PrestaShop modules are open source extensions to the [PrestaShop e-commerce platform][prestashop]. Everyone is welcome and even encouraged to contribute with their own improvements!

Just make sure to follow our [contribution guidelines][contribution-guidelines].

## License

This module is released under the [Academic Free License 3.0][AFL-3.0]

[report-issue]: https://github.com/PrestaShop/PrestaShop/issues/new/choose
[prestashop]: https://www.prestashop-project.org/
[contribution-guidelines]: https://devdocs.prestashop-project.org/8/contribute/contribution-guidelines/project-modules/
[AFL-3.0]: https://opensource.org/licenses/AFL-3.0
