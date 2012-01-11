[PHP-Decoda](http://milesj.me/code/php/decoda) integration in Symfony2

A lightweight lexical string parser for BBCode styled markup.

## Installation

### Install PHP-Decoda

#### Download

With submodule: `git submodule add git://github.com/milesj/php-decoda.git vendor/php-decoda`

With clone: `git clone git://github.com/milesj/php-decoda.git vendor/php-decoda`

Using the vendors script

Add the following lines in your ``deps`` file::

    [php-decoda]
    git=http://github.com/milesj/php-decoda.git

Run the vendors script::

    ./bin/vendors install

#### Register autoloading

    // app/autoload.php

    $loader->registerPrefixes(array(
        ...
        'Decoda' => __DIR__.'/../vendor/php-decoda/decoda',
    ));

### Install FMBbCodeBundle

#### Download

With submodule: `git submodule add git://github.com/helios-ag/FMBbCodeBundle.git bundles/FM/BbCodeBundle`

With clone: `git clone git://github.com/helios-ag/FMBbCodeBundle bundles/FM/BbCodeBundle`

Using the vendors script

Add the following lines in your ``deps`` file::

    [php-decoda]
    git=http://github.com/milesj/php-decoda.git

Run the vendors script::

    ./bin/vendors install

#### Register autoloading

    // app/autoload.php

    $loader->registerNamespaces(array(
        ...
        'FM' => __DIR__.'/../vendor/bundles',
        // your other namespaces
    ));

#### Register the bundle

    // app/AppKernel.php

    public function registerBundles()
    {
        return array(
            // ...
            new FM\BbCodeBundle\FMBbCodeBundle(),
            // ...
        );
    }

### Basic configuration

## Make the Twig extensions available by updating your configuration:

   this is mandatory parameters:

   fm_bb_code:
      locale: ru-ru

    By default only "default" filter enabled, which provide support
    for [b], [i], [u], [s], [sub], [sup] BBCodes

# Examples to use the extension in your Twig template

     {{'[b]Bold text[/b]'|BBCode}}<br />
     {{'[u]Underlined text[/u]'|BBCode}}<br />
     {{'[i]Italic text[/i]'|BBCode}}<br />

     After enabling "quote" filter, you can do such things:

      {{'[quote="helios"]My quote[/quote]'|BBCode}}<br />
     
## Full tree of parameters

    fm_bb_code:
      locale: ru-ru
      xhtml: true
      filters:
        default: enabled
        block: enabled
        code: enabled
        email: enabled
        image: enabled
        list: enabled
        quote: enabled
        text: enabled
        url: enabled
        video: enabled

### TODO:

    Add support of whitelist tags and hooks.