[![Build Status](https://scrutinizer-ci.com/g/gplcart/social_login/badges/build.png?b=master)](https://scrutinizer-ci.com/g/gplcart/social_login/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/gplcart/social_login/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/gplcart/social_login/?branch=master)

Social Login is a [GPL Cart](https://github.com/gplcart/gplcart) module that allows users to register and login to your GPL Cart site with their existing accounts from social networks.

Currently supported providers:

- Facebook
- Google+

Requirements:

- CURL


In order to use this module you need to obtain OAuth 2.0 credentials from corresponding providers. You only need `Client Id/App ID` and `Client secret/App Secret`. For redirect URIs specify `http://yourdomain.com/oauth`

**Installation**

1. Download and extract to `system/modules` manually or using composer `composer require gplcart/social_login`. IMPORTANT: If you downloaded the module manually, be sure that the name of extracted module folder doesn't contain a branch/version suffix, e.g `-master`. Rename if needed.
2. Go to `admin/module/list` end enable the module
3. Set up your API credentials and adjust other settings on `admin/module/settings/social_login`
4. Test it! Log out then log in on `http://yourdomain.com/login` using a social network button