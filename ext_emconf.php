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

$EM_CONF[$_EXTKEY] = array(
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
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.5.0-6.2.99',
			'extbase' => '1.3.0-0.0.0',
			'fluid' => '1.3.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:34:{s:9:"ChangeLog";s:4:"12f9";s:16:"ext_autoload.php";s:4:"9075";s:21:"ext_conf_template.txt";s:4:"64c3";s:12:"ext_icon.gif";s:4:"d43b";s:17:"ext_localconf.php";s:4:"1d90";s:14:"ext_tables.php";s:4:"5cb0";s:40:"Classes/Controller/TwitterController.php";s:4:"abef";s:45:"Classes/Domain/Repository/TweetRepository.php";s:4:"c0e3";s:23:"Classes/Twitter/Api.php";s:4:"aa51";s:28:"Classes/Twitter/Redirect.php";s:4:"4d98";s:26:"Classes/Twitter/SignIn.php";s:4:"3377";s:52:"Classes/UserFunction/user_wttwitter_userfunction.php";s:4:"ab28";s:33:"Classes/Utility/Compatibility.php";s:4:"76be";s:36:"Configuration/Flexforms/flexform.xml";s:4:"a6cd";s:43:"Configuration/TypoScript/Main/constants.txt";s:4:"bf4f";s:39:"Configuration/TypoScript/Main/setup.txt";s:4:"b83b";s:45:"Configuration/TypoScript/NewsTicker/setup.txt";s:4:"152a";s:44:"Resources/Private/BackEnd/TwitterWizicon.php";s:4:"458c";s:40:"Resources/Private/Language/locallang.xml";s:4:"f37c";s:43:"Resources/Private/Language/locallang_db.xml";s:4:"8e9a";s:47:"Resources/Private/Language/locallang_module.xml";s:4:"a4a0";s:42:"Resources/Private/Partials/Lists/Show.html";s:4:"6559";s:43:"Resources/Private/Partials/Tweets/Show.html";s:4:"6e60";s:45:"Resources/Private/Templates/Twitter/List.html";s:4:"4e2e";s:63:"Resources/Private/Templates/TwitterNewsTicker/Twitter/List.html";s:4:"bf30";s:33:"Resources/Public/Icons/ce_wiz.gif";s:4:"e010";s:37:"Resources/Public/Icons/icon_close.gif";s:4:"227a";s:38:"Resources/Public/Icons/icon_geotag.gif";s:4:"5558";s:39:"Resources/Public/Icons/icon_retweet.gif";s:4:"f6b4";s:39:"Resources/Public/Icons/icon_twitter.gif";s:4:"112e";s:48:"Resources/Public/Images/sign-in-with-twitter.png";s:4:"7306";s:52:"Resources/Public/Media/CSS/wt_twitter_newsticker.css";s:4:"5f22";s:50:"Resources/Public/Media/JS/wt_twitter_newsticker.js";s:4:"50de";s:14:"doc/manual.sxw";s:4:"defa";}',
	'suggests' => array(
	),
);

?>