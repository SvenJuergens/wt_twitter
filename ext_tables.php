<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    $_EXTKEY,
    'List',
    'wt_twitter - Frontend Tweets'
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['wttwitter_list'] = 'layout,select_key,recursive,pages';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['wttwitter_list'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('wttwitter_list', 'FILE:EXT:wt_twitter/Configuration/Flexforms/flexform.xml');


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript/Main/', 'Main TypoScript');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript/NewsTicker/', 'Newsticker');
