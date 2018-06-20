# Norsys Google Tag Manager Bundle

This project is a bundle which eases the configuration and handling of Google Tag Manager.

It provides a bunch of base classes to extend from.


Installation
============

Step 1: Add the repository & download the Bundle
------------------------------------------------

Open a command console, enter your project directory and execute the
following commands:

```bash
# download the latest stable version of this bundle:
$ composer require norsys/google-tag-manager-bundle
```

This command requires you to have `composer` installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Step 2: Register the Bundle
---------------------------

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:


```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Norsys\GoogleTagManagerBundle\NorsysGoogleTagManagerBundle(),
            // ...
        );
        // ...
    }
    // ...
}
```

Step 3: Configuration & Implementation
--------------------------------------

The GTM bundle allows you to define google tag manager parameters, providing a default values management.

### Basic integration

```yaml
# app/config/config.yml
#...
norsys_google_tag_manager:
    id: 'GTM-XXXXXX'
```

#### Template integration

To ease quick integration, the bundle provides a `twig` partial and a javascript file to include in your template:

Example:

```twig
{# Google Tag Manager #}
{% block google_tag_manager %}
    {% include '@NorsysGoogleTagManager/google_tag_manager.html.twig' %}
    {{ asset('web/bundles/norsysgoogletagmanager/js/google-tag-manager.js') }}
{% endblock google_tag_manager %}
```

#### Controlling GTM loading

By default, GoogleTagManager is initialized at the point the javascript file is included.

You can also tied it to a specific DOM event:

```yaml
# app/config/config.yml
#...
norsys_google_tag_manager:
    id: 'GTM-XXXXXX'
    on_event:
        enabled: true
        name: load
        container: body
```

### DataLayer configuration

The GoogleTagManager Bundle handles 2 distinct types of parameters:
- [Static parameters](#usage:static)
- [Dynamic parameters](#usage:dynamic)

 > :warning: As a rule of thumb,  `static` and `dynamic` parameters must have different names.

<a name="usage:static"></a>

#### Static parameters

Here is a very basic config example:

```yaml
# app/config/config.yml
#...
norsys_google_tag_manager:
    data_layer:
        default:
            static:
                virtualPageURL: '/home'

        pages:
            faq:
                static:
                    virtualPageURL: '/otherLink/faq'
```

The bundle will merge defaults and per-route static parameters, and issue the resulting data layer.

The dataLayer sent for the page responding to the `faq` route will be:

```javascript
    dataLayer = [ { "virtualPageURL": "/otherLink/faq" } ];
```

<a name="usage:dynamic"></a>

#### Dynamic parameters

On some situations, it can be useful to have some parameters value changing dynamically upon the context.

For this purpose, the bundle provides support for dynamic parameters.

Each dynamic parameter consists of a service implementing [NorsysGoogleTagManagerBundle\Dynamic\ParameterInterface](src/Dynamic/ParameterInterface.php).

The custom dynamic parameter class will have to implement the 2 following methods:
- `getValue()` The method responsible for calculating the parameters value. It receives the whole merged and resolved config as unique argument
- `getName()`  This method must return the unique parameter name, the one that represents it in the config

```php
# src/AppBundle/DynamicParameter/Acme.php
<?php

namespace AppBundle\DynamicParameter;

use Norsys\GoogleTagManagerBundle\Config\ParameterBag;

use Norsys\GoogleTagManagerBundle\Dynamic\ParameterInterface;

class Acme implements ParameterInterface
{
    public function getValue(ParameterBag $configPage): string
    {
        // do something to calculate value
        // ...

        return $calculatedValue;
    }

    public function getName(): string
    {
        return 'acmeDynamicParam';
    }
}
```

Each dynamic parameter class refered to in the bundle config must be registered, hence declared using the appropriate tag:

```yaml
# app/config/services.yml
#...
    app.google_tag_manager.config.dynamic_parameter.acme:
        class: AppBundle\DynamicParameter\Acme
        tags:
            - { name: 'norsys_google_tag_manager.dynamic_parameter' }

#...
```
Now we can use the parameter in the bundle config, referencing to it via the alias defined by its `getName()` method.


```yaml
# app/config/config.yml
#...
norsys_google_tag_manager:
    data_layer:
        default:
            # ...
            dynamic:
                acmeDynamicParam: ~
            # ...
```


Even if rarely ever needed for most cases, it is possible to pass the parameter an initialization string, and then fetch it in the associated service.

As an example, let's say we want to pass a C-like template string:

```yaml
# app/config/config.yml
#...
norsys_google_tag_manager:
    data_layer:
        default:
            # ...
            dynamic:
                acmeDynamicParam: '%s:%s:%s'
            # ...
```

We can then easily fetch this init value for further processing inside the `getValue()` method:

```php
# src/AppBundle/DynamicParameter/Acme.php
<?php

namespace AppBundle\DynamicParameter;

use Norsys\GoogleTagManagerBundle\Config\ParameterBag;

use Norsys\GoogleTagManagerBundle\Dynamic\ParameterInterface;

class Acme implements ParameterInterface
{
    // ...

    public function getValue(ParameterBag $configPage): string
    {
        // The value passed in the parameter config can be retreived easily
        $initValue = $configPage->get($this->getName());

        // Fetch $locale, $route, $land, for example from an object injected in the service's constructor
        // ...

        $calculatedValue = sprintf($initValue, $locale, $route, $land);

        return $calculatedValue;
    }

    // ...

}
```


Dynamic parameters can also be overriden, on a per-route basis:

```yaml
norsys_google_tag_manager:
    data_layer:
        default:
            # ...
            dynamic:
                acmeDynamicParam: 'whatever-init-value'

        pages:
            faq:
                dynamic:
                    acmeDynamicParam: 'another-init-value'
```

#### Dynamic parameters aliases

Additionnally this bundle also supports aliases for dynamic parameters.

Let's consider using an alias of the previously created `acmeDynamicParam`:


```yaml
norsys_google_tag_manager:
    data_layer:
        aliases:
            myDummyParam: acmeDynamicParam

        default:
            # ...
            dynamic:
                acmeDynamicParam: 'whatever-init-value'
```

Now we can use the aliased name to refer directly to its target:

```yaml
norsys_google_tag_manager:
    data_layer:
        aliases:
            myDummyParam: acmeDynamicParam

        default:
            # ...
            dynamic:
                # The following is equivalent to:
                #
                # acmeDynamicParam: 'whatever-init-value'
                #
                myDummyParam: 'whatever-init-value'
```

### Conclusion

Now, taking for granted the 2 above `static` and `dynamic` configured parameters, the resulting generated dataLayer for the `faq` route would be something like:

```javascript
    dataLayer = [ { "virtualPageURL": "/otherLink/faq", "acmeDynamicParam": "constructed-value" } ];
```
