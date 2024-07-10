<?php
/**
 * The template for displaying search forms.
 */
?>
<form class="search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<div class="input-group">
		<input type="text" name="s" class="form-control" placeholder="<?php esc_attr_e( 'Search', 'readytek' ); ?>" />
		<button type="submit" class="btn btn-secondary" name="submit"><img src="/wp-content/themes/readytek/assets/images/search-ico.svg" alt="*"></button>
	</div><!-- /.input-group -->
</form>
