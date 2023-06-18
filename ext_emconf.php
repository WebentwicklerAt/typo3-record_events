<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'Record events',
    'description' => 'Triggers events for database records.',
    'category' => 'misc',
    'clearCacheOnLoad' => true,
    'version' => '0.0.0',
    'state' => 'alpha',
    'author' => 'Gernot Leitgab',
    'author_company' => 'Webentwickler.at',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'WebentwicklerAt\\RecordEvents\\' => 'Classes'
        ]
    ],
];
