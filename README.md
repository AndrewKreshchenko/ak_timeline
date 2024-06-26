[![TYPO3 11](https://img.shields.io/badge/TYPO3-11-orange.svg)](https://get.typo3.org/version/11)

> **Warning**
> The package is still alpha, is on development phase.

# TimelineVis TYPO3 Extension

## Prerequisites

* PHP v. 7+
* Works on Typo3 v.11 for now. For lower Typo3 versions (mostly v10) please consider `*.log` files or `*.md` documentation that exist near the same actual files (see [installation point 1](#installation)).
* 64-bit operation system. Mostly for dates computation and operations with large timestamps due to PHP [limits](https://www.php.net/manual/en/language.types.integer.php#language.types.integer.overflow).

## Installation

1. Upload the package in `app/public/typo3conf/ext/` directory.

2. Update `composer.json` in the `app/` (or `public/`). As the code as a early version, I'd recommend install it manually by changing `composer.json` and then run `composer update`.

3. Update Database with backend tool (in Typo3 11: Menu "Maintaince" -> "Analyze Database Structure").
4. Check out the plugin!

## Contribution

Please make sure to read the [Contributing Guide](/Documentation/contribute.md) before making a pull request.

Thanks goes to these wonderful people

<a href="https://github.com/chrissonntag" title="Christian Sonntag">
  <img width="90" src="https://avatars.githubusercontent.com/u/39337016?v=4" />
</a>

## License

TimelineVis is licensed under a [GPL-2.0 license](./LICENSE).

## Table of contents
- [Setup](/Documentation/setup.md)
- [Customize](/Documentation/customize.md)
- [Extending existing functionality](/Documentation/extend.md)
- [Contribute](/Documentation/contribute.md)
