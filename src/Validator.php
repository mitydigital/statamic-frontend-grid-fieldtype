<?php

namespace MityDigital\StatamicFrontendGridFieldtype;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Validator extends \Statamic\Fields\Validator
{
    public function filterPrecognitiveRules($rules)
    {
        $request = request();

        if (! $request->headers->has('Precognition-Validate-Only')) {
            return $rules;
        }

        $validateFields = explode(',', $request->header('Precognition-Validate-Only'));

        $rules = Collection::make($rules);
        $subRules = $rules->filter(function (array $rules, string $field) use ($validateFields) {
            foreach ($validateFields as $validatable) {
                if (Str::startsWith($field, $validatable.'.')) {
                    return true;
                }
            }

            return false;
        });

        return $rules
            ->only($validateFields)
            ->merge($subRules)
            ->all();
    }
}
