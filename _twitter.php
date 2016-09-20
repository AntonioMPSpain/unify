<?

require "libs/twitteroauth/autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

$consumer_key = "pL5K3qImFsQT2xYG2dVHcEArl";
$consumer_key_secret = "WEnvi8quc1MzegcW79ZPq2MuC6pEjzzQyWC5kQZvudWiNZSlXi";
$acess_token = "2359959254-gx8eec8yASjCLLjkWbvKCRRRidROshEMZFMEjJW";
$acess_token_secret = "BZVWtKzoM98tfyeO9wOwyrTkdbNB86jMAjfLWHW32FoWx";

$twitter = new TwitterOAuth($consumer_key, $consumer_key_secret, $access_token, $access_token_secret);

getFooterTweets();

function getFooterTweets(){
	
	global $twitter;
	$content = $twitter->get("statuses/user_timeline", ["screen_name" => activatie, "include_rts" => false,"count" => 7 , "exclude_replies" => true ]);
	
	$i=0;
	$tweets = "";
	foreach ($content as $cont){
		
		echo $cont->text."<br>";
		$tweets[$i]=$cont->text;
	}
	
	
	return $content;
	
}




?>