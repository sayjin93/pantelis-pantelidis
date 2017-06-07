<?php

$client_id = $_GET["client_id"];
$track_id  = intval( $_GET["track_id"] );

$api_call = 'https://api.soundcloud.com/tracks/' . $track_id . '/stream/?client_id=' . $client_id . '&format=json';

/**
 * gets the html content from a URL
 *
 * @param  string $url
 *
 * @return string
 */
function curl_get_contents( $url ) {
	$c = curl_init();
	curl_setopt( $c, CURLOPT_RETURNTRANSFER, 1 );
	curl_setopt( $c, CURLOPT_URL, $url );
	curl_setopt( $c, CURLOPT_TIMEOUT, 30 );
	curl_setopt( $c, CURLOPT_SSL_VERIFYHOST, 0 );
	curl_setopt( $c, CURLOPT_SSL_VERIFYPEER, 0 );

	// curl_setopt($c, CURLOPT_FOLLOWLOCATION, TRUE);
	// $contents = curl_exec($c);
	$contents = curl_exec_follow( $c );

	curl_close( $c );

	return ( $contents ) ? $contents : false;

}

/**
 * [curl_exec_follow description] http://us2.php.net/manual/en/function.curl-setopt.php#102121
 *
 * @param  curl $ch handler
 * @param  integer $maxredirect hoe many redirects we allow
 *
 * @return contents
 */
function curl_exec_follow( $ch, $maxredirect = 5 ) {
	//using normal curl redirect
	if ( ini_get( 'open_basedir' ) == '' AND ini_get( 'safe_mode' == 'Off' ) ) {
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, $maxredirect > 0 );
		curl_setopt( $ch, CURLOPT_MAXREDIRS, $maxredirect );
	} //using safemode...WTF!
	else {
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );
		if ( $maxredirect > 0 ) {
			$newurl = curl_getinfo( $ch, CURLINFO_EFFECTIVE_URL );

			$rch = curl_copy_handle( $ch );
			curl_setopt( $rch, CURLOPT_HEADER, true );
			curl_setopt( $rch, CURLOPT_NOBODY, true );
			curl_setopt( $rch, CURLOPT_FORBID_REUSE, false );
			curl_setopt( $rch, CURLOPT_RETURNTRANSFER, true );

			do {
				curl_setopt( $rch, CURLOPT_URL, $newurl );
				$header = curl_exec( $rch );
				if ( curl_errno( $rch ) ) {
					$code = 0;
				} else {
					$code = curl_getinfo( $rch, CURLINFO_HTTP_CODE );
					if ( $code == 301 OR $code == 302 ) {
						preg_match( '/Location:(.*?)\n/', $header, $matches );
						$newurl = trim( array_pop( $matches ) );
					} else {
						$code = 0;
					}
				}
			} while ( $code AND -- $maxredirect );

			curl_close( $rch );

			if ( ! $maxredirect ) {
				if ( $maxredirect === null ) {
					trigger_error( 'Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING );
				} else {
					$maxredirect = 0;
				}

				return false;
			}

			curl_setopt( $ch, CURLOPT_URL, $newurl );
		}
	}

	$h = curl_getinfo( $ch );

	return $h;
}


$myUrlInfo = curl_get_contents( $api_call );
$realurl   = $myUrlInfo["url"];

echo $realurl;

?>