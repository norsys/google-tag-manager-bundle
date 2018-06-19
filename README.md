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

### Bundle implementation

There are two ways to implement the google tag manager:
- [Using the Twig Extension](#twig:extension)
- [Using the Twig partial](#twig:partial)

<a name="twig:extension"></a>

#### Using the Twig Extension

The bundle exposes a twig function `render_google_tag_manager(route)` taking the route as argument, which renders the GTM as a JSON object:

```twig
<script>
    dataLayer = [{{ render_google_tag_manager(app.request.attributes.get('_route')) }}];
</script>
```

<a name="twig:partial"></a>

#### Using the Twig partial

To ease quick integration, the bundle provides a `twig` partial, taking 2 mandatory arguments:


- `google_key`: Your account ID key for google tag manager
- `route`: The route of the page including the partial

Example:

```twig
{# Google Tag Manager #}
{% block google_tag_manager %}
    {% include '@NorsysGoogleTagManager/google_tag_manager.html.twig' with {
        'route': app.request.attributes.get('_route'),
        'google_key': 'GTM-XXXXXX'
    } %}
{% endblock google_tag_manager %}

```

### Bundle configuration

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
    default:
        static:
            virtualPageURL: '/home'

    pages:
        faq:
            static:
                virtualPageURL: '/otherLink/faq'
```

The bundle will merge defaults and per-route static parameters, and issue the resulting data layer.

Here the resulting generated html for the page responding to the `faq` route:

```html
<script>
    dataLayer = [ { "virtualPageURL": "/otherLink/faq" } ];
</script>

<noscript>
    <iframe src="//www.googletagmanager.com/ns.html?id=GTM-XXXXXX" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>

<script>
    (function(w,d,s,l,i){w[l]=w[l]||[];
        w[l].push({'gtm.start':  new Date().getTime(),event:'gtm.js'});
        var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';
        j.async=true;
        j.src='//www.googletagmanager.com/gtm.js?id='+i+dl;
        f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-XXXXXX');
</script>
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

Now, taking for granted the 2 above `static` and `dynamic` configured parameters, the resulting generated html for the `faq` route would be something like:

```html
<script>
    dataLayer = [ { "virtualPageURL": "/otherLink/faq", "acmeDynamicParam": "constructed-value" } ];
</script>

<noscript>
    <iframe src="//www.googletagmanager.com/ns.html?id=GTM-XXXXXX" height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>

<script>
    (function(w,d,s,l,i){w[l]=w[l]||[];
        w[l].push({'gtm.start':  new Date().getTime(),event:'gtm.js'});
        var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';
        j.async=true;
        j.src='//www.googletagmanager.com/gtm.js?id='+i+dl;
        f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-XXXXXX');
</script>
```
