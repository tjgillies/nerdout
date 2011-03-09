<div class="content_wide">
	<h2>Nerd Cities</h2>
	<a class="button" href="<?= base_url() ?>portland">Portland</a>	
	<div class="clear"></div>
</div>

<div class="content_wide">
	<h2>Get Your Nerd On!</h2>
	<div id="get_your_nerd_on">
		<a class="button" href="<?= base_url() ?>signup">Signup</a>	<?= $this->social_igniter->get_social_logins('<div class="social_login">', '</div>'); ?>
		<div class="clear"></div>
	</div>
</div>