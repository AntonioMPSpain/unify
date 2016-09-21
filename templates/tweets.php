<!-- Latest Tweets -->
<div class="col-md-9 md-margin-bottom-40">
	<div class="latest-tweets">
		<div class="headline"><h2 class="heading-sm">Tweets</h2></div>
		{% for tweet in tweets %}
			<div class="latest-tweets-inner">
				<i class="fa fa-twitter"></i>
				<p>
					<a target="_blank" href="https://twitter.com/activatie">@activatie: </a>
					{{ tweet.tweet|raw }} 
					<small class="twitter-time">hace {{ tweet.tiempo }}</small>
				</p>
			</div>
		{% endfor %}
	</div>
</div>
<!-- End Latest Tweets -->