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
 * Class to sign in into Twitter
 *
 * @author Nicole Cordes <cordes@cps-it.de>
 */
class Tx_WtTwitter_Twitter_SignIn
{

    /**
     * Shows the "Sign in with Twitter" button in the extension configuration
     *
     * @return string The rendered view
     */
    public function showButton()
    {
        $content = '';

        $extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['wt_twitter']);
        if (empty($extensionConfiguration['oauth_token']) || empty($extensionConfiguration['oauth_token_secret'])) {
            if ($GLOBALS['TYPO3_CONF_VARS']['SYS']['curlUse'] && function_exists('curl_init')) {
                $url = Tx_WtTwitter_Twitter_Api::getOAuthRequestTokenUrl();

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);

                $oAuthParameter = Tx_WtTwitter_Twitter_Api::createSignature(
                    Tx_WtTwitter_Twitter_Api::getOAuthParameter(
                        '',
                        [
                            'oauth_callback' => \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_URL') .
                                \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('wt_twitter') . 'Classes/Twitter/Redirect.php'
                        ]
                    ),
                    $url,
                    'POST',
                    Tx_WtTwitter_Twitter_Api::consumerSecret,
                    ''
                );
                $header = [
                    'Authorization: OAuth ' . Tx_WtTwitter_Twitter_Api::implodeArrayForHeader($oAuthParameter),
                    'Content-Length:',
                    'Content-Type:',
                    'Expect:'
                ];
                curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

                $response = curl_exec($ch);
                if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
                    $responseArray = \TYPO3\CMS\Core\Utility\GeneralUtility::explodeUrl2Array($response);

                    $content .= '<a href="#" onclick="twitterWindow = window.open(\'' .
                        Tx_WtTwitter_Twitter_Api::getOAuthAuthorizeUrl() . '?oauth_token=' . $responseArray['oauth_token'] .
                        '\',\'Sign in with Twitter\',\'height=650,width=650,status=0,menubar=0,resizable=1,location=0,directories=0,scrollbars=1,toolbar=0\');">';
                    $content .= '<img alt="Sign in with Twitter" height="28" src="' . \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('wt_twitter') . 'Resources/Public/Images/sign-in-with-twitter.png" width="158" />';
                    $content .= '</a>';
                } else {
                    $this->notify(
                        'Twitter couldn\'t generate the request token.<br /><br />' .
                        'Headers sent:<br />' . nl2br(curl_getinfo($ch, CURLINFO_HEADER_OUT)),
                        'An error occurred',
                        \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
                    );
                }
                curl_close($ch);
            } else {
                $this->notify(
                    'Please enable the use of curl in TYPO3 Install Tool by activation of TYPO3_CONF_VARS[SYS][curlUse] and check PHP integration.',
                    'No curl available',
                    \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR
                );
            }
        } else {
            $this->notify(
                'You already registered this application with your Twitter account.',
                'Already signed in',
                \TYPO3\CMS\Core\Messaging\FlashMessage::OK
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
     */
    public function notify($message, $messageHeader, $severity = \TYPO3\CMS\Core\Messaging\FlashMessage::OK)
    {
        if (TYPO3_MODE !== 'BE') {
            return;
        }
        $flashMessage = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Messaging\FlashMessage::class,
            $message,
            $messageHeader,
            $severity,
            true
        );
        /** @var $flashMessageService \TYPO3\CMS\Core\Messaging\FlashMessageService */
        $flashMessageService = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Messaging\FlashMessageService::class);
        /** @var $defaultFlashMessageQueue \TYPO3\CMS\Core\Messaging\FlashMessageQueue */
        $defaultFlashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
        $defaultFlashMessageQueue->enqueue($flashMessage);
    }
}
