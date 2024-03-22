## 1.3.8 (2023-10-26)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.3.8)

*  [BUGFIX][IN23-62] Added last visited column in admin grid. *(Simon Vianen)*

## 1.3.5 (2023-10-26)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.3.5)

*  [BUGFIX][IN23-251] Convert setup scripts to db_schema.xml conform newer Magento 2 standards. *(Boris van Katwijk)*


## 1.3.4 (2023-05-09)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.3.4)

*  Declared dynamic property to fix PHP8.2 error *(Experius)*


## 1.3.3 (2022-04-28)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.3.3)

*  [BUGFIX] - Deprecated Functionality: explode(): Passing null to parameter #2 ($string) of type string is deprecated *(Ruben Panis)*


## 1.3.2 (2021-08-02)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.3.2)

*  [BUGFIX] Use page_not_found_view ACL resource *(Lewis Voncken)*


## 1.3.1 (2021-07-15)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.3.1)

*  Update README.md *(Experius)*
*  [BUGFIX][STJ-937] Added classWhitelist on the configurable object. *(Gijs Blanken)*


## 1.3.0 (2020-09-18)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.3.0)

*  [PERFORMANCE] Added index to the experius_page_not_found *(Lewis Voncken)*


## 1.2.8 (2020-05-06)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.2.8)

*  Added handout for product import to readme.md *(pascalexperius)*


## 1.2.7 (2019-11-27)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.2.7)

*  [BUGFIX] Magento 2.3.3 admin grid fix *(dheesbeen)*


## 1.2.6 (2019-08-30)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.2.6)

*  [BUGFIX] - Removed use of non-existent variable *(Ruben Panis)*
*  [REFACTOR] - Fixed some code standard bugs/warnings *(Ruben Panis)*
*  [BUGFIX] - Fixed exception message with correct variablename *(Ruben Panis)*
*  [REFACTOR] Change way to ignore exit with code sniffer, now passes checks *(Ruben Panis)*


## 1.2.5 (2018-01-11)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.2.5)

*  [BUGFIX] Forward to 410 instate of redirecting to the controller to prevent a 302 redirect *(Lewis Voncken)*


## 1.2.4 (2017-10-24)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.2.4)

*  [BUGFIX] Updated module.xml version to 1.0.1 *(Mr. Lewis)*


## 1.2.3 (2017-10-20)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.2.3)

*  [BUGFIX] import getoptions error, grid history save *(Derrick Heesbeen)*


## 1.2.2 (2017-10-20)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.2.2)

*  [TASK] Repository changes Pull request Bart *(Derrick Heesbeen)*


## 1.2.1 (2017-10-20)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.2.1)

*  [TASK] add duplicate from_url check *(Derrick Heesbeen)*


## 1.2.0 (2017-10-20)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.2.0)

*  [TASK] added 410 redirect option and 410 response page *(Derrick Heesbeen)*


## 1.0.14 (2017-10-03)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.0.14)

*  Revert "[TASK] Made from_url unique, if database already contains duplicates those are merged" *(Derrick Heesbeen)*


## 1.1.0 (2017-09-15)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.1.0)

*  [TASK] Add compatibility with Experius ContentPage module *(bartlubbersen)*
*  [TASK] Made from_url unique, if database already contains duplicates those are merged [TASK] If new redirect is added through back-end but from_url already exists the page with that url is updated and warning is shown [BUGFIX] There can't be duplicate from_url's because those caused the issue that only the first could be redirected *(Bart Lubbersen)*


## 1.0.13 (2017-09-06)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.0.13)

*  [FEATURE] find and replace command *(Derrick Heesbeen)*
*  [BUGFIX] moved replace script to separated module *(Derrick Heesbeen)*
*  [BUGFIX] Fix to make acl work *(Derrick Heesbeen)*


## 1.0.12 (2017-08-22)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.0.12)

*  [BUGFIX] bug in import when using a replace url, bug with nog redirecting in live modus *(Derrick Heesbeen)*


## 1.0.11 (2017-08-22)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.0.11)

*  [BUGFIX] unique command name in di xml decleration *(Derrick Heesbeen)*


## 1.0.10 (2017-08-22)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.0.10)

*  [FEATURE] Console import script for csv redirect list *(Derrick Heesbeen)*


## 1.0.9 (2017-08-18)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.0.9)

*  [BUGFIX] add params to redirect url check on excisting params *(Derrick Heesbeen)*


## 1.0.8 (2017-08-18)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.0.8)

*  Update ActionPredispatch.php *(Derrick Heesbeen)*
*  [FEATURE] Made setting less complex :-) *(Derrick Heesbeen)*


## 1.0.6 (2017-08-18)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.0.6)

*  [FEATURE] include params for saved from url *(Derrick Heesbeen)*


## 1.0.5 (2017-08-15)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.0.5)

*  [FEATURE] added settings, added exluded params options *(Derrick Heesbeen)*


## 1.0.4 (2017-07-31)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.0.4)

*  [BUGFIX] Temp Fix for redirect to work with fpc *(Derrick Heesbeen)*


## 1.0.3 (2017-07-27)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.0.3)

*  [BUGFIX] solved fatal error of Interface implementation *(Lewis Voncken)*


## 1.0.2 (2017-07-25)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.0.2)

*  Fix typos *(Tim Neutkens)*
*  [TASK] make link in grids and add params to redirect *(Derrick Heesbeen)*


## 1.0.1 (2017-06-22)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.0.1)

*  [FEATURE] Updated readme with menu item location *(Derrick Heesbeen)*
*  [BUGFIX] admin form save issue, renamed to correct title *(Derrick Heesbeen)*


## 1.0.0 (2017-06-22)

[View Release](git@github.com:experius/Magento-2-Module-PageNotFound.git/commits/tag/1.0.0)

*  first commit *(Derrick Heesbeen)*


