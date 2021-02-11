# craft-b
Shared module for traditional Craft/Twig sites

## Installation

```shell
$ composer require ether/craft-b
```

In `config/app.php`:

```php
<?php

return [
    'modules' => [
        'craft-b' => [
            'class' => '\ether\craftb\CraftB',        
        ],
    ],
    'bootstrap' => ['craft-b'],
];
```

## Twig

### Atom

Renders an atom (basically a module or component but with a shorter name).  
Optionally you can pass some variables. The context is never passed to the atom,
and it will fail silently if it doesn't exist.  
By default, it will load templates from the `_atoms` directory in your 
`templates` folder. You can change this by adding a [config file](#config). 

```twig
{% atom 'hero' {} %}
```

Also supports `children` (will inject a variable called `children`):

```twig
{% atom 'hero' {} %}
    <h1>Hello world!</h1>
{% endatom %}
```

### Critical

For use with [Build](https://github.com/tam/build).  
Outputs critical css into the head of the rendered template.    
Will look for css files in the `_critical` directory in `templates`. You can 
change this in the [config file](#config).

```twig
{% critical 'about' %}
```

## Config

You can override the config by creating a `B.php` file in the `config` directory.  
See [config.php](src/config.php) for the available settings.

## Development

With Docker installed run:

```shell
$ docker-compose run php composer install
```