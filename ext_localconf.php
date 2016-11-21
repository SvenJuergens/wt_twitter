<?php
if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	$_EXTKEY,
	'List',
	array(
		'Twitter' => 'list'
	),
	array( // don't cache some actions
		'Twitter' => ''
	)
);