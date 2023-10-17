
# OpenSource FinTech standards and data

[![Deploy](https://github.com/openfintechio/openfintech/actions/workflows/deploy.yml/badge.svg)](https://github.com/openfintechio/openfintech/actions/workflows/deploy.yml)
[![Total Downloads](https://poser.pugx.org/paycore/openfintech-data/downloads?format=flat-square)](https://packagist.org/packages/paycore/openfintech-data)
[![License: MIT](https://img.shields.io/packagist/l/paycore/openfintech-data?style=flat-square)](https://opensource.org/licenses/MIT)

## About [openfintech.io](https://openfintech.io/)

__Mission:__ speeds up development and helps FinTech-services to communicate in _one language_.

__Provides:__ open data for companies, organizations, currencies, banks, digital exchangers, payment providers (PSP), payment methods, etc.

__Created for:__ communication of cross-integrated micro-services in "one language". 

__The goal is:__ to standardize entity identifiers that are used to exchange information among different web-services.

## Overview

International standards yield technological, economic and social advantages.

Benefits for community:
   
- **Development boost.** Standards speed up the development of new applications and simplify the process of communication between the services.
- **It's Free!** Data and service is available under the MIT License.
- **Collaboration.** It is an open standard and open data, every player of FinTech market can contribute to development and enhancement.
- **Easy integration.** All data is available through native JSON data sources with JSON-Schemas.
- **Rich development tools.** SDKs, UI Viewer and Editor (in-progress), data convertors, validators, formaters, etc.

## Data

OpenFinTech catalog includes Data and __Unified Identification Codes__ of the FinTech industry and world-wide services like:

- Currencies: National, Digital, Virtual, Cryptocurrencies, etc.
- Banks and Branches.
- Payment Service Providers (PSP): Distributors, Aggregators, Collectors, Acquirers, etc.
- Payment & Payout Methods: E-wallets, Bank Cards, Prepaid Cards, SSK, Alternative Methods, etc.
- Digital Exchangers: Online, Offline, Stock, etc. 

It also includes different resources like entity logos and icons.

## UML

![Class Diagram](uml-class-diagram.png?raw=true "Domain Model")

## Agreement

- Identification code (natural key) MUST be 
    - Unique in data collection
    - Not less than 3 symbols and 
    - Consist of: 
        - Characters a to z
        - Digits 0 through 9
        - Hyphen (-), Dot (.), Underscore (_), But cannot start nor end with them
    - Good example: "paypal", "walletone", "webmoney-transfer", "ingbank.pl"
    - Bad example: "pay_pal", "w1", "wm_trn", "ingbpl"
- Resources COULD contains such files as:
    - Icon
        - Filename: icon.[png|svg]
        - Format: Only PNG or SVG (is more preferable).
        - Shape: Icons are made to fit in squares (‘quadratic’), while logos do not have shape restrictions.
        - Size: Icons are usually in 16x16 (favicon) or 512x512 (large size is more preferable).
    - Logo
        - Filename: logo.[png|svg]
        - Format: Only PNG or SVG (is more preferable).
        - Shape: Logos on the other hand are vector based, and can be scaled into any size without losing quality since they also need to be used in different materials related to the organization it represents such as brochures, business cards, website, banner, signage etc.
        - Size: Min width is 200 px. Max width is 2000 px. Large size is more preferable.
- _Translatable_ values MUST be:
    - Format: key-value array, where key is 2-char ISO language code (example: "en", "ge", "uk"). 
    - Example: "name": {"en": "Yandex.Money", "ru": "Яндрекс.Деньги"}  
        
## Community

Feel free to contribute new FinTech entities and data.

# SVG optimization
```bash
find . -name *svg -exec mv {} /tmp/to_be_optimized.svg \; -exec scour -i /tmp/to_be_optimized.svg -o {} --enable-viewboxing --enable-id-stripping --enable-comment-stripping --shorten-ids --indent=none \;
```  
# Links

## Libraries

https://github.com/moneyphp/iso-currencies
Provides up-to-date list of ISO 4217 currencies

https://github.com/musalbas/mcc-mnc-table
Mobile Country Codes (MCC) and Mobile Network Codes (MNC) table in CSV, JSON and XML. Updated daily.

https://github.com/greggles/mcc-codes
A public repository of Merchant Category Codes (MCC) in formats easier to read than most places (i.e. not a PDF).

https://github.com/umpirsky/currency-list
List of all currencies with names and ISO 4217 codes in all languages and all data formats.

https://github.com/umpirsky/country-list
List of all countries with names and ISO 3166-1 codes in all languages and all data formats.

## Tools

* https://www.jsonschemavalidator.net/
* https://www.jsonschema.net/
* https://stedolan.github.io/jq/
* https://jqplay.org/
