[![TYPO3 11](https://img.shields.io/badge/TYPO3-11-orange.svg)](https://get.typo3.org/version/11)
[![TYPO3 10](https://img.shields.io/badge/TYPO3-10-orange.svg)](https://get.typo3.org/version/10)

# TimelineVis TYPO3 Extension

## Prerequisites

* PHP v. 7-8 (currently tested on v. 7).
* Works on Typo3 v.11 by default. For lower Typo3 versions, please consider `*.log` files or `*.md` documentation that exist near the same actual files (see [installation point 1](#installation)).
* 64-bit operation system, but I'm inclined to think you have. :smile: Mostly for dates computation and operations with large timestamps due to PHP [limits](https://www.php.net/manual/en/language.types.integer.php#language.types.integer.overflow).
* Stating from Typo3 11 use peer dependency [numbered_pagination](https://github.com/georgringer/numbered_pagination) because widget is not works from Typo3 v.11 anymore.

## Installation

1. Upload the package in `app/public/typo3conf/ext/` directory.

2. Update `composer.json` in the `app/` (or `public/`). Add configuration for alpha version in `package.json`:
```json
"require": {
	"ak/ak-timelinevis": "@dev"
},
"repositories": [
	{
		"type": "composer",
		"url": "https://composer.typo3.org/"
	},
	{ "type": "path", "url": "packages/*" }
]
```

3. Update Database with backend tool (in Typo3 11: Menu "Maintaince" -> "Analyze Database Structure").
4. Check out the plugin. Do not hesitate to ask if something doesn't work :smirk:

## Table of contents
- [Setup](/Documentation/setup.md)
- [Customize](/Documentation/customize.md)
- [Extending existing functionality](/Documentation/extend.md)
- [Contribute](/Documentation/contribute.md)
