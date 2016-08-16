	<footer id="footer" class="container clearfix">
		<p><?php if ( of_get_option('viro_copyright') <> "" ) { echo of_get_option('viro_copyright'); } else { echo 'Copyright &copy; '; echo date('Y'); echo '<strong> '; bloginfo(); echo '</strong>'; } ?></p>
	</footer> 
	
	<?php wp_footer(); ?>
	
	</body>
</html>