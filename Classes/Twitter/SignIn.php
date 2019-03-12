<?php
namespace WtTwitterPackage\WtTwitter\Twitter;

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
 * Class to sign in into Twitter
 *
 * @author Nicole Cordes <cordes@cps-it.de>
 */
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SignIn
{

    /**
     * Shows the "Sign in with Twitter" button in the extension configuration
     *
     * @return string The rendered view
     * @throws \TYPO3\CMS\Core\Exception
     */
    public function showButton()
    {
        $content = '';

        $extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['wt_twitter']);
        if (empty($extensionConfiguration['oauth_token']) || empty($extensionConfiguration['oauth_token_secret'])) {
            if (function_exists('curl_init')) {
                $url = TwitterApi::getOAuthRequestTokenUrl();

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
                curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);

                $oAuthParameter = TwitterApi::createSignature(
                    TwitterApi::getOAuthParameter(
                        '',
                        [
                            'oauth_callback' => GeneralUtility::getIndpEnv('TYPO3_SITE_URL') .
                                ExtensionManagementUtility::siteRelPath('wt_twitter') . 'Classes/Twitter/Redirect.php'
                        ]
                    ),
                    $url,
                    'POST',
                    TwitterApi::consumerSecret,
                    ''
                );
                $header = [
                    'Authorization: OAuth ' . TwitterApi::implodeArrayForHeader($oAuthParameter),
                    'Content-Length:',
                    'Content-Type:',
                    'Expect:'
                ];
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

                $response = curl_exec($ch);
                if (curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200) {
                    $responseArray = GeneralUtility::explodeUrl2Array($response);

                    $content .= '<a href="#" onclick="twitterWindow = window.open(\'' .
                        TwitterApi::getOAuthAuthorizeUrl() . '?oauth_token=' . $responseArray['oauth_token'] .
                        '\',\'Sign in with Twitter\',\'height=650,width=650,status=0,menubar=0,resizable=1,location=0,directories=0,scrollbars=1,toolbar=0\');">';
                    $content .= '<img alt="Sign in with Twitter" height="28" src="' . ExtensionManagementUtility::extRelPath('wt_twitter') . 'Resources/Public/Images/sign-in-with-twitter.png" width="158" />';
                    $content .= '</a>';
                } else {
                    $this->notify(
                        'Twitter couldn\'t generate the request token.<br /><br />' .
                        'Headers sent:<br />' . nl2br(curl_getinfo($ch, CURLINFO_HEADER_OUT)),
                        'An error occurred',
                        FlashMessage::ERROR
                    );
                }
                curl_close($ch);
            } else {
                $this->notify(
                    'Please enable the use of curl and check PHP integration.',
                    'No curl available',
                    FlashMessage::ERROR
                );
            }
        } else {
            $this->notify(
                'You already registered this application with your Twitter account.',
                'Already signed in',
                FlashMessage::OK
            );
        }

        return $content;
    }

    /**
     * Notifies the user using a Flash message.
     * original from EXT:image_autoresize
     *
     * @param string $message The message
     * @param string $messageHeader The message header
     * @param int $severity Optional severity, must be either of \TYPO3\CMS\Core\Messaging\FlashMessage::INFO,
     *                          \TYPO3\CMS\Core\Messaging\FlashMessage::OK,
     *                          \TYPO3\CMS\Core\Messaging\FlashMessage::WARNING
     *                          or \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR.
     *                          Default is \TYPO3\CMS\Core\Messaging\FlashMessage::OK.
     * @return void
     * @throws \TYPO3\CMS\Core\Exception
     */
    public function notify($message, $messageHeader, $severity = FlashMessage::OK): void
    {
        if (TYPO3_MODE !== 'BE') {
            return;
        }
        /** @var FlashMessage $flashMessage */
        $flashMessage = GeneralUtility::makeInstance(
            FlashMessage::class,
            $message,
            $messageHeader,
            $severity,
            true
        );
        /** @var FlashMessageService $flashMessageService */
        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        /** @var FlashMessageQueue $defaultFlashMessageQueue  */
        $defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $defaultFlashMessageQueue->enqueue($flashMessage);
    }
}
