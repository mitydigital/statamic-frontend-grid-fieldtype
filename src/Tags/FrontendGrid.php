<?php

namespace MityDigital\StatamicFrontendGridFieldtype\Tags;

use Illuminate\Support\Arr;
use Statamic\Fields\Field;
use Statamic\Tags\Tags;

class FrontendGrid extends Tags
{
    public function html(): string
    {
        $value = $this->context->get('value');
        $config = $value->field();

        // convert config of fields (and widths) to template of rows
        $template = [];
        $templateIndex = 0;
        $currentWidth = 0;
        foreach (Arr::get($config->config(), 'fields', []) as $field) {

            $width = (float) Arr::get($field['field'], 'width', 100);

            $currentWidth += $width;

            if ($currentWidth > 100) {
                $templateIndex++;
                $currentWidth = $width;
            }

            if (! isset($template[$templateIndex])) {
                $template[$templateIndex] = [];
            }

            $field['field']['width'] = $width;
            $template[$templateIndex][] = $field;
        }

        return view('statamic-frontend-grid-fieldtype::forms.fields.frontend_grid_submission', [
            'heading' => Arr::get($config->config(), 'row_heading', 'Set'),
            'template' => $template,
            'data' => $value->raw(),
        ])->render();
    }

    public function text(): string
    {
        $value = $this->context->get('value');
        $config = $value->field();

        $heading = Arr::get($config->config(), 'row_heading', 'Set');

        $output = [];

        foreach ($value->raw() as $rowIndex => $row) {

            $output[] = '** '.$heading.' '.($rowIndex + 1);

            foreach (Arr::get($config->config(), 'fields', []) as $field) {
                $display = $field['field']['display'];
                $type = $field['field']['type'];

                if ($type !== 'spacer') {
                    $output[] = $display;
                    $value = Arr::get($row, $field['handle'], '');

                    if (is_array($value)) {
                        $output[] = implode(', ', $value);
                    } else {
                        $output[] = $value;
                    }

                    $output[] = '';
                }
            }

            $output[] = '';
            $output[] = '';
        }

        return implode("\r\n", $output);
    }

    public function has(): bool
    {
        $handle = $this->params->get('handle', 'form');

        // get the form
        $formHandle = $this->context->get($handle);

        $form = \Statamic\Facades\Form::find($formHandle);

        if ($form) {
            if ($form->fields()->filter(fn (Field $field) => $field->type() === 'frontend_grid')->count()) {
                return true;
            }
        }

        return false;
    }
}
