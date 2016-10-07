<?

include_once "_config.php";

require $libspath."twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key = "pL5K3qImFsQT2xYG2dVHcEArl";
$consumer_key_secret = "WEnvi8quc1MzegcW79ZPq2MuC6pEjzzQyWC5kQZvudWiNZSlXi";
$acess_token = "2359959254-gx8eec8yASjCLLjkWbvKCRRRidROshEMZFMEjJW";
$acess_token_secret = "BZVWtKzoM98tfyeO9wOwyrTkdbNB86jMAjfLWHW32FoWx";

$twitter = new TwitterOAuth($consumer_key, $consumer_key_secret, $access_token, $access_token_secret);

function getFooterTweets(){
	
	global $twitter;
	$numTweets = 3;
	$content = $twitter->get("statuses/user_timeline", ["screen_name" => activatie, "include_rts" => false,"count" => 30 , "exclude_replies" => true ]);
	
	$i=0;
	$tweets = "";
	
	foreach ($content as $cont){
		
		$asda = $cont->created_at."<br>";
		$tweet["tweet"] = buscarLinkGenerarHTML($cont->text);
		$tweet["tiempo"] = time_elapsed_string(($cont->created_at));
		$tweets[$i] = $tweet;
		$i++;
		
		if ($i==$numTweets){
			break;
		}
	}
	
	return $tweets;
	
}




?>