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
 * Repository for tweets
 *
 * @author Nicole Cordes <cordes@cps-it.de>
 * @package TYPO3
 * @subpackage wt_twitter
 */
class Tx_WtTwitter_Domain_Repository_TweetRepository {

	/**
	 * The extension configuration
	 *
	 * @var array
	 */
	protected $extensionConfiguration = array();

	/**
	 * The flash messages container
	 *
	 * @var Tx_Extbase_MVC_Controller_FlashMessages
	 */
	protected $flashMessageContainer = NULL;

	/**
	 * Constructor function
	 */
	public function __construct() {
		$this->extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['wt_twitter']);
	}

	/**
	 * @param Tx_Extbase_MVC_Controller_FlashMessages $flashMessageContainer
	 * @return void
	 */
	public function injectFlashMessageContainer(Tx_Extbase_MVC_Controller_FlashMessages $flashMessageContainer) {
		$this->flashMessageContainer = $flashMessageContainer;
	}

	/**
	 * @param array $settings
	 * @param NULL $response
	 * @return array
	 */
	public function getTweetsFromUserTimeline($settings, &$response = NULL) {
		$tweets = array();

		if ($this->isTwitterSigned() && $this->isCurlActivated()) {
			$parameter = array();

			// Get screen name
			if (Tx_WtTwitter_Utility_Compatibility::testInt($settings['account'])) {
				$parameter['user_id'] = $settings['account'];
			} else {
				$parameter['screen_name'] = $settings['account'];
			}

			// Enable retweets
			if ($settings['showRetweets']) {
				$parameter['include_rts'] = 'true';
			} else {
				$parameter['include_rts'] = 'false';
			}

			// Exclude retweets
			if ($settings['excludeReplies']) {
				$parameter['exclude_replies'] = 'true';
			} else {
				$parameter['exclude_replies'] = 'false';
			}

			$tweets = $this->callApi(Tx_WtTwitter_Twitter_Api::getStatusesUserTimelineUrl(), 'GET', $parameter, $response);
		}

		return $this->addOldUserInformation($this->sliceArray($tweets, $settings['limit']));
	}

	/**
	 * @param array $settings
	 * @param NULL $response
	 * @return array
	 */
	public function getTweetsFromSearch($settings, &$response = NULL) {
		$tweets = array();

		if ($this->isTwitterSigned()&& $this->isCurlActivated()) {
			$parameter = array(
				'q' => $settings['hashtag']
			);

			$result = $this->callApi(Tx_WtTwitter_Twitter_Api::getSearchTweetsUrl(), 'GET', $parameter, $response);
			$tweets = $result->statuses;
		}

		return $this->addOldUserInformation($this->sliceArray($tweets, $settings['limit']));
	}

	public function getListsFromUser($settings, &$response = NULL) {
		$lists = array();

		if ($this->isTwitterSigned()&& $this->isCurlActivated()) {
			$parameter = array(
				'count' => ((int) $settings['limit'] > 0 ? $settings['limit'] : '1000'),
				'cursor' => '-1'
			);

			// Get screen name
			if (Tx_WtTwitter_Utility_Compatibility::testInt($settings['account'])) {
				$parameter['user_id'] = $settings['account'];
			} else {
				$parameter['screen_name'] = $settings['account'];
			}

			$result = $this->callApi(Tx_WtTwitter_Twitter_Api::getListsOwnershipsUrl(), 'GET', $parameter, $response);
			$lists = $result->lists;

			usort($lists, function($a, $b) use ($settings) {
				switch ($settings['orderby']) {
					case 'subscriberCount':
						$valueA = (int) $a->subscriber_count;
						$valueB = (int) $b->subscriber_count;
						break;
					case 'memberCount':
						$valueA = (int) $a->member_count;
						$valueB = (int) $b->member_count;
						break;
					default:
						$dateA = new DateTime($a->created_at);
						$valueA = $dateA->getTimestamp();
						$dateB = new DateTime($b->created_at);
						$valueB = $dateB->getTimestamp();
				}

				if ($valueA === $valueB) {
					return 0;
				}

				return ($valueA > $valueB) ? -1 : 1;
			});
		}

		return $lists;
	}

	/**
	 * @return boolean
	 */
	protected function isTwitterSigned() {
		if (empty($this->extensionConfiguration['oauth_token']) || empty($this->extensionConfiguration['oauth_token_secret'])) {
			$this->flashMessageContainer->add(
				'Please authorize your Twitter account in the extension settings.',
				'Twitter account not authorize',
				t3lib_FlashMessage::ERROR
			);

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @return boolean
	 */
	protected function isCurlActivated() {
		if (!$GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse'] || !function_exists('curl_init')) {
			$this->flashMessageContainer->add(
				'Please enable the use of curl in TYPO3 Install Tool by activation of TYPO3_CONF_VARS[SYS][curlUse] and check PHP integration.',
				'No curl available',
				t3lib_FlashMessage::ERROR
			);

			return FALSE;
		}

		return TRUE;
	}

	/**
	 * @param string $url
	 * @param string $method
	 * @param array $parameter
	 * @param NULL $response
	 * @return array
	 */
	protected function callApi($url, $method, $parameter, $response) {
		return Tx_WtTwitter_Twitter_Api::processRequest(
			$this->extensionConfiguration['oauth_token'],
			$this->extensionConfiguration['oauth_token_secret'],
			$url,
			$method,
			$parameter,
			$response
		);
	}

	/**
	 * @param array $array
	 * @param integer $count
	 * @return array
	 */
	protected function sliceArray(array $array, $count) {
		if (empty($count) || count($array) <= $count) {
			return $array;
		}

		return array_slice($array, 0, $count);
	}

	/**
	 * @param array $tweets
	 * @return array
	 */
	protected function addOldUserInformation(array $tweets) {
		foreach ($tweets as &$tweet) {
			$tweet->profile_image_url = $tweet->user->profile_image_url;
			$tweet->from_user = $tweet->user->screen_name;
		}
		unset($tweet);

		return $tweets;
	}

}