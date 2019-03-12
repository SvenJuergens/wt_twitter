<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'WtTwitterPackage.WtTwitter',
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
