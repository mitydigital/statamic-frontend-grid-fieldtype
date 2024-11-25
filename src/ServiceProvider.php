<?php

namespace MityDigital\StatamicFrontendGridFieldtype;

use MityDigital\StatamicFrontendGridFieldtype\Fieldtypes\FrontendGridFieldtype;
use MityDigital\StatamicFrontendGridFieldtype\Tags\FrontendGrid;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $fieldtypes = [
        FrontendGridFieldtype::class,
    ];

    protected $tags = [
        FrontendGrid::class,
    ];

    protected $vite = [
        'input' => [
            'resources/css/frontend-grid.css',
            'resources/js/cp.js',
        ],
        'publicDirectory' => 'resources/dist',
        'hotFile' => __DIR__.'/../resources/dist/hot',
    ];

    public function bootAddon()
    {
        app()->bind(\Statamic\Fields\Validator::class, Validator::class);

        $this->publishes([
            __DIR__
            .'/../resources/views/forms/fields' => resource_path('views/vendor/statamic-frontend-grid-fieldtype/forms/fields'),
        ], 'statamic-frontend-grid-fieldtype-views');

        $this->publishes([
            __DIR__.'/../resources/views/snippets/frontend_grid.antlers.html' => resource_path('views/vendor/statamic-frontend-grid-fieldtype/snippets/frontend_grid.antlers.html'),
        ], 'statamic-frontend-grid-fieldtype-logic');
    }
}
