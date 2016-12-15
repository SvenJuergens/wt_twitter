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
 * @author Nicole Cordes <cordes@cps-it.de>
 */
final class Tx_WtTwitter_Twitter_Api
{
    const consumerKey = '2G3ws4mSXwMLmiKX3erUA';

    const consumerSecret = 'XgMWWvWa3HPNkGKiZUrYMOqfqGyw9sCdyG9Rc2Rzw';

    /**
     * @return string
     */
    public static function getOAuthAccessTokenUrl()
    {
        return 'https://api.twitter.com/oauth/access_token';
    }

    /**
     * @return string
     */
    public static function getOAuthAuthorizeUrl()
    {
        return 'https://api.twitter.com/oauth/authorize';
    }

    /**
     * @return string
     */
    public static function getOAuthRequestTokenUrl()
    {
        return 'https://api.twitter.com/oauth/request_token';
    }

    /**
     * @return string
     */
    public static function getListsMembershipsUrl()
    {
        return 'https://api.twitter.com/1.1/lists/memberships.json';
    }

    /**
     * @return string
     */
    public static function getListsOwnershipsUrl()
    {
        return 'https://api.twitter.com/1.1/lists/ownerships.json';
    }

    /**
     * @return string
     */
    public static function getSearchTweetsUrl()
    {
        return 'https://api.twitter.com/1.1/search/tweets.json';
    }

    /**
     * @return string
     */
    public static function getStatusesUserTimelineUrl()
    {
        return 'https://api.twitter.com/1.1/statuses/user_timeline.json';
    }

    /**
     * @param string $oAuthToken
     * @param array $additionalParameter
     *
     * @return array
     */
    public static function getOAuthParameter($oAuthToken, array $additionalParameter = [])
    {
        $oAuthParameter = [
            'oauth_consumer_key' => static::consumerKey,
            'oauth_nonce' => md5(microtime() . mt_rand()),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => time(),
            'oauth_token' => $oAuthToken,
            'oauth_version' => '1.0'
        ];

        return array_merge($oAuthParameter, $additionalParameter);
    }

    /**
     * @param array $oAuthParameter
     * @param string $url
     * @param string $method
     * @param string $consumerSecret
     * @param string $oAuthTokenSecret
     * @param array $additionalParameter
     *
     * @return array
     */
    public static function createSignature(array $oAuthParameter, $url, $method, $consumerSecret, $oAuthTokenSecret, array $additionalParameter = [])
    {
        $encodedParameter = [];
        $parameter = array_merge($oAuthParameter, $additionalParameter);
        foreach ($parameter as $key => $value) {
            $encodedParameter[rawurlencode($key)] = rawurlencode($value);
        }
        unset($key, $value);
        ksort($encodedParameter);

        $requestParts = [];
        foreach ($encodedParameter as $key => $value) {
            $requestParts[] = $key . '=' . $value;
        }
        unset($key, $value);

        $parameterString = implode('&', $requestParts);

        $signatureBaseString = strtoupper($method) . '&' . rawurlencode($url) . '&' . rawurlencode($parameterString);
        $signingKey = rawurlencode($consumerSecret) . '&' . rawurlencode($oAuthTokenSecret);

        $oAuthParameter['oauth_signature'] = base64_encode(hash_hmac('sha1', $signatureBaseString, $signingKey, true));

        return $oAuthParameter;
    }

    /**
     * @param array $parameterArray
     *
     * @return string
     */
    public static function implodeArrayForHeader(array $parameterArray)
    {
        $parts = [];
        foreach ($parameterArray as $key => $value) {
            $parts[] = $key . '="' . rawurlencode($value) . '"';
        }

        return implode(', ', $parts);
    }

    /**
     * @param array $parameterArray
     *
     * @return string
     */
    public static function implodeArrayForUrl(array $parameterArray)
    {
        $parts = [];
        foreach ($parameterArray as $key => $value) {
            $parts[] = $key . '=' . rawurlencode($value);
        }

        return implode('&', $parts);
    }

    /**
     * @param string $oAuthToken
     * @param string $oAuthTokenSecret
     * @param string $url
     * @param string $method
     * @param array $parameter
     * @param NULL $response
     * @return array
     */
    public static function processRequest($oAuthToken, $oAuthTokenSecret, $url, $method, $parameter, &$response)
    {
        $tweets = [];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_URL, $url . '?' . static::implodeArrayForUrl($parameter));

        $oAuthParameter = static::createSignature(
            static::getOAuthParameter(
                $oAuthToken
            ),
            $url,
            $method,
            static::consumerSecret,
            $oAuthTokenSecret,
            $parameter
        );

        $header = [
            'Authorization: OAuth ' . static::implodeArrayForHeader($oAuthParameter),
            'Content-Length:',
            'Content-Type:',
            'Expect:'
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        $result = curl_exec($ch);
        if (isset($response)) {
            $response = $result;
        }
        if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200) {
            $tweets = json_decode($result);
        }
        curl_close($ch);

        return $tweets;
    }
}
