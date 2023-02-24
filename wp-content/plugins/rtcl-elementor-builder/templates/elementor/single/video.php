<?php
/**
 * @author     RadiusTheme
 * @package    classified-listing/templates
 * @version    1.0.0
 *
 * @var array[] $images
 * @var array[] $videos
 * @var string  $video_url
 */

use Rtcl\Helpers\Functions;

$total_gallery_videos = count( $videos );

if ( $total_gallery_videos ) :
	foreach ( $videos as $index => $video_url ) { ?>
		<div class="rtcl el-single-addon listing-video-widget">
			<iframe class="rtcl-lightbox-iframe" src="<?php echo Functions::get_sanitized_embed_url( $video_url ); ?>" style=" margin: 0;padding: 0; background-color: #000" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
		</div>
		<?php
	}
endif;
