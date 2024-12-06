<?php

return [

    'title' => 'Frontend Grid',

    'config' => [

        'row_heading' => [
            'display' => 'Row Heading',
            'instructions' => 'Used in the CP and submissions to group each set. If your field is for a group of "Vehicles" then "Vehicle" is a suitable Row Heading.',
        ],

        'scope' => [
            'display' => 'Scope',
            'instructions' => 'If using a Scoped JS Driver, include your scope reference here. Leave blank otherwise.',
        ],

        'add_row' => [
            'display' => 'Add Row Label',
            'instructions' => 'Optional. Will default to "Add". When a "Next" row label is provided, this will only be shown on empty grids.',
        ],

        'add_next_row' => [
            'display' => 'Add "Next" Row Label',
            'instructions' => 'Optional. Shown when there is at least 1 set in your grid. Will fall back to the "Add Row Label".',
        ],

        'delete_row' => [
            'display' => 'Delete Row Label',
            'instructions' => 'Customize the label of the "Delete Row" button.',
        ],

    ],

];
