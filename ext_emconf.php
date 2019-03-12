<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "wt_twitter".
 *
 * Auto generated 06-06-2014 10:17
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Frontend Twitter Feed',
    'description' => 'Show your twitter entries in FE. In addtion: Use for twitter newsticker. Typoscript and HTML templates for all kind of configuration possibilities. Links will be parsed, geotags supported. Extbase and Fluid extension.',
    'category' => 'plugin',
    'shy' => 0,
    'version' => '2.1.0',
    'dependencies' => 'extbase,fluid',
    'conflicts' => '',
    'priority' => '',
    'loadOrder' => '',
    'module' => '',
    'state' => 'alpha',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearcacheonload' => 1,
    'lockType' => '',
    'author' => 'Nicole Cordes',
    'author_email' => 'cordes@cps-it.de',
    'author_company' => 'CPS-IT GmbH',
    'CGLcompliance' => '',
    'CGLcompliance_note' => '',
    'constraints' => [
        'depends' => [
            'typo3' => '8.2.0-9.5.99'
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'WtTwitterPackage\\WtTwitter\\' => 'Classes'
        ],
    ],
];
