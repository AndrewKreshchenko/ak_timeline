[![TYPO3 11](https://img.shields.io/badge/TYPO3-11-orange.svg)](https://get.typo3.org/version/11)
[![TYPO3 10](https://img.shields.io/badge/TYPO3-10-orange.svg)](https://get.typo3.org/version/10)

# TYPO3 Extension `ak_timeline`

## Prerequisites

* PHP v. 7-8 (currently tested on v. 7).
* Works on Typo3 v.11 by default. For lower Typo3 versions, please consider `*.log` files or `*.md` documentation that exist near the same actual files (see [installation point 1](#installation)).
* 64-bit operation system, but I'm inclined to think you have. :smile: Mostly for dates computation and operations with large timestamps due to PHP [limits](https://www.php.net/manual/en/language.types.integer.php#language.types.integer.overflow).

## Installation

1. Uplopad the package in `app/public/typo3conf/ext/` directory.

2. Update `composer.json` in the `app/` (or `public/`) directory have following rules:
```json
{
	"name": "typo3/cms-base-distribution",
	"description" : "TYPO3 CMS Base Distribution",
	"license": "GPL-2.0-or-later",
	"config": {
		"allow-plugins": {
			"typo3/class-alias-loader": true,
			"typo3/cms-composer-installers": true
		},
		"platform": {
			"php": "7.4.1"
		},
		"sort-packages": true
	},
	"require": {
		"ak/ak-timelinevis": "@dev",
		"typo3/cms-backend": "^11.5.0",
		"typo3/cms-belog": "^11.5.0",
		"typo3/cms-beuser": "^11.5.0",
		"typo3/cms-core": "^11.5.0",
		"typo3/cms-dashboard": "^11.5.0",
		"typo3/cms-extbase": "^11.5.0",
		"typo3/cms-extensionmanager": "^11.5.0",
		"typo3/cms-felogin": "^11.5.0",
		"typo3/cms-filelist": "^11.5.0",
		"typo3/cms-fluid": "^11.5.0",
		"typo3/cms-fluid-styled-content": "^11.5.0",
		"typo3/cms-form": "^11.5.0",
		"typo3/cms-frontend": "^11.5.0",
		"typo3/cms-impexp": "^11.5.0",
		"typo3/cms-info": "^11.5.0",
		"typo3/cms-install": "^11.5.0",
		"typo3/cms-recordlist": "^11.5.0",
		"typo3/cms-rte-ckeditor": "^11.5.0",
		"typo3/cms-seo": "^11.5.0",
		"typo3/cms-setup": "^11.5.0",
		"typo3/cms-sys-note": "^11.5.0",
		"typo3/cms-t3editor": "^11.5.0",
		"typo3/cms-tstemplate": "^11.5.0",
		"typo3/cms-viewpage": "^11.5.0"
	},
	"repositories": [
		{
			"type": "composer",
			"url": "https://composer.typo3.org/"
		},
		{ "type": "path", "url": "packages/*" }
	],
	"scripts":{
		"typo3-cms-scripts": [
			"typo3cms install:fixfolderstructure"
		],
		"post-autoload-dump": [
			"@typo3-cms-scripts"
		]
	}
}
```

Stating from Typo3 11 use plugin [numbered_pagination](https://github.com/georgringer/numbered_pagination) because widget is not works from Typo3 v.11 anymore.
You could install with composer:
```sh
composer require georgringer/numbered-pagination
```

3. Update Database with backend tool (in Typo3 11: Menu "Maintaince" -> "Analyze Database Structure").
4. Check out the plugin. Do not hesitate to ask if something doesn't work :smirk:
