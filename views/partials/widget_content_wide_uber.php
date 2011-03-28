<div class="nerd_card">
	<div class="nerd_image"><img src="<?= $profile_avatar ?>" border="0"></div>
	<div class="nerd_points"><?= $checkin_count ?> <span class="nerd_points_word">nerdouts</span></div>
	<div class="clear"></div>			
	<a class="nerd_name" href="<?= $profile_link ?>" target="_blank"><?= $profile_name ?></a>
	<ul>
		<?php foreach($checkins as $checkin): $place = json_decode($checkin->data); ?>
		<li><a href="<?= $place->url ?>"><?= character_limiter($place->title, 16) ?></a> <span class="nerdout_date"><?= format_datetime('TIME', $checkin->created_at) ?></span></li>
		<?php endforeach; ?>
	</ul>
	<p><a href="<?= $profile_link ?>" target="_blank">Follow</a></p>
</div>
