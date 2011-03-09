<link rel="alternate" type="application/rss+xml" title="<?= $site_title ?> RSS 2.0 Nerdouts <?= ucwords($this->uri->segment(2)) ?> " href="<?= base_url().'feed/nerdout/'.$this->uri->segment(2) ?>" />

<?php if ($this->uri->segment(2) == 'city'): ?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="https://github.com/JasonSanford/GeoJSON-to-Google-Maps/raw/master/GeoJSON.js"></script>
<script type="text/javascript" src="<?= $site_assets ?>js/gmaps.js"></script>
<?php endif; ?>