<?php
$extensionClassPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('wt_twitter') . 'Classes/';

return [
    'tx_wttwitter_twitter_api' => $extensionClassPath . 'Twitter/Api.php'
];
