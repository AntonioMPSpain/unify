<?

$pullright="";
if (isset($_REQUEST['right'])){
	$pullright= "pull-right";
}

?>
<!-- Social Links -->
<ul class="social-icons <?=$pullright?>">
	<li><a target="_blank" href="https://www.facebook.com/pages/Activatie/783928711720244" data-original-title="Facebook" class="rounded-x social_facebook"></a></li>
	<li><a target="_blank" href="https://twitter.com/activatie" data-original-title="Twitter" class="rounded-x social_twitter"></a></li>
	<li><a target="_blank" href="https://es.linkedin.com/in/activatie-ab907bba" data-original-title="Linkedin" class="rounded-x social_linkedin"></a></li>
	<li><a target="_blank" href="https://twitter.com/activatie" data-original-title="Youtube" class="rounded-x social_youtube"></a></li>
	<li><a target="_blank" href="<?=$rsspath?>rss.xml" data-original-title="RSS" class="rounded-x social_rss"></a></li>
</ul>
<!-- End Social Links -->