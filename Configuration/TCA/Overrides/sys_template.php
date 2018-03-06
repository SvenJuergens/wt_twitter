<?php
defined('TYPO3_MODE') || die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'wt_twitter',
    'Configuration/TypoScript/Main/',
    'Main TypoScript'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'wt_twitter',
    'Configuration/TypoScript/NewsTicker/',
    'Newsticker'
);