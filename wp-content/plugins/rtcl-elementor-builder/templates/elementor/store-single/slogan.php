<?php
/**
 * Store single content
 *
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.3.21
 */

?>
<div class="store-details">
	<?php if ( $store->get_the_slogan() ) : ?>
		<h3 class="is-slogan"><?php $store->the_slogan(); ?></h3>
	<?php endif; ?>
</div>
