<?php
/**
 * Plugin Name:         Media List and Export
 * Plugin URI:          https://jasonlawton.com/plugins/media-list-export
 * Description:         Show a list of uploaded media and add the ability to export it
 * Author:              Jason Lawton
 * Author URI:          https://jasonlawton.com/
 * Version:             0.1
 * Minimum PHP Version: 7.3
 * Text Domain:         medialistexport
 * License:             GPLv2 or later (license.txt)
 *
 * @package MediaListExport
 */

define( 'JHL_MLE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'JHL_MLE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

add_action( 'admin_menu', 'jhl_mle_add_admin_menu' );

function jhl_mle_add_admin_menu(  ) { 
    
	// add_menu_page( 'List media', 'List media', 'manage_categories', 'jhl_list_plugins', 'jhl_mle_options_page' );
    add_submenu_page(
        'upload.php', // parent slug
        'Media list', // page title
        'Media list', // menu title
        'manage_options', // capability
        'media-list', // menu slug
        'jhl_mle_options_page' // callback function
    );

}

function jhl_mle_options_page(  ) { 
    global $post;

    // get the media
    $query_images_args = array(
        'post_type'      => 'attachment',
        'post_mime_type' => 'image',
        'post_status'    => 'inherit',
        'posts_per_page' => -1,
    );
    $query_images = new WP_Query( $query_images_args );
    foreach ( $query_images->posts as $image ) {
        $image->attachment_url = wp_get_attachment_url( $image->ID );
        $image->attachment_path = get_attached_file( $image->ID );
    }
    ?>

    <div class="wrap">
        <h1 class="wp-heading-inline">Media list</h1>

        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.7.0/css/buttons.dataTables.min.css">

        <script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
        
        <table class="media-list wp-list-table widefat fixed striped posts">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date added</th>
                    <th>Size</th>
                    <th>File type</th>
                </tr>
            </thead>
            <tbody>
                <?php if ( $query_images->have_posts() ) {
                    while ( $query_images->have_posts() ) {
                        $query_images->the_post();
                        ?>
                        <tr>
                            <td width="60%">
                                <?php the_title(); ?>
                            </td>
                            <td width="5%"><?php echo get_the_date(); ?></td>
                            <td width="15%"><?php echo filesize( $post->attachment_path ); ?></td>
                            <td width="20%"><?php echo $post->post_mime_type; ?></td>
                        </tr>
                        <?php
                    }
                } ?>
            </tbody>
        </table>

        <script>
        jQuery(document).ready(function () {
            jQuery('.media-list').DataTable({
                "dom": 'Bfrtip',
                "buttons": [
                    'csv',
                ],
            });
        });
        </script>
    </div>
    <?php
}