# Frontend Grid Fieldtype

<!-- statamic:hide -->

![Statamic 5](https://img.shields.io/badge/Statamic-5.0-FF269E?style=for-the-badge&link=https://statamic.com)
[![Frontend Grid Fieldtype for Statamic on Packagist](https://img.shields.io/packagist/v/mitydigital/statamic-frontend-grid-fieldtype?style=for-the-badge)](https://packagist.org/packages/mitydigital/statamic-frontend-grid-fieldtype/stats)

---

<!-- /statamic:hide -->

> An opinionated frontend grid fieldtype for Statamic forms.

This is currently a work in progress.

There is no support offered at this time.

## Installation
```bash
composer require mitydigital/statamic-frontend-grid-fieldtype
```

## Config

If you're using a JS driver, such as Alpine, that supports scoping, your fieldtype
configuration can include this scope.

You can also add labels for:
- Set (visible in the Submissions only)
- Add Row Label
- Delete Row Label

Just like Grid, you can also add a min and max number of rows.

## Your form setup

On your Form template, outside of any Alpine work, include:

```antlers
{{ if frontend_grid:has }}
{{ partial:statamic-frontend-grid-fieldtype::snippets/frontend_grid }}
{{ /if }}
```

This will include the smarts for the Frontend Grid fieldtype if the current context
has a Frontend Grid fieldtype. By default it is looking for a form with the handle
provided by a `form` handle. You can change this by passing a `handle` parameter to 
the `:has` method.

## Alpine

This is designed to work within a scoped setup.

It will require your form helper be available with the `form` variable.

## Layouts

Publish layouts:
```bash
php artisan vendor:publish --tag=statamic-frontend-grid-fieldtype-views
```

There is a layout for:
1. Rendering the fieldtype on the frontend, and
2. Rendering the value of the fieldtype in a Submission email

### Render Tag

A `frontend_grid` tag is included for you to include in your HTML templates.

In your HTML template, you can get the rendered output using:
```antlers
{{ frontend_grid:html }}
```

This will use the second layout mentioned above - and by default is a table that 
respects the grid widths.

In your text template, you can get the rendered output using:
```antlers
{{ frontend_grid:text }}
```

This is a simple text output, with label+linebreak+value loops for each set.

There is also a `has` option to detect if the current context has a Frontend Grid fieldtype.
You can provide an optional `handle` parameter that should be the field that stores your Form's
handle. By default this is `form`.

## Logic

**This is only needed if you need to change the logic**
```bash
php artisan vendor:publish --tag=statamic-frontend-grid-fieldtype-logic
```

## Credits

- [Marty Friedel](https://github.com/martyf)
