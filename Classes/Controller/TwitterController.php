<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Nicole Cordes <cordes@cps-it.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * The twitter controller for the wt_twitter package
 *
 * @author Nicole Cordes <cordes@cps-it.de>
 * @package TYPO3
 * @subpackage wt_twitter
 */
class Tx_WtTwitter_Controller_TwitterController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	public function initializeAction() {
		// Merge flexForm and TypoScript values
		$settings = (array)$this->settings['setup'];
		if (is_array($this->settings['flexform'])) {
			foreach ($this->settings['flexform'] as $key => $value) {
				if (!empty($value)) {
					$settings[$key] = $value;
				}
			}
			unset($key, $value);
		}
		$this->settings = $settings;

		// Set cache if cache_timeout was not set in the current page
		if ($GLOBALS['TSFE']->page['cache_timeout'] == 0) {
			$GLOBALS['TSFE']->set_cache_timeout_default($this->settings['cache_timeout']); // cache of current page should be renewed after 50 Min
		}
	}

	/**
	 * List action for this controller. Displays a list of twitter entries.
	 *
	 * @return string The rendered view
	 */
	public function listAction() {
		$extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['wt_twitter']);
		$tweets = array();

		if (empty($extensionConfiguration['oauth_token']) || empty($extensionConfiguration['oauth_token_secret'])) {
			$this->flashMessageContainer->add(
				'Please authorize your Twitter account in the extension settings.',
				'Twitter account not authorize',
				t3lib_FlashMessage::ERROR
			);
		} else {
			$count = (int)$this->settings['limit'];
			switch ($this->settings['mode']) {
				case 'showOwn':
					$tweets = Tx_WtTwitter_Twitter_Api::getTweetsFromUserTimeline(
						$extensionConfiguration['oauth_token'],
						$extensionConfiguration['oauth_token_secret'],
						$this->settings['account'],
						$this->settings['showRetweets'],
						$count
					);
					break;
				case 'showFromSearch':
					$tweets = Tx_WtTwitter_Twitter_Api::getTweetsFromSearch(
						$extensionConfiguration['oauth_token'],
						$extensionConfiguration['oauth_token_secret'],
						$this->settings['hashtag'],
						$count
					);
					break;
			}
		}
		if (!$this->settings['sortDESC']) {
			$tweets = array_reverse($tweets);
		}
		$this->view->assign('tweets', $tweets); // array to view

		if (count($this->settings) < 5) { // only flexform config (but no TypoScript)
			$this->flashMessageContainer->add('Please add wt_twitter Static Template in the TYPO3 Backend'); // show missing template error
		}

		if ($this->settings['debug'] == 1) {
			t3lib_utility_Debug::debug($this->settings, 'TypoScript and Flexform settings');
			t3lib_utility_Debug::debug($tweets, 'Result Array from Twitter');
		}
	}
}

if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_twitter/Classes/Controller/TwitterController.php']) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wt_twitter/Classes/Controller/TwitterController.php']);
}

?>