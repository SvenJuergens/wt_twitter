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

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class Tx_WtTwitter_Controller_TwitterController extends ActionController {

	/**
	 * @var Tx_WtTwitter_Domain_Repository_TweetRepository
	 */
	protected $tweetRepository = NULL;

	/**
	 * @param Tx_WtTwitter_Domain_Repository_TweetRepository $tweetRepository
	 * @return void
	 */
	public function injectTweetRepository(Tx_WtTwitter_Domain_Repository_TweetRepository $tweetRepository) {
		$this->tweetRepository = $tweetRepository;
	}

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
		$tweets = array();
		$response = '';

		if (count($this->settings) < 8) { // only flexform config (but no TypoScript)
			$this->addFlashMessage('Please add wt_twitter Static Template in the TYPO3 Backend'); // show missing template error
		}

		switch ($this->settings['mode']) {
			case 'showOwn':
				$tweets = $this->tweetRepository->getTweetsFromUserTimeline($this->settings, $response);
				break;
			case 'showFromSearch':
				$tweets = $this->tweetRepository->getTweetsFromSearch($this->settings, $response);
				break;
			case 'showListsFrom':
				$tweets = $this->tweetRepository->getListsFromUser($this->settings, $response);
				break;
			case 'showListsFor':
				$tweets = $this->tweetRepository->getListsForUser($this->settings, $response);
				break;
		}
		// Look up for any errors
		if ($response !== '') {
			$response = json_decode($response);
			if (!empty($response->errors)) {
				foreach ($response->errors as $error) {
					$this->addFlashMessage(
						$error->message . ' (error code: ' . $error->code . ')',
						'Error ' . $error->code,
                        \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
					);
				}
				unset($error);
			}
		}

		// Assign tweets
		if (!$this->settings['sortDESC']) {
			$tweets = array_reverse($tweets);
		}
		$this->view->assign('tweets', $tweets); // array to view

		if ($this->settings['debug'] == 1) {
            \TYPO3\CMS\Core\Utility\DebugUtility::debug($this->settings, 'TypoScript and Flexform settings');
            \TYPO3\CMS\Core\Utility\DebugUtility::debug($tweets, 'Result Array from Twitter');
		}
	}
}