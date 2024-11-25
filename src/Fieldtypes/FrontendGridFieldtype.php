<?php

namespace MityDigital\StatamicFrontendGridFieldtype\Fieldtypes;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Statamic\Fields\Field;
use Statamic\Fields\Fields;
use Statamic\Fields\Fieldtype;
use Statamic\Fields\Validator;
use Statamic\Tags\Concerns\RendersForms;

class FrontendGridFieldtype extends Fieldtype
{
    use RendersForms;

    protected $categories = ['special'];

    protected $icon = 'grid';

    protected $selectableInForms = true;

    public static function title()
    {
        return __('statamic-frontend-grid-fieldtype::fieldtype.title');
    }

    public function view()
    {
        return 'statamic-frontend-grid-fieldtype::forms.fields.frontend_grid';
    }

    protected function configFieldItems(): array
    {
        return [
            [
                'display' => __('Fields'),
                'fields' => [
                    'fields' => [
                        'display' => __('Fields'),
                        'instructions' => __('statamic::fieldtypes.grid.config.fields'),
                        'type' => 'fields',
                        'full_width_setting' => true,
                    ],
                ],
            ],
            [
                'display' => __('Appearance & Behavior'),
                'fields' => [
                    'row_heading' => [
                        'display' => __('statamic-frontend-grid-fieldtype::fieldtype.config.row_heading.display'),
                        'instructions' => __('statamic-frontend-grid-fieldtype::fieldtype.config.row_heading.instructions'),
                        'type' => 'text',
                        'validate' => [
                            'required',
                        ],
                    ],
                    'scope' => [
                        'display' => __('statamic-frontend-grid-fieldtype::fieldtype.config.scope.display'),
                        'instructions' => __('statamic-frontend-grid-fieldtype::fieldtype.config.scope.instructions'),
                        'type' => 'text',
                    ],
                    'max_rows' => [
                        'display' => __('Maximum Rows'),
                        'instructions' => __('statamic::fieldtypes.grid.config.max_rows'),
                        'type' => 'integer',
                    ],
                    'min_rows' => [
                        'display' => __('Minimum Rows'),
                        'instructions' => __('statamic::fieldtypes.grid.config.min_rows'),
                        'type' => 'integer',
                    ],
                    'add_row' => [
                        'display' => __('Add Row Label'),
                        'instructions' => __('statamic::fieldtypes.grid.config.add_row'),
                        'type' => 'text',
                    ],
                    'delete_row' => [
                        'display' => __('statamic-frontend-grid-fieldtype::fieldtype.config.delete_row.display'),
                        'instructions' => __('statamic-frontend-grid-fieldtype::fieldtype.config.delete_row.instructions'),
                        'type' => 'text',
                    ],
                ],
            ],
        ];
    }

    public function extraRenderableFieldData(): array
    {
        $fieldHandle = $this->field()->handle();
        $fields = collect(Arr::get($this->field()->config(), 'fields', []));

        // set defaults
        $setDefault = $fields
            ->mapWithKeys(function (array $config) {
                $default = Arr::get($config['field'], 'default', null);
                if ($config['field']['type'] === 'checkboxes') {
                    $default = [];
                }

                return [$config['handle'] => $default];
            })
            ->toArray();

        $scope = Arr::get($this->field()->config(), 'scope', '');
        if ($scope) {
            $scope = $scope.'.';
        }

        // set fields
        $setFields = $fields
            ->map(function (array $config) use ($fieldHandle, $scope) {
                $field = $this->getRenderableField(new Field($config['handle'], $config['field']));

                $handle = $config['handle'];
                $markup = Arr::get($field, 'field', '');

                // remove, replace
                $remove = [];
                $replace = [];

                $remove = [
                    sprintf('@change="form.validate(\'%s\')"', $handle),
                ];

                $replace = [
                    sprintf('form.errors.%s', $handle) => sprintf('form.errors[\'%s.\'+index+\'.%s\']', $fieldHandle, $handle),
                ];

                $chk = Arr::get($field, 'type') === 'radio';

                if (Arr::get($field, 'type') === 'checkboxes') {
                    // name="X" becomes x-bind:name="FIELD_IDX_HANDLE[]"
                    $replace[sprintf('name="%s[]"', $handle)] =
                        sprintf('x-bind:name="\'%s[\'+index+\'][%s][]\'"', $fieldHandle, $handle);

                    $replace[sprintf('x-model="%s%s"', $scope, $handle)] = sprintf('x-model="set.%s"', $handle);

                    foreach (Arr::get($config['field'], 'options', []) as $option) {
                        // checkbox_{{ handle}}_{{ value }}
                        $replace[sprintf('id="checkbox_%s_%s"', $handle, $option['key'])] =
                            sprintf('x-bind:id="\'checkbox_%s_\'+index+\'_%s_%s\'"', $fieldHandle, $handle, $option['key']);
                        $replace[sprintf('for="checkbox_%s_%s"', $handle, $option['key'])] =
                            sprintf('x-bind:for="\'checkbox_%s_\'+index+\'_%s_%s\'"', $fieldHandle, $handle, $option['key']);
                    }
                } elseif (Arr::get($field, 'type') === 'radio') {

                    $replace[sprintf('x-model="%s%s"', $scope, $handle)] = sprintf('x-model="set.%s"', $handle);

                    foreach (Arr::get($config['field'], 'options', []) as $option) {
                        $replace[sprintf('id="radio_%s_%s"', $handle, $option['key'])] =
                            sprintf('x-bind:id="\'radio_%s_\'+index+\'_%s_%s\'"', $fieldHandle, $handle, $option['key']);
                        $replace[sprintf('for="radio_%s_%s"', $handle, $option['key'])] =
                            sprintf('x-bind:for="\'radio_%s_\'+index+\'_%s_%s\'"', $fieldHandle, $handle, $option['key']);
                    }
                } else {
                    // name="X" becomes x-bind:name="FIELD_IDX_HANDLE"
                    $replace[sprintf('name="%s"', $handle)] = sprintf('x-bind:name="\'%s[\'+index+\'][%s]\'"', $fieldHandle, $handle);

                    // x-model="form.handle" becomes x-model="set.handle"
                    $replace[sprintf('x-model="%s%s"', $scope, $handle)] = sprintf('x-model="set.%s"', $handle);

                    $replace[sprintf('id="%s"', $handle)] = sprintf('x-bind:id="\'%s_\'+index+\'_%s\'"', $fieldHandle, $handle);
                }

                if ($chk) {
                    ray($replace);
                    ray($remove);
                    ray($markup);
                }

                foreach ($replace as $search => $value) {
                    $markup = str_replace($search, $value, $markup);
                }

                foreach ($remove as $search) {
                    $markup = str_replace($search, '', $markup);
                }

                if ($chk) {
                    //dd($markup);
                }

                if (true) {
                    $identifier = sprintf('name="%s"', $handle);
                    $newIdentifier = sprintf('x-bind:name="\'%s[\'+index+\'][%s]\'"', $fieldHandle, $handle);
                    $xModel = sprintf('x-model="set.%s"', $handle);

                    if (Arr::get($field, 'type') === 'checkboxes') {
                        // different identifier
                        $identifier = sprintf('name="%s[]"', $handle);
                        $newIdentifier = sprintf('x-bind:name="\'%s[\'+index+\'][%s][]\'"', $fieldHandle, $handle);
                    }

                    // add x-model
                    $markup = Str::replace($identifier, $xModel.' '.$identifier, $markup);

                    // remove name="handle[]"
                    $markup = Str::replace($identifier, $newIdentifier, $markup);
                }

                $field['field'] = $markup;

                return $field;
            })
            ->toArray();

        // make field labels
        $fieldLabels = collect($setFields)
            ->mapWithKeys(fn (array $field) => [$field['handle'] => $field['display']]);

        // errors
        $formHandle = $this->field()->form()->handle() ?? 'default';
        $errors = session('errors') ? session('errors')->getBag(sprintf('form.%s', $formHandle)) : new MessageBag;
        ray($errors)->red();
        $setErrors = collect($errors->messages())
            ->filter(fn (array $errors, string $field) => Str::startsWith($field, $fieldHandle.'.'))
            ->map(function (array $errors, string $field) use ($fieldHandle) {
                $search = Str::match('/'.$fieldHandle.'.\d+.\S+/', $field);
                $thisField = explode('.', $search);
                $thisFieldHandle = $thisField[2];

                return [
                    'index' => $thisField[1],
                    'handle' => $thisFieldHandle,
                    'messages' => $errors,
                ];
            })
            ->values()
            ->groupBy('index')
            ->map(function (Collection $errors) {
                return $errors->mapWithKeys(fn (array $properties) => [$properties['handle'] => implode(', ', $properties['messages'])]);
            })
            ->toArray();

        return [
            'set_default' => $setDefault,
            'set_errors' => $setErrors,
            'set_fields' => $setFields,

            'add_row' => $this->config('add_row', __('Add')),
            'delete_row' => $this->config('delete_row', __('Delete')),
        ];
    }

    public function extraRules(): array
    {
        $rules = array_map([Validator::class, 'explodeRules'], $this->extraRules);

        $handle = $this->field()->handle();

        collect(Arr::get($this->field()->config(), 'fields', []))->each(function (array $config) use ($handle, &$rules) {
            $setHandle = Arr::get($config, 'handle');
            $validation = Arr::get($config, 'field.validate');
            if ($validation) {
                $rules[$handle.'.*.'.$setHandle] = $validation;
            }
        });

        $fieldRules = [];
        if (in_array('required', Arr::get($this->field()->config(), 'validate', []))) {
            $fieldRules[] = 'required';
        } else {
            $fieldRules[] = 'nullable';
        }

        $fieldRules[] = 'array';
        if ($minRows = Arr::get($this->field()->config(), 'min_rows', null)) {
            $fieldRules[] = 'min:'.$minRows;
        }

        if ($maxRows = Arr::get($this->field()->config(), 'max_rows', null)) {
            $fieldRules[] = 'max:'.$maxRows;
        }

        if (count($fieldRules)) {
            $rules[$handle] = $fieldRules;
        }

        return $rules;
    }

    public function extraValidationAttributes(): array
    {
        $handle = $this->field()->handle();
        $fields = collect(Arr::get($this->field()->config(), 'fields', []));

        return $fields->mapWithKeys(function (array $config) use ($handle, &$attributes) {
            $fieldHandle = Arr::get($config, 'handle');
            $display = Arr::get($config['field'], 'display');

            return [
                $handle.'.*.'.$fieldHandle => $display,
            ];
        })->toArray();
    }

    public function preProcess($data)
    {
        if ($data) {
            $template = collect(Arr::get($this->field()->config(), 'fields', []))->pluck('handle');

            $processed = [
                '_sets' => count($data),
            ];
            foreach ($data as $setIndex => $set) {
                foreach ($template as $field) {
                    $key = $setIndex.'_'.$field;
                    $processed[$key] = '';

                    if ($value = Arr::get($set, $field, '')) {
                        $processed[$key] = $value;
                    }
                }
            }

            return $processed;
        }

        return $data;
    }
}
