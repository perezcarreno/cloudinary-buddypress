<?php

/* ----------------------------------------------------------------------------
 *
 * Youzer Related Code
 * 
 * ----------------------------------------------------------------------------
 */


/**
 * Disable Images Compression
 * Provided by Youzer team
 * Using this function we disable Youzer's attachments compression, as Cloudinary will handle it for us.
 */
function yzc_enable_attachments_compression( $enable ) {
	return false;
}
add_filter('yz_enable_attachments_compression', 'yzc_enable_attachments_compression' );


/**
 * Include Cloudinary SDK
 * We include Cloudinary's SDK to be able to access their upload functionality.
 * Note: Make sure to download the cloudinary_sdk folder into your theme's lib folder.
 */
add_action( 'init', 'pcc_include_cloudinary' );

function pcc_include_cloudinary() {
	// Your Cloudinary credentials (keep these secret)
	$pcc_cloudinary_cloud_name = 'XXXXXXXXXXXXX'; // -- Enter your own Cloudinary credentials
	$pcc_cloudinary_api_key = 'XXXXXXXXXXXXX'; // -- Enter your own Cloudinary credentials
	$pcc_cloudinary_api_secret = 'XXXXXXXXXXXXX'; // -- Enter your own Cloudinary credentials

	// Include Cloudinary SDK from current theme's 'lib' folder (make sure it exists first)
	include_once get_stylesheet_directory() . '/lib/cloudinary_sdk/autoload.php';

	// Set up Cloudinary configuration (maybe move out of this file)
	\Cloudinary::config(array( 
	  "cloud_name" => $pcc_cloudinary_cloud_name, 
	  "api_key" => $pcc_cloudinary_api_key, 
	  "api_secret" => $pcc_cloudinary_api_secret 
	));
}


/*
 * Add Cloudinary Integration
 * This function hooks into Youzer's upload attachment function and uses Cloudinary's SDK to upload media
 * to your Cloudinary account. It also adds a tag with the user's ID for easy searching.
 * Note: This assumes you want to use a "community" folder on your Cloudinary account to store the files. Create it beforehand.
 */
function pcc_add_cloudinary_integration( $uploadedfile, $movefile ) {

    // Get current user
    $user = wp_get_current_user();

    // Get a file id (name without extension) from the file url
    $fileid = pathinfo( $uploadedfile['real_name'], PATHINFO_FILENAME);

    // Get Uploaded File extension
    $ext = strtolower( pathinfo( $uploadedfile['real_name'], PATHINFO_EXTENSION ) );

    switch ( $ext ) {
        case "jpg":
        case "jpeg":
        case "png":
        case "gif":
            // Check if image size is less than 40MB
            if ( $uploadedfile['file_size'] < 41943040 ) {
                // Transfers file to Cloudinary using image parameters and tags it to the user id
                $cloudfile = \Cloudinary\Uploader::upload( $movefile['url'] ,
                array(
                    "resource_type" => "image",
                    "width" => 1920,
                    "height" => 1920,
                    "crop" => "limit",
                    "quality"=>"auto",
                    "public_id" => "community/" . $fileid,
                    "tags" => "user" . $user->ID ));
            } else {
                // Transfers file to Cloudinary using upload_large image parameters and tags it to the user id
                $cloudfile = \Cloudinary\Uploader::upload_large( $movefile['url'] ,
                array(
                    "resource_type" => "image",
                    "chunk_size" => 6000000,
                    "width" => 1920,
                    "height" => 1920,
                    "crop" => "limit",
                    "quality"=>"auto",
                    "public_id" => "community/" . $fileid,
                    "tags" => "user" . $user->ID ));
            }

            break;
        case "mp4":
        case "mov":
        case "ogg":
        case "ogv":
        case "webm":
            // Check if video size is less than 40MB
            if ( $uploadedfile['file_size'] < 41943040 ) {
                // Transfers file to Cloudinary using video parameters and tags it to the user id
                $cloudfile = \Cloudinary\Uploader::upload( $movefile['url'] ,
                array(
                    "resource_type" => "video",
                    "width" => 1920,
                    "height" => 1920,
                    "crop" => "limit",
                    "quality"=>"auto",
                    "public_id" => "community/" . $fileid,
                    "tags" => "user" . $user->ID ));
            } else {
                // Transfers file to Cloudinary using upload_large video parameters and tags it to the user id
                $cloudfile = \Cloudinary\Uploader::upload_large( $movefile['url'] ,
                array(
                    "resource_type" => "video",
                    "chunk_size" => 6000000,
                    "public_id" => "community/" . $fileid,
                    "eager" => array(
                        array(
                            "width" => 1920, "height" => 1920, "crop" => "limit", "quality" => "auto", "video_codec" => "auto", "format" => "mp4"
                        ), array (
                            "width" => 1920, "height" => 1920, "crop" => "limit", "quality" => "auto", "video_codec" => "auto", "format" => "ogg"
                        ), array (
                            "width" => 1920, "height" => 1920, "crop" => "limit", "quality" => "auto", "video_codec" => "auto", "format" => "webm"
                        ), array (
                            "width" => 1920, "height" => 1920, "crop" => "limit", "quality" => "auto", "video_codec" => "auto", "format" => "mov"
                        )
                    ),
                    "eager_async" => true,
                    "tags" => "user" . $user->ID ));
            }

            break;
        default:

    }


}
add_action( 'yz_after_attachments_upload', 'pcc_add_cloudinary_integration', 10, 2 );


/**
 * Override Youzer's get_wall_post_video function
 * This function returns the Cloudinary video player instead of the regular video tag
 */
function pcc_get_wall_post_video( $video_content, $attachments ) {
    // Cloudinary Cloud name
    $pcc_cloudinary_cloud_name = 'XXXXXXXXXXXXX'; // -- Enter your cloud name from your Cloudinary account

	// Get Video Url.
	$video_url = yz_get_file_url( $attachments[0] );
	// Get a video id (name without extension) from the video
	$video_id = pathinfo($video_url, PATHINFO_FILENAME);
	// Start output capture
	ob_start();
	?>

	<video
	  id="<?php echo $video_id; ?>"
	  controls
	  muted
	  class="cld-video-player cld-fluid"
	  data-cld-autoplay-mode="on-scroll"
	  data-cld-source-types='["mp4", "ogg", "webm", "mov"]'
	  data-cld-transformation='{"width": 1920, "height": 1920, "crop": "limit", "quality": "auto", "video_codec": "auto"}'
	  data-cld-public-id="<?php echo "community/" . $video_id; ?>">
	</video>

	<script>
	var cld = cloudinary.Cloudinary.new({ cloud_name: '<?php echo $pcc_cloudinary_cloud_name; ?>' });
	var player = cld.videoPlayer('<?php echo $video_id; ?>');
	</script>
	<?php
	// Return output
	return ob_get_clean();
}
add_filter('yz_get_wall_post_video', 'pcc_get_wall_post_video', 10, 2 );


/**
 * Override Youzer's yz_get_media_url function
 * This functions returns the Cloudinary image URL instead of the Youzer directory image URL
 */
function pcc_get_cloudinary_media_url( $file_url, $file_name ) {
    // Cloudinary Cloud name
    $pcc_cloudinary_cloud_name = 'XXXXXXXXXXXXX'; // -- Enter your cloud name from your Cloudinary account

	// Cloudinary image path (including 'community' directory)
	$cloudinary_path = 'https://res.cloudinary.com/' . $pcc_cloudinary_cloud_name . '/image/upload/f_auto/community/';
	// Get an image id (name without extension) from the image url
	$img_id = pathinfo($file_name, PATHINFO_FILENAME);
	// Add filename to Cloudinary path
	$img_url = $cloudinary_path . $img_id;
	// Return resulting file url
	return $img_url;
}
add_filter('yz_get_media_url', 'pcc_get_cloudinary_media_url', 10, 3 );


/**
 * Override Youzer's yz_wall_attachment_filename function
 * This function creates a custom unique filename based on a custom prefix and the user's ID
 */
function pcc_update_wall_attachment_filename( $filename, $ext ) {
    // Custom name prefix
    $pcc_custom_filename_prefix = 'XXXXXXXXXXXXX_'; // -- Enter the prefix you want to use for your files (example: mysite_)
	// Get current user
	$user = wp_get_current_user();

	// Create Unique user-based file id and name (with extension)
	$fileid = $pcc_custom_filename_prefix . $user->ID . uniqid( '_file_' );
	$filename = $fileid . '.' . $ext;

	return $filename;

}
add_filter('yz_wall_attachment_filename', 'pcc_update_wall_attachment_filename', 10, 2 );


/**
 * Add Cloudinary Video Player code to BP related pages
 * This function adds the video player JS and CSS code to any BuddyPress related pages.
 */
function pcc_add_cloudinary_player_head() {
	// IF on any BP related page
  	if ( bp_is_single_item() || bp_is_my_profile() || bp_is_group() || bp_is_groups_component() || bp_is_directory() ) { 
    ?>
        <link href="https://unpkg.com/cloudinary-video-player@1.4.0/dist/cld-video-player.min.css" rel="stylesheet">
		<script src="https://unpkg.com/cloudinary-core@2.8.2/cloudinary-core-shrinkwrap.min.js" type="text/javascript"></script>
		<script src="https://unpkg.com/cloudinary-video-player@1.4.0/dist/cld-video-player.min.js" 
    type="text/javascript"></script>
    <?php
  }
}
add_action('wp_head', 'pcc_add_cloudinary_player_head');



// END Buddypress mods
?>