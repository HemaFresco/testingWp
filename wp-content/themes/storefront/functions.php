<?php
/**
 * Storefront engine room
 *
 * @package storefront
 */

/**
 * Assign the Storefront version to a var
 */
$theme              = wp_get_theme( 'storefront' );
$storefront_version = $theme['Version'];

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 980; /* pixels */
}

$storefront = (object) array(
	'version'    => $storefront_version,

	/**
	 * Initialize all the things.
	 */
	'main'       => require 'inc/class-storefront.php',
	'customizer' => require 'inc/customizer/class-storefront-customizer.php',
);

require 'inc/storefront-functions.php';
require 'inc/storefront-template-hooks.php';
require 'inc/storefront-template-functions.php';
require 'inc/wordpress-shims.php';

if ( class_exists( 'Jetpack' ) ) {
	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';
}

if ( storefront_is_woocommerce_activated() ) {
	$storefront->woocommerce            = require 'inc/woocommerce/class-storefront-woocommerce.php';
	$storefront->woocommerce_customizer = require 'inc/woocommerce/class-storefront-woocommerce-customizer.php';

	require 'inc/woocommerce/class-storefront-woocommerce-adjacent-products.php';

	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';
	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';
	require 'inc/woocommerce/storefront-woocommerce-functions.php';
}

if ( is_admin() ) {
	$storefront->admin = require 'inc/admin/class-storefront-admin.php';

	require 'inc/admin/class-storefront-plugin-install.php';
}

/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */
if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {
	require 'inc/nux/class-storefront-nux-admin.php';
	require 'inc/nux/class-storefront-nux-guided-tour.php';
	require 'inc/nux/class-storefront-nux-starter-content.php';
}

/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */



 
function file_upload_callback()
{	

	//$files = $_FILES;

	//print_r($files); die;
	echo $fileCount = count($_FILES['file']['name']);
	//die;


	//check_ajax_referer('file_upload', 'security');
	$arr_img_ext = array('image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/svg', 'image/webp');
	$attach_id = null;


	foreach ( $_FILES['file']['name'] as $f => $name ) {
		if (in_array($_FILES['file']['type'], $arr_img_ext)) {
			$upload = wp_upload_bits($_FILES["file"]["name"], null, file_get_contents($_FILES["file"]["tmp_name"]));
			$guid = $upload["url"];
		//	print_r($upload);

			$attachments = array(
				'guid'              =>  $upload["url"],
				'post_mime_type'    =>  $upload["type"],
				'post_title'        =>  preg_replace('/\.[^.]+$/', '', $name ),
				'post_content'      =>  '',
				'post_status'       =>  'inherit',
			);
			$uploadFiles = $upload["file"];
			$userId = get_current_user_id();
			
			$attach_id = wp_insert_attachment( $attachments, $uploadFiles, $userId );

			$attach_data = wp_generate_attachment_metadata($attach_id, $upload["file"]);
			wp_update_attachment_metadata($attach_id, $attach_data);
			
		}
	
		$file_url = $upload["url"];
		$user_id = get_current_user_id();
		$meta = get_user_meta($user_id, "gallery_images", true);
		
		if ($meta !== "") {
			if ($meta=="[]"){
				$meta = array();
			}
			$meta[] = $attach_id;
			$updated = update_user_meta($user_id, 'gallery_images', $meta);
		} else {
			$gallery = array();
			$gallery[] = $attach_id;
			add_user_meta($user_id, 'gallery_images', $gallery);
		}
	
	
	}
	wp_die();
	
	
}
add_action('wp_ajax_file_upload', 'file_upload_callback');
add_action('wp_ajax_nopriv_file_upload', 'file_upload_callback');