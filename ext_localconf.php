<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    $_EXTKEY,
    'List',
    [
        'Twitter' => 'list'
    ],
    [ // don't cache some actions
        'Twitter' => ''
    ]
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:wt_twitter/Configuration/PageTS/NewContentElementWizard.ts">'
);