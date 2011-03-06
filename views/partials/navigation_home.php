<h2 class="content_title"><img src="<?= $modules_assets ?>nerdout_32.png"> Nerdout</h2>
<ul class="content_navigation">
	<?= navigation_list_btn('home/nerdout', 'Recent') ?>
	<?= navigation_list_btn('home/nerdout/custom', 'Custom') ?>
	<?php if ($logged_user_level_id <= 2) echo navigation_list_btn('home/nerdout/manage', 'Manage', $this->uri->segment(4)) ?>
</ul>