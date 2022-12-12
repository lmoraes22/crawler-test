<?php
	
	const APPLICANT_TEST_URL = 'http://applicant-test.us-east-1.elasticbeanstalk.com/';

	$rawResponse = fetchUrl('GET', APPLICANT_TEST_URL);

    list($cookieData, $body) = splitCookies($rawResponse);

    preg_match('#<input[^>]* id="token" value="([^">]+)" \/>#is', $body, $tokenMatches);

    $originalToken = $tokenMatches[1] ?? '';

    $parsedToken = parseToken($originalToken);

    echo 'originalToken: ' . $originalToken . '<hr>';
    echo 'parsedToken: ' . $parsedToken . '<hr>';
    echo 'cookieData: ' . $cookieData . '<hr>';

    $rawResponse = fetchUrl('POST', APPLICANT_TEST_URL, "token={$parsedToken}", $cookieData);
    $body = splitCookies($rawResponse)[1];

    preg_match('#<span[^<>]*>([\d,]+).*?</span>#', $body, $answer);

    echo 'RESULT: '. $answer[1];

    die();

	function fetchUrl($method, $url, $postData = '', $cookieData = '')
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_ENCODING, "UTF-8");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_COOKIE, $cookieData);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Origin: http://applicant-test.us-east-1.elasticbeanstalk.com/',
            'Referer: http://applicant-test.us-east-1.elasticbeanstalk.com/',
            'Host: applicant-test.us-east-1.elasticbeanstalk.com',
        ]);

        $rawResponse = curl_exec($ch);

        curl_close($ch);

        return $rawResponse;
    }

    function parseToken($token)
    {
        $newToken = '';

        $replacements = [
            'a'=> 'z',
            'b'=> 'y',
            'c'=> 'x',
            'd'=> 'w',
            'e'=> 'v',
            'f'=> 'u',
            'g'=> 't',
            'h'=> 's',
            'i'=> 'r',
            'j'=> 'q',
            'k'=> 'p',
            'l'=> 'o',
            'm'=> 'n',
            'n'=> 'm',
            'o'=> 'l',
            'p'=> 'k',
            'q'=> 'j',
            'r'=> 'i',
            's'=> 'h',
            't'=> 'g',
            'u'=> 'f',
            'v'=> 'e',
            'w'=> 'd',
            'x'=> 'c',
            'y'=> 'b',
            'z'=> 'a',
            '0'=> '9',
            '1'=> '8',
            '2'=> '7',
            '3'=> '6',
            '4'=> '5',
            '5'=> '4',
            '6'=> '3',
            '7'=> '2',
            '8'=> '1',
            '9'=> '0'
        ];


        for ($i = 0; $i < strlen($token); $i++) {
            $newToken .= $replacements[$token[$i]];
        }

        return $newToken;
    }

    function splitCookies($rawResponse)
    {
        list($curlHeader, $curlBody) = preg_split("/\R\R/", $rawResponse, 2);

        preg_match_all("/^Set-Cookie:\s+(.*);/mU", $curlHeader, $cookieMatchArray);
        $cookieData = implode(';', $cookieMatchArray[1]);

        return [$cookieData, $curlBody];
    }

?>