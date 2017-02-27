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
class user_wttwitteruserfunction
{

    /**
     * @var \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer
     */
    public $cObj = null;

    /**
     * @var array
     */
    public $conf = [];

    /**
     * Remove a given string from another string
     *
     * @var string $content: Empty content variable
     * @var array $conf: TypoScript configuration for this userFunc
     *
     * @return string
     */
    public function user_wttwitter_remove($content = '', $conf = [])
    {
        // config
        $this->conf = $conf['userFunc.']; // ts config

        // let's go
        $string = $this->cObj->cObjGetSingle($this->conf['string'], $this->conf['string.']); // get string from ts
        $remove = $this->cObj->cObjGetSingle($this->conf['remove'], $this->conf['remove.']); // get remove part from ts

        return trim(str_replace($remove, '', $string)); // return string without another string
    }

    /**
     * Use typolink to convert all urls, names and hashtags in a string to html links
     *
     * @var string $content: Empty content variable
     * @var array $conf: TypoScript configuration for this userFunc
     *
     * @return string
     */
    public function user_wttwitter_link($content = '', $conf = [])
    {
        // config
        $conf = $conf['userFunc.']; // ts config
        $string = $this->cObj->getCurrentVal(); // get date from typoscript

        // 1. rewrite URL with typolink
        preg_match_all('/(^|\s)(https?:\/\/|www\.)\S*/i', $string, $arr_result); // get all links of the string
        foreach ((array)$arr_result[0] as $url) { // one loop for every link in the string
            if (!empty($url)) { // if there is a URL
                $typoLinkConf = ['parameter' => $url]; // typolink configuration
                $typoLinkConf = array_merge((array)$conf['typolink.'], $typoLinkConf); // get params from typoscript
                $string = str_replace(
                    $url,
                    $this->cObj->typoLink($url, $typoLinkConf),
                    $string
                ); // replace each url with typolink
            }
        }

        // 2. rewrite @name with typolink to www.twitter.com/name
        preg_match_all('/(^|\s)@(\w+)/', $string, $arr_result2); // get all twitternames of the string
        foreach ((array)$arr_result2[0] as $value) { // one loop for every twittername in string
            $value = trim($value); // trim it
            if (!empty($value)) { // if there is a value
                $typoLinkConf = ['parameter' => 'https://www.twitter.com/' . str_replace('@', '', $value)]; // typolink configuration
                $typoLinkConf = array_merge((array)$conf['typolink.'], $typoLinkConf); // get params from typoscript
                $string = str_replace(
                    $value,
                    $this->cObj->typoLink($value, $typoLinkConf),
                    $string
                ); // replace each url with typolink
            }
        }

        // 3. rewrite #hashtag with typolink to search.twitter.com/search?q=#hashtag
        //preg_match_all('/(^|\s)#(\w+)/', $string, $arr_result3); // get all twitternames of the string
        preg_match_all('/(^|\s)#([\w]+)/', $string, $arr_result3);
        foreach ((array)$arr_result3[0] as $value) { // one loop for every twittername in string
            $value = trim($value); // trim it
            if (!empty($value)) { // if there is a value
                $typoLinkConf = ['parameter' => 'https://twitter.com/search?q=%23' . str_replace('#', '', $value)]; // typolink configuration
                $typoLinkConf = array_merge((array)$conf['typolink.'], $typoLinkConf); // get params from typoscript
                $string = str_replace(
                    $value,
                    $this->cObj->typoLink($value, $typoLinkConf),
                    $string
                ); // replace each url with typolink
            }
        }

        return $string; // return string without another string
    }

    /**
     * change Changes "RT" to retweet icon
     *
     * @var        string $content: Empty content variable
     * @var        array $conr: TypoScript configuration for this userFunc
     * @return    string        $content: new content
     */
    public function user_wttwitter_retweetIcon($content = '', $conf = [])
    {
        // config
        $conf = $conf['userFunc.']; // ts config

        // let's go
        $string = $this->cObj->cObjGetSingle($conf['string'], $conf['string.']); // get string from ts
        $image = $this->cObj->cObjGetSingle($conf['image'], $conf['image.']); // get image from ts

        return preg_replace('/RT /', $image . ' ', $string); // replace and return
    }

    /**
     * change xml pubDate to a readable format
     *
     * @var        string $content: Empty content variable
     * @var        array $conr: TypoScript configuration for this userFunc
     * @return    string        $date: timestamp from pubDate
     */
    public function user_wttwitter_date($content = '', $conf = [])
    {
        $conf = $conf['userFunc.']; // ts config

        $date = $this->cObj->getCurrentVal(); // get date from typoscript
        $timestamp = strtotime($date); // change to timestamp
        $date = strftime($conf['strftime'], $timestamp); // change back to readable format

        return $date;
    }
}
