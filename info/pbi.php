<?
error_reporting(E_ALL);
ini_set("display_errors", 1);

// echo "<pre>";
// print_r($_POST);
// print_r($_GET);
// echo "</pre>"; 

/* Get oauth2 token using a POST request */
$curlPostToken = curl_init();
curl_setopt($curlPostToken, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curlPostToken, CURLOPT_SSL_VERIFYPEER, 0);

curl_setopt_array($curlPostToken, array(
	CURLOPT_URL => "https://login.windows.net/common/oauth2/token",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "POST",
	CURLOPT_POSTFIELDS => array(
		grant_type => 'password',
		scope => 'openid',
		resource => 'https://analysis.windows.net/powerbi/api',
		client_id => '5e4523d6-3140-480c-bdbb-cac1137842e8', // Registered App ApplicationID
		username => 'bi@serambiental.com', // for example john.doe@yourdomain.com
		password => 'Negocios2018' // Azure password for above user
		)
	));
$tokenResponse = curl_exec($curlPostToken);
$tokenError = curl_error($curlPostToken);
curl_close($curlPostToken);
Print_R($tokenResponse);
// decode result, and store the access_token in $embeddedToken variable:
$tokenResult = json_decode($tokenResponse, true);
$token = $tokenResult["access_token"];
$embeddedToken = "Bearer "  . ' ' .  $token;

/*      Use the token to get an embedded URL using a GET request */
$curlGetUrl = curl_init();
curl_setopt($curlGetUrl, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($curlGetUrl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt_array($curlGetUrl, array(
	CURLOPT_URL => "https://api.powerbi.com/v1.0/myorg/groups/b7224d4a-905d-4654-83a4-024fdd693fe4/reports/",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 30,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => array(
		"Authorization: $embeddedToken",
		"Cache-Control: no-cache",
		),
	)
);
$embedResponse = curl_exec($curlGetUrl);
$embedError = curl_error($curlGetUrl);
curl_close($$curlGetUrl);

if ($embedError) {
	echo "cURL Error #:" . $embedError;
} else {
	$embedResponse = json_decode($embedResponse, true);
	$embedUrl = $embedResponse['value'][0]['embedUrl'];
}
?>

<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="scripts/powerbi.js"></script>
<div id="reportContainer"></div>
<script>
// Get models. models contains enums that can be used.
var models = window['powerbi-client'].models;
// Embed configuration used to describe the what and how to embed.
// This object is used when calling powerbi.embed.
// This also includes settings and options such as filters.
// You can find more information at https://github.com/Microsoft/PowerBI-JavaScript/wiki/Embed-Configuration-Details.

var embedConfiguration= {
	type: 'report',
	id: '436dc0b9-7d94-4563-b4fb-dc00b4d73e98', // the report ID
	embedUrl: "<?php echo $embedUrl ?>",
	accessToken: "<?php echo $token; ?>" ,
};

var $reportContainer = $('#reportContainer');
var report = powerbi.embed($reportContainer.get(0), embedConfiguration);
</script>
