FMBBCodeBundle
==============

[![Build Status](https://travis-ci.org/helios-ag/FMBbCodeBundle.png?branch=master)](https://travis-ci.org/helios-ag/FMBbCodeBundle)

[![knpbundles.com](http://knpbundles.com/helios-ag/FMBbCodeBundle/badge)](http://knpbundles.com/helios-ag/FMBbCodeBundle)

[PHP-Decoda](https://github.com/milesj/decoda) integration in Symfony2

A lightweight lexical string parser for BBCode styled markup.

## Installation

To install this bundle, you'll need both the [Decoda library](https://github.com/milesj/decoda)
and this bundle. Installation depends on how your project is setup:

### Step 1: Installation

Using Composer, just add the following configuration to your `composer.json`:

Or you can use composer to install this bundle:
Add FMBbcodeBundle in your composer.json:

```json
{
    "require": {
        "helios-ag/fm-bbcode-bundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update helios-ag/fm-bbcode-bundle
```

### Step 2: Enable the bundle

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
### Step 3: Dump emoticons (optional)

To enable emoticons via emoticon hook, use the following command to copy emoticons images to
public folder (web/emoticons)

``` bash
    ./app/console bbcode:dump
```

## Basic configuration

### Make the Twig extensions available by updating your configuration:

    By default only "default" filter enabled, which provide support
    for [b], [i], [u], [s], [sub], [sup], [abbr], [br], [hr], [time]
    BBCodes

### Examples to use the extension in your Twig template

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

Please keep in mind, that whitelist tags suppress tags, that applied by filters configuration.


### Strip filter
To clear text from any bbcodes use bbcode_clean filter:
example:
``` jinja
{{'[b]some text[/b]'|bbcode_clean}}
```
This filter will eliminate any known to decoda tags


## Advanced configuration

### Overriding messages

Some templates and hooks, use text strings, that can be translated into different languages, the original file
located under decoda/config directory, but content of this file can be overriden with messages option, under
messages: node. File should be json formatted.

```yaml
fm_bbcode:
    config:
      messages: @SomeBundle/Resources/config/messages.json
```

### Adding own templates

Your own templates can be defined at templates node, the example below shows how:
```yaml
    fm_bbcode:
        config:
          templates:
            - path: @SomeBundle/Resources/views/templates
```
Template examples can be found inside decoda library


### Adding own filter

To enable a custom filter, add it as a regular service in one of your configuration, and tag it with `fm_bbcode.decoda.filter`:

```yaml
services:
  acme_demo.decoda.filter.your_filter_name:
    class: Fully\Qualified\Filter\Class\Name
    tags:
      - { name: fm_bbcode.decoda.filter, id: your_filter_name }
```

Your service must implement the `Decoda\Filter` interface.

If your service is created by a factory, you **MUST** correctly set the class parameter for this tag to work correctly.


### Adding own hook

To enable a custom hook, add it as a regular service in one of your configuration, and tag it with `fm_bbcode.decoda.hook`:

```yaml
services:
  acme_demo.decoda.hook.your_hook_name:
    class: Fully\Qualified\Hook\Class\Name
    tags:
      - { name: fm_bbcode.decoda.hook, id: your_hook_name }
```

Your service must implement the `Decoda\Hook` interface.

If your service is created by a factory, you **MUST** correctly set the class parameter for this tag to work correctly.


### Customize your own emoticons

Your own emoticons can be defined at `emoticon` node, the example below shows how:

```yaml
fm_bbcode:
  emoticon:
    resource: path/to/emoticons.yml
```

```yaml
# path/to/emoticons.yml
imports:
  - { resource: path/to/another/emoticons.yml }

emoticons:
  my_emoticon:
    url:   # Default: %fm_bbcode.emoticon.path%/my_emoticon.png
    html:  # Default: <img src="%fm_bbcode.emoticon.path%/my_emoticon.png" alt="" >
    xHtml: # Default: <img src="%fm_bbcode.emoticon.path%/my_emoticon.png" alt="" />
    smilies:
      - ":my_emoticon:"
```


## Contributors

* Gaiffe Antoine [toinouu](https://github.com/toinouu)
* Luis Íñiguez [idetia](https://github.com/idetia)
* Sebastian [slider](https://github.com/slider)
* [olleyyy](https://github.com/olleyyy)
* Dirk Olbertz [dolbertz](https://github.com/dolbertz)
* Florian Krauthan [fkrauthan](https://github.com/fkrauthan)
* [predakanga](https://github.com/predakanga)
* Dan [piratadelfuturo](https://github.com/piratadelfuturo)
* Alexandre Quercia [alquerci](https://github.com/alquerci)

