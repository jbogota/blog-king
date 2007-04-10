<div id="footer"><p><a href="http://www.website-king.de/" id="wordpress-logo"><img src="images/wordpress-logo.png" alt="Website King" /></a></p>
<p>
<small>Website King Blog <a href="http://www.websiteking.de/blog/"></a>
<br />Englischsprachige<a href="http://codex.wordpress.org/"><?php _e('Documentation'); ?></a> &#38; <a href="http://wordpress.org/support/"><?php _e('Support Forums'); ?></a>
 | Deutschsprachige <a href="http://doku.wordpress.de/"><?php _e('Documentation'); ?></a> &#38; <a href="http://forum.wordpress.de/"><?php _e('Support Forums'); ?></a></small>

<br />
v<?php bloginfo('version'); ?> &#8212; <?php printf(__('%s seconds'), number_format(timer_stop(), 2)); ?>
</p>

</div>
<?php do_action('admin_footer', ''); ?>
<script type="text/javascript">if(typeof wpOnload=='function')wpOnload();</script>

</body>
</html>