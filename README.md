[PHP-Decoda](/milesj/php-decoda) integration in Symfony2

A lightweight lexical string parser for BBCode styled markup.

## Installation

To install this bundle, you'll need both the [Decoda library](/milesj/php-decoda)
and this bundle. Installation depends on how your project is setup:

### Step 1: Installation

Add the following lines to your ``deps`` file

```
[php-decoda]
    git=http://github.com/milesj/php-decoda.git

[FMBbcodeBundle]
    git=http://github.com/helios-ag/FMBbCodeBundle.git
    target=bundles/FM/BbcodeBundle
```

Run the vendors script::

    ./bin/vendors install

Or you can use composer to install this bundle:
Add FMBbcodeBundle in your composer.json:

```js
{
    "require": {
        "helios-ag/fm-bbcode-bundle": "*"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update helios-ag/fm-bbcode-bundle
```


### Step 2: Configure the autoloader

Add the following entries to your autoloader:

``` php
<?php
// app/autoload.php

$loader->registerNamespaces(array(
    // ...
       'FM' => __DIR__.'/../vendor/bundles',
        // your other namespaces
    ));

$loader->registerPrefixes(array(
    //...
       'Decoda' => __DIR__.'/../vendor/php-decoda/decoda',
    // your other libraries
    ));
```

### Step 3: Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new FM\BbcodeBundle\FMBbcodeBundle(),
    );
}
```

### Basic configuration

## Make the Twig extensions available by updating your configuration:

    By default only "default" filter enabled, which provide support
    for [b], [i], [u], [s], [sub], [sup] BBCodes


# Examples to use the extension in your Twig template

Define BBCode filter in your config.yml:

``` yaml
    fm_bbcode:
      filter_sets:
        my_default_filter:
          locale: ru
          xhtml: true
          filters: [ default ]
```

And you can do the following:

``` jinja
     {{'[b]Bold text[/b]'|bbcode_filter('my_default_filter')}}
     {{'[u]Underlined text[/u]'|bbcode_filter('my_default_filter')}}
     {{'[i]Italic text[/i]'|bbcode_filter('my_default_filter')}}
```

``` yaml
    fm_bbcode:
      filter_sets:
        my_default_filter:
          locale: ru
          xhtml: true
          filters: [ default, quote ]
          whitelist: [ b, quote ]
```

After enabling "quote" filter, you can do such things:

``` jinja
      {{'[quote="helios"]My quote[/quote]'|bbcode_filter('my_default_filter')}}
```

Also you can define multiple filter sets under filter_sets parameter like this:

``` yaml
    fm_bbcode:
      filter_sets:
        my_forum_filter:
          locale: ru
          xhtml: true
          filters: [ default, quote ]
          whitelist: [ b, quote ]
        my_comment_filter:
          locale: ru
          xhtml: true
          filters: [ default, block, code, email, image, list, quote, text, url, video ]
```

``` jinja
      {{'[quote="helios"]My quote[/quote]'|bbcode_filter('my_forum_filter')}}
      {{'[code]My source code[/code]'|bbcode_filter('my_comment_filter')}}
```

Please keep in mind, that whitelist tags overrides filters configuration.
