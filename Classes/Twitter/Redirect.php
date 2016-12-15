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

require_once('Api.php');

$oAuthToken = '';
$oAuthTokenSecret = '';
if (!empty($_GET['oauth_token']) && !empty($_GET['oauth_verifier'])) {
    $url = Tx_WtTwitter_Twitter_Api::getOAuthAccessTokenUrl();

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);

    $oAuthParameter = Tx_WtTwitter_Twitter_Api::createSignature(
        Tx_WtTwitter_Twitter_Api::getOAuthParameter(
            $_GET['oauth_token'],
            [
                'oauth_token' => $_GET['oauth_token'],
                'oauth_verifier' => $_GET['oauth_verifier']
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
        $responseArray = [];
        $responseParts = explode('&', $response);
        foreach ($responseParts as $value) {
            if (strlen($value)) {
                list($partKey, $partValue) = explode('=', $value, 2);
                $responseArray[rawurldecode($partKey)] = rawurldecode($partValue);
            }
        }
        $oAuthToken = $responseArray['oauth_token'];
        $oAuthTokenSecret = $responseArray['oauth_token_secret'];
    }
    curl_close($ch);
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>wt_twitter redirection page</title>
</head>
<body>
<script type="text/javascript">
	if (parent.window.opener) {
		oAuthTokenObject = "";
		oAuthTokenSecretObject = "";
		if (parent.window.opener.document.configurationform) {
			oAuthTokenObject = parent.window.opener.document.configurationform["tx_extensionmanager_tools_extensionmanagerextensionmanager[config][oauth_token][value]"];
			oAuthTokenSecretObject = parent.window.opener.document.configurationform["tx_extensionmanager_tools_extensionmanagerextensionmanager[config][oauth_token_secret][value]"];
		} else {
			if (parent.window.opener.document.forms) {
				for (i=0; i<parent.window.opener.document.forms.length; i++) {
					if (typeof parent.window.opener.document.forms[i]["data[oauth_token]"] != "undefined") {
						oAuthTokenObject = parent.window.opener.document.forms[i]["data[oauth_token]"];
						oAuthTokenSecretObject = parent.window.opener.document.forms[i]["data[oauth_token_secret]"];
						break;
					}
				}
			}
		}
		if (oAuthTokenObject != "" && oAuthTokenSecretObject != "") {
			<?php
                    echo 'oAuthTokenObject.value = "' . $oAuthToken . '";';
                    echo 'oAuthTokenSecretObject.value = "' . $oAuthTokenSecret . '";';
            ?>
			parent.window.opener.focus();
			parent.close();
		}
	}
</script>

<p>If this window doesn't close itself, something went wrong. Please copy the tokens manually to the extension configuration or try to sign in with Twitter again.</p>

<p>Access token: <?php echo $oAuthToken; ?></p>

<p>Access token secret: <?php echo $oAuthTokenSecret; ?></p>
</body>
</html>