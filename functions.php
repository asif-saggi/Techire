<?php

include 'phpseclib/Net/SCP.php';
include 'phpseclib/Net/SSH2.php';

/**
 * Include Theme Customizer.
 *
 * @since v1.0
 */
//date_default_timezone_set('America/Los_Angeles');
$theme_customizer = __DIR__ . '/inc/customizer.php';
if (is_readable($theme_customizer)) {
    require_once $theme_customizer;
}

if (!function_exists('readytek_setup_theme')) {
    /**
     * General Theme Settings.
     *
     * @since v1.0
     *
     * @return void
     */
    function readytek_setup_theme()
    {
        // Make theme available for translation: Translations can be filed in the /languages/ directory.
        load_theme_textdomain('readytek', __DIR__ . '/languages');

        /**
         * Set the content width based on the theme's design and stylesheet.
         *
         * @since v1.0
         */
        global $content_width;
        if (!isset($content_width)) {
            $content_width = 800;
        }

        // Theme Support.
        add_theme_support('title-tag');
        add_theme_support('automatic-feed-links');
        add_theme_support('post-thumbnails');
        add_theme_support(
            'html5',
            array(
                'search-form',
                'comment-form',
                'comment-list',
                'gallery',
                'caption',
                'script',
                'style',
                'navigation-widgets',
            )
        );

        // Add support for Block Styles.
        add_theme_support('wp-block-styles');
        // Add support for full and wide alignment.
        add_theme_support('align-wide');
        // Add support for Editor Styles.
        add_theme_support('editor-styles');
        // Enqueue Editor Styles.
        add_editor_style('style-editor.css');

        // Default attachment display settings.
        update_option('image_default_align', 'none');
        update_option('image_default_link_type', 'none');
        update_option('image_default_size', 'large');

        // Custom CSS styles of WorPress gallery.
        add_filter('use_default_gallery_style', '__return_false');
    }
    add_action('after_setup_theme', 'readytek_setup_theme');

    // Disable Block Directory: https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/filters/editor-filters.md#block-directory
    remove_action('enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets');
    remove_action('enqueue_block_editor_assets', 'gutenberg_enqueue_block_editor_assets_block_directory');
}

if (!function_exists('wp_body_open')) {
    /**
     * Fire the wp_body_open action.
     *
     * Added for backwards compatibility to support pre 5.2.0 WordPress versions.
     *
     * @since v2.2
     *
     * @return void
     */
    function wp_body_open()
    {
        do_action('wp_body_open');
    }
}

if (!function_exists('readytek_add_user_fields')) {
    /**
     * Add new User fields to Userprofile:
     * get_user_meta( $user->ID, 'facebook_profile', true );
     *
     * @since v1.0
     *
     * @param array $fields User fields.
     *
     * @return array
     */
    function readytek_add_user_fields($fields)
    {
        // Add new fields.
        $fields['facebook_profile'] = 'Facebook URL';
        $fields['twitter_profile'] = 'Twitter URL';
        $fields['linkedin_profile'] = 'LinkedIn URL';
        $fields['xing_profile'] = 'Xing URL';
        $fields['github_profile'] = 'GitHub URL';

        return $fields;
    }
    add_filter('user_contactmethods', 'readytek_add_user_fields');
}

/**
 * Test if a page is a blog page.
 * if ( is_blog() ) { ... }
 *
 * @since v1.0
 *
 * @return bool
 */
function is_blog()
{
    global $post;
    $posttype = get_post_type($post);

    return ((is_archive() || is_author() || is_category() || is_home() || is_single() || (is_tag() && ('post' === $posttype))) ? true : false);
}

/**
 * Disable comments for Media (Image-Post, Jetpack-Carousel, etc.)
 *
 * @since v1.0
 *
 * @param bool $open    Comments open/closed.
 * @param int  $post_id Post ID.
 *
 * @return bool
 */
function readytek_filter_media_comment_status($open, $post_id = null)
{
    $media_post = get_post($post_id);

    if ('attachment' === $media_post->post_type) {
        return false;
    }

    return $open;
}
add_filter('comments_open', 'readytek_filter_media_comment_status', 10, 2);

/**
 * Style Edit buttons as badges: https://getbootstrap.com/docs/5.0/components/badge
 *
 * @since v1.0
 *
 * @param string $link Post Edit Link.
 *
 * @return string
 */
function readytek_custom_edit_post_link($link)
{
    return str_replace('class="post-edit-link"', 'class="post-edit-link badge bg-secondary"', $link);
}
add_filter('edit_post_link', 'readytek_custom_edit_post_link');

/**
 * Style Edit buttons as badges: https://getbootstrap.com/docs/5.0/components/badge
 *
 * @since v1.0
 *
 * @param string $link Comment Edit Link.
 */
function readytek_custom_edit_comment_link($link)
{
    return str_replace('class="comment-edit-link"', 'class="comment-edit-link badge bg-secondary"', $link);
}
add_filter('edit_comment_link', 'readytek_custom_edit_comment_link');

/**
 * Responsive oEmbed filter: https://getbootstrap.com/docs/5.0/helpers/ratio
 *
 * @since v1.0
 *
 * @param string $html Inner HTML.
 *
 * @return string
 */
function readytek_oembed_filter($html)
{
    return '<div class="ratio ratio-16x9">' . $html . '</div>';
}
add_filter('embed_oembed_html', 'readytek_oembed_filter', 10);

if (!function_exists('readytek_content_nav')) {
    /**
     * Display a navigation to next/previous pages when applicable.
     *
     * @since v1.0
     *
     * @param string $nav_id Navigation ID.
     */
    function readytek_content_nav($nav_id)
    {
        global $wp_query;

        if ($wp_query->max_num_pages > 1) {
            ?>
			<div id="<?php echo esc_attr($nav_id); ?>" class="d-flex mb-4 justify-content-between">
				<div><?php next_posts_link('<span aria-hidden="true">&larr;</span> ' . esc_html__('Older posts', 'readytek'));?></div>
				<div><?php previous_posts_link(esc_html__('Newer posts', 'readytek') . ' <span aria-hidden="true">&rarr;</span>');?></div>
			</div><!-- /.d-flex -->
			<?php
} else {
            echo '<div class="clearfix"></div>';
        }
    }

    /**
     * Add Class.
     *
     * @since v1.0
     *
     * @return string
     */
    function posts_link_attributes()
    {
        return 'class="btn btn-secondary btn-lg"';
    }
    add_filter('next_posts_link_attributes', 'posts_link_attributes');
    add_filter('previous_posts_link_attributes', 'posts_link_attributes');
}

/**
 * Init Widget areas in Sidebar.
 *
 * @since v1.0
 *
 * @return void
 */
function readytek_widgets_init()
{
    // Area 1.
    register_sidebar(
        array(
            'name' => 'Primary Widget Area (Sidebar)',
            'id' => 'primary_widget_area',
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        )
    );

    register_sidebar(
        array(
            'name' => 'Footer 1',
            'id' => 'footer_1',
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        )
    );

    register_sidebar(
        array(
            'name' => 'Footer 2',
            'id' => 'footer_2',
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        )
    );

    register_sidebar(
        array(
            'name' => 'Footer 3',
            'id' => 'footer_3',
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        )
    );

    register_sidebar(
        array(
            'name' => 'Footer Copyright',
            'id' => 'footer_copy',
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '<h3 class="widget-title">',
            'after_title' => '</h3>',
        )
    );
}
add_action('widgets_init', 'readytek_widgets_init');

if (!function_exists('readytek_article_posted_on')) {
    /**
     * "Theme posted on" pattern.
     *
     * @since v1.0
     */
    function readytek_article_posted_on()
    {
        printf(
            wp_kses_post(__('<span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a><span class="by-author"> <span class="sep"> by </span> <span class="author-meta vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'readytek')),
            esc_url(get_the_permalink()),
            esc_attr(get_the_date() . ' - ' . get_the_time()),
            esc_attr(get_the_date('c')),
            esc_html(get_the_date() . ' - ' . get_the_time()),
            esc_url(get_author_posts_url((int) get_the_author_meta('ID'))),
            sprintf(esc_attr__('View all posts by %s', 'readytek'), get_the_author()),
            get_the_author()
        );
    }
}

/**
 * Template for Password protected post form.
 *
 * @since v1.0
 *
 * @return string
 */
function readytek_password_form()
{
    global $post;
    $label = 'pwbox-' . (empty($post->ID) ? rand() : $post->ID);

    $output = '<div class="row">';
    $output .= '<form action="' . esc_url(site_url('wp-login.php?action=postpass', 'login_post')) . '" method="post">';
    $output .= '<h4 class="col-md-12 alert alert-warning">' . esc_html__('This content is password protected. To view it please enter your password below.', 'readytek') . '</h4>';
    $output .= '<div class="col-md-6">';
    $output .= '<div class="input-group">';
    $output .= '<input type="password" name="post_password" id="' . esc_attr($label) . '" placeholder="' . esc_attr__('Password', 'readytek') . '" class="form-control" />';
    $output .= '<div class="input-group-append"><input type="submit" name="submit" class="btn btn-primary" value="' . esc_attr__('Submit', 'readytek') . '" /></div>';
    $output .= '</div><!-- /.input-group -->';
    $output .= '</div><!-- /.col -->';
    $output .= '</form>';
    $output .= '</div><!-- /.row -->';

    return $output;
}
add_filter('the_password_form', 'readytek_password_form');

if (!function_exists('readytek_comment')) {
    /**
     * Style Reply link.
     *
     * @since v1.0
     *
     * @param string $class Link class.
     *
     * @return string
     */
    function readytek_replace_reply_link_class($class)
    {
        return str_replace("class='comment-reply-link", "class='comment-reply-link btn btn-outline-secondary", $class);
    }
    add_filter('comment_reply_link', 'readytek_replace_reply_link_class');

    /**
     * Template for comments and pingbacks:
     * add function to comments.php ... wp_list_comments( array( 'callback' => 'readytek_comment' ) );
     *
     * @since v1.0
     *
     * @param object $comment Comment object.
     * @param array  $args    Comment args.
     * @param int    $depth   Comment depth.
     */
    function readytek_comment($comment, $args, $depth)
    {
        $GLOBALS['comment'] = $comment;
        switch ($comment->comment_type):
    case 'pingback':
    case 'trackback':
        ?>
				<li class="post pingback">
					<p>
						<?php
esc_html_e('Pingback:', 'readytek');
        comment_author_link();
        edit_comment_link(esc_html__('Edit', 'readytek'), '<span class="edit-link">', '</span>');
        ?>
					</p>
				<?php
break;
    default:
        ?>
				<li <?php comment_class();?> id="li-comment-<?php comment_ID();?>">
					<article id="comment-<?php comment_ID();?>" class="comment">
						<footer class="comment-meta">
							<div class="comment-author vcard">
								<?php
$avatar_size = ('0' !== $comment->comment_parent ? 68 : 136);
        echo get_avatar($comment, $avatar_size);

        /* Translators: 1: Comment author, 2: Date and time */
        printf(
            wp_kses_post(__('%1$s, %2$s', 'readytek')),
            sprintf('<span class="fn">%s</span>', get_comment_author_link()),
            sprintf(
                '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
                esc_url(get_comment_link($comment->comment_ID)),
                get_comment_time('c'),
                /* Translators: 1: Date, 2: Time */
                sprintf(esc_html__('%1$s ago', 'readytek'), human_time_diff((int) get_comment_time('U'), current_time('timestamp')))
            )
        );

        edit_comment_link(esc_html__('Edit', 'readytek'), '<span class="edit-link">', '</span>');
        ?>
							</div><!-- .comment-author .vcard -->

							<?php if ('0' === $comment->comment_approved) {?>
								<em class="comment-awaiting-moderation">
									<?php esc_html_e('Your comment is awaiting moderation.', 'readytek');?>
								</em>
								<br />
							<?php }?>
						</footer>

						<div class="comment-content"><?php comment_text();?></div>

						<div class="reply">
							<?php
comment_reply_link(
            array_merge(
                $args,
                array(
                    'reply_text' => esc_html__('Reply', 'readytek') . ' <span>&darr;</span>',
                    'depth' => $depth,
                    'max_depth' => $args['max_depth'],
                )
            )
        );
        ?>
						</div><!-- /.reply -->
					</article><!-- /#comment-## -->
		<?php
break;
        endswitch;
    }

    /**
     * Custom Comment form.
     *
     * @since v1.0
     * @since v1.1: Added 'submit_button' and 'submit_field'
     * @since v2.0.2: Added '$consent' and 'cookies'
     *
     * @param array $args    Form args.
     * @param int   $post_id Post ID.
     *
     * @return array
     */
    function readytek_custom_commentform($args = array(), $post_id = null)
    {
        if (null === $post_id) {
            $post_id = get_the_ID();
        }

        $commenter = wp_get_current_commenter();
        $user = wp_get_current_user();
        $user_identity = $user->exists() ? $user->display_name : '';

        $args = wp_parse_args($args);

        $req = get_option('require_name_email');
        $aria_req = ($req ? " aria-required='true' required" : '');
        $consent = (empty($commenter['comment_author_email']) ? '' : ' checked="checked"');
        $fields = array(
            'author' => '<div class="form-floating mb-3">
							<input type="text" id="author" name="author" class="form-control" value="' . esc_attr($commenter['comment_author']) . '" placeholder="' . esc_html__('Name', 'readytek') . ($req ? '*' : '') . '"' . $aria_req . ' />
							<label for="author">' . esc_html__('Name', 'readytek') . ($req ? '*' : '') . '</label>
						</div>',
            'email' => '<div class="form-floating mb-3">
							<input type="email" id="email" name="email" class="form-control" value="' . esc_attr($commenter['comment_author_email']) . '" placeholder="' . esc_html__('Email', 'readytek') . ($req ? '*' : '') . '"' . $aria_req . ' />
							<label for="email">' . esc_html__('Email', 'readytek') . ($req ? '*' : '') . '</label>
						</div>',
            'url' => '',
            'cookies' => '<p class="form-check mb-3 comment-form-cookies-consent">
							<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" class="form-check-input" type="checkbox" value="yes"' . $consent . ' />
							<label class="form-check-label" for="wp-comment-cookies-consent">' . esc_html__('Save my name, email, and website in this browser for the next time I comment.', 'readytek') . '</label>
						</p>',
        );

        $defaults = array(
            'fields' => apply_filters('comment_form_default_fields', $fields),
            'comment_field' => '<div class="form-floating mb-3">
											<textarea id="comment" name="comment" class="form-control" aria-required="true" required placeholder="' . esc_attr__('Comment', 'readytek') . ($req ? '*' : '') . '"></textarea>
											<label for="comment">' . esc_html__('Comment', 'readytek') . '</label>
										</div>',
            /** This filter is documented in wp-includes/link-template.php */
            'must_log_in' => '<p class="must-log-in">' . sprintf(wp_kses_post(__('You must be <a href="%s">logged in</a> to post a comment.', 'readytek')), wp_login_url(esc_url(get_the_permalink(get_the_ID())))) . '</p>',
            /** This filter is documented in wp-includes/link-template.php */
            'logged_in_as' => '<p class="logged-in-as">' . sprintf(wp_kses_post(__('Logged in as <a href="%1$s">%2$s</a>. <a href="%3$s" title="Log out of this account">Log out?</a>', 'readytek')), get_edit_user_link(), $user->display_name, wp_logout_url(apply_filters('the_permalink', esc_url(get_the_permalink(get_the_ID()))))) . '</p>',
            'comment_notes_before' => '<p class="small comment-notes">' . esc_html__('Your Email address will not be published.', 'readytek') . '</p>',
            'comment_notes_after' => '',
            'id_form' => 'commentform',
            'id_submit' => 'submit',
            'class_submit' => 'btn btn-primary',
            'name_submit' => 'submit',
            'title_reply' => '',
            'title_reply_to' => esc_html__('Leave a Reply to %s', 'readytek'),
            'cancel_reply_link' => esc_html__('Cancel reply', 'readytek'),
            'label_submit' => esc_html__('Post Comment', 'readytek'),
            'submit_button' => '<input type="submit" id="%2$s" name="%1$s" class="%3$s" value="%4$s" />',
            'submit_field' => '<div class="form-submit">%1$s %2$s</div>',
            'format' => 'html5',
        );

        return $defaults;
    }
    add_filter('comment_form_defaults', 'readytek_custom_commentform');
}

if (function_exists('register_nav_menus')) {
    /**
     * Nav menus.
     *
     * @since v1.0
     *
     * @return void
     */
    register_nav_menus(
        array(
            'main-menu' => 'Main Navigation Menu',
            'footer-menu' => 'Footer Menu',
        )
    );
}

// Custom Nav Walker: wp_bootstrap_navwalker().
$custom_walker = __DIR__ . '/inc/wp-bootstrap-navwalker.php';
if (is_readable($custom_walker)) {
    require_once $custom_walker;
}

$custom_walker_footer = __DIR__ . '/inc/wp-bootstrap-navwalker-footer.php';
if (is_readable($custom_walker_footer)) {
    require_once $custom_walker_footer;
}

/**
 * Loading All CSS Stylesheets and Javascript Files.
 *
 * @since v1.0
 *
 * @return void
 */
function readytek_scripts_loader()
{
    $theme_version = wp_get_theme()->get('Version');

    // 1. Styles.
    wp_enqueue_style('style', get_theme_file_uri('style.css'), array(), $theme_version, 'all');
    wp_enqueue_style('main', get_theme_file_uri('assets/dist/main.css'), array(), $theme_version, 'all'); // main.scss: Compiled Framework source + custom styles.
    wp_enqueue_style('owl', get_theme_file_uri('assets/css/owl.carousel.min.css'), array(), $theme_version, 'all');
    wp_enqueue_style('owl-theme', get_theme_file_uri('assets/css/owl.theme.default.min.css'), array(), $theme_version, 'all');
    wp_enqueue_style('custom', get_theme_file_uri('assets/css/custom.css'), array(), $theme_version, 'all');
    wp_enqueue_style('font-awesome', '/wp-content/plugins/js_composer/assets/lib/bower/font-awesome/css/v4-shims.min.css', array(), $theme_version, 'all');
    wp_enqueue_style('data-table', '//cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css', array(), $theme_version, 'all');

    if (is_rtl()) {
        wp_enqueue_style('rtl', get_theme_file_uri('assets/dist/rtl.css'), array(), $theme_version, 'all');
    }

    // 2. Scripts.
    // wp_localize_script( 'mainjs', 'my_ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
    // Option 1: Manually enqueue the wp-util library.
    wp_enqueue_script('wp-util');

    // Option 2: Make wp-util a dependency of your script (usually better).
    // wp_enqueue_script('my-script', 'my-script.js', ['wp-util']);
    wp_enqueue_script('mainjs', get_theme_file_uri('assets/dist/main.bundle.js'), ['wp-util'], $theme_version, true);
    wp_enqueue_script('data-table', '//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js', array(), $theme_version, true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'readytek_scripts_loader');

/**
 * Show cart contents / total Ajax
 */
add_filter('woocommerce_add_to_cart_fragments', 'header_add_to_cart_fragment');

function header_add_to_cart_fragment($fragments)
{
    global $woocommerce;

    ob_start();

    ?>
		<a class="cart-customlocation" href="<?php echo wc_get_cart_url(); ?>"><img src="/wp-content/themes/readytek/assets/images/icon-cart.svg" alt="*"> <span class="count"><?php echo sprintf(_n('%d', '%d', WC()->cart->get_cart_contents_count()), WC()->cart->get_cart_contents_count());?></span></a>
	<?php
$fragments['a.cart-customlocation'] = ob_get_clean();
    return $fragments;
}



function mytheme_add_woocommerce_support()
{
    add_theme_support('woocommerce');
}

add_action('after_setup_theme', 'mytheme_add_woocommerce_support');
add_theme_support('wc-product-gallery-zoom');
add_theme_support('wc-product-gallery-lightbox');
add_theme_support('wc-product-gallery-slider');

// // Display Fields
// add_action('woocommerce_product_options_advanced', 'woocommerce_product_custom_fields');

// function woocommerce_product_custom_fields()
// {
//     global $woocommerce, $post;
//     echo '<div class="product_custom_field">';
//     // Custom Product Text Field
//     woocommerce_wp_text_input(
//         array(
//             'id' => '_custom_product_speed_field',
//             'placeholder' => 'Please enter Speed(FPM)',
//             'label' => __('Speed (FPM)', 'woocommerce'),
//             'desc_tip' => 'true'
//         )
//     );

//     woocommerce_wp_text_input(
//         array(
//             'id' => '_custom_product_temperature_field',
//             'placeholder' => 'Please enter Temperature Range',
//             'label' => __('Temperature Range', 'woocommerce'),
//             'desc_tip' => 'true'
//         )
//     );

//     woocommerce_wp_text_input(
//         array(
//             'id' => '_custom_product_presure_field',
//             'placeholder' => 'Please enter Presure Limit',
//             'label' => __('Presure Limit', 'woocommerce'),
//             'desc_tip' => 'true'
//         )
//     );

//     echo '</div>';

// }

// // Save Fields
// add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');

// function woocommerce_product_custom_fields_save($post_id)
// {
//     // Custom Product Text Field
//     $woocommerce_custom_product_speed_field = $_POST['_custom_product_speed_field'];
//     $woocommerce_custom_product_temperature_field = $_POST['_custom_product_temperature_field'];
//     $woocommerce_custom_product_presure_field = $_POST['_custom_product_presure_field'];
//     if (!empty($woocommerce_custom_product_speed_field))
//         update_post_meta($post_id, '_custom_product_speed_field', esc_attr($woocommerce_custom_product_speed_field));
//     if (!empty($woocommerce_custom_product_temperature_field))
//         update_post_meta($post_id, '_custom_product_temperature_field', esc_attr($woocommerce_custom_product_temperature_field));
//     if (!empty($woocommerce_custom_product_presure_field))
//         update_post_meta($post_id, '_custom_product_presure_field', esc_attr($woocommerce_custom_product_presure_field));
// }

add_filter('woocommerce_product_add_to_cart_text', 'product_cat_add_to_cart_button_text', 20, 1);
function product_cat_add_to_cart_button_text($text)
{
    // Only for a product category archive pages
    if (is_product_category()) {
        $text = __('Add', 'woocommerce');
    }

    return $text;
}
add_filter('woocommerce_account_menu_items', 'payment_methods_link', 40);
function payment_methods_link($menu_links)
{

//     $menu_links = array_slice($menu_links, 0, 5, true)
    //         + array('payment-methods' => 'Payment Methods')
    //         + array_slice($menu_links, 5, NULL, true);

    return $menu_links;
}
// register permalink endpoint
add_action('init', 'payment_methods_add_endpoint');
function payment_methods_add_endpoint()
{

    add_rewrite_endpoint('log-history', EP_PAGES);
}
function wooc_extra_register_fields()
{?>
		<p class="form-row form-row-wide">
			<label for="reg_billing_phone"><?php _e('Phone', 'woocommerce');?></label>
			<input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php esc_attr_e($_POST['billing_phone']);?>" />
		</p>
		<p class="form-row form-row-first">
			<label for="reg_billing_first_name"><?php _e('First name', 'woocommerce');?><span class="required">*</span></label>
			<input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if (!empty($_POST['billing_first_name'])) {
    esc_attr_e($_POST['billing_first_name']);
}
    ?>" />
		</p>
		<p class="form-row form-row-last">
			<label for="reg_billing_last_name"><?php _e('Last name', 'woocommerce');?><span class="required">*</span></label>
			<input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if (!empty($_POST['billing_last_name'])) {
        esc_attr_e($_POST['billing_last_name']);
    }
    ?>" />
		</p>
		<div class="clear"></div>
	<?php
}
add_action('woocommerce_register_form_start', 'wooc_extra_register_fields');
// add_filter( 'widget_text', 'do_shortcode' );

// 1. Add custom field input @ Product Data > Variations > Single Variation

add_action('woocommerce_variation_options_pricing', 'add_seal_jacket_material_to_variations', 10, 3);

function add_seal_jacket_material_to_variations($loop, $variation_data, $variation)
{
    woocommerce_wp_select(array(
        'id' => 'seal_jacket_material[' . $loop . ']',
        'class' => 'select short',
        'label' => __('Select a Seal Jacket Material', 'woocommerce'),
        'options' => array(
            'graphite_fiber_reinforced_ptfe' => 'Graphite Fiber-Reinforced PTFE',
            'polymer_filled_ptfe' => 'Polymer-Filled PTFE',
        ),
        'value' => get_post_meta($variation->ID, 'seal_jacket_material', true),
    ));

    woocommerce_wp_text_input(array(
        'id' => 'seal_length[' . $loop . ']',
        'class' => 'short',
        'label' => __('Seal Length (L)', 'woocommerce'),
        'value' => get_post_meta($variation->ID, 'seal_length', true),
    ));

    woocommerce_wp_text_input(array(
        'id' => 'gland_length[' . $loop . ']',
        'class' => 'short',
        'label' => __('Gland Length (G)', 'woocommerce'),
        'value' => get_post_meta($variation->ID, 'gland_length', true),
    ));

    woocommerce_wp_text_input(array(
        'id' => 'radial_clearance[' . $loop . ']',
        'class' => 'short',
        'label' => __('Radial Clearance @ 70° F (E)', 'woocommerce'),
        'value' => get_post_meta($variation->ID, 'radial_clearance', true),
    ));

    woocommerce_wp_text_input(array(
        'id' => 'mate_chamfer_length[' . $loop . ']',
        'class' => 'short',
        'label' => __('Mate Chamfer Length (J)', 'woocommerce'),
        'value' => get_post_meta($variation->ID, 'mate_chamfer_length', true),
    ));

    woocommerce_wp_text_input(array(
        'id' => 'mount_chamfer_length[' . $loop . ']',
        'class' => 'short',
        'label' => __('Mount Chamfer Length (M)', 'woocommerce'),
        'value' => get_post_meta($variation->ID, 'mount_chamfer_length', true),
    ));

    woocommerce_wp_text_input(array(
        'id' => 'unit_weight_per_item_fe[' . $loop . ']',
        'class' => 'short',
        'label' => __('Unit Weight Per Item Front End', 'woocommerce'),
        'value' => get_post_meta($variation->ID, 'unit_weight_per_item_fe', true),
    ));
}

// -----------------------------------------
// 2. Save custom field on product variation save

add_action('woocommerce_save_product_variation', 'save_seal_jacket_material_variations', 10, 2);

function save_seal_jacket_material_variations($variation_id, $i)
{
    $seal_length = $_POST['seal_length'][$i];
    if (isset($seal_length)) {
        update_post_meta($variation_id, 'seal_length', esc_attr($seal_length));
    }

    $gland_length = $_POST['gland_length'][$i];
    if (isset($gland_length)) {
        update_post_meta($variation_id, 'gland_length', esc_attr($gland_length));
    }

    $radial_clearance = $_POST['radial_clearance'][$i];
    if (isset($radial_clearance)) {
        update_post_meta($variation_id, 'radial_clearance', esc_attr($radial_clearance));
    }

    $mate_chamfer_length = $_POST['mate_chamfer_length'][$i];
    if (isset($mate_chamfer_length)) {
        update_post_meta($variation_id, 'mate_chamfer_length', esc_attr($mate_chamfer_length));
    }

    $mount_chamfer_length = $_POST['mount_chamfer_length'][$i];
    if (isset($mount_chamfer_length)) {
        update_post_meta($variation_id, 'mount_chamfer_length', esc_attr($mount_chamfer_length));
    }

    $seal_jacket_material = $_POST['seal_jacket_material'][$i];
    if (isset($seal_jacket_material)) {
        update_post_meta($variation_id, 'seal_jacket_material', esc_attr($seal_jacket_material));
    }

    $unit_weight_per_item_fe = $_POST['unit_weight_per_item_fe'][$i];
    if (isset($unit_weight_per_item_fe)) {
        update_post_meta($variation_id, 'unit_weight_per_item_fe', esc_attr($unit_weight_per_item_fe));
    }

}

// -----------------------------------------
// 3. Store custom field value into variation data

add_filter('woocommerce_available_variation', 'add_seal_jacket_material_variation_data');

function add_seal_jacket_material_variation_data($variations)
{
    $variations['seal_jacket_material'] = '<div class="woocommerce_seal_jacket_material">Seal Jacket Material: <span>' . get_post_meta($variations['variation_id'], 'seal_jacket_material', true) . '</span></div>';
    $variations['seal_length'] = '<div class="woocommerce_seal_length">Seal Length (L): <span>' . get_post_meta($variations['variation_id'], 'seal_length', true) . '</span></div>';
    $variations['gland_length'] = '<div class="woocommerce_gland_length">Gland Length (G): <span>' . get_post_meta($variations['variation_id'], 'gland_length', true) . '</span></div>';
    $variations['radial_clearance'] = '<div class="woocommerce_radial_clearance">Radial Clearance @ 70° F (E): <span>' . get_post_meta($variations['variation_id'], 'radial_clearance', true) . '</span></div>';
    $variations['mate_chamfer_length'] = '<div class="woocommerce_mate_chamfer_length">Mate Chamfer Length (J): <span>' . get_post_meta($variations['variation_id'], 'mate_chamfer_length', true) . '</span></div>';
    $variations['mount_chamfer_length'] = '<div class="woocommerce_mount_chamfer_length">Mount Chamfer Length (M): <span>' . get_post_meta($variations['variation_id'], 'mount_chamfer_length', true) . '</span></div>';
    $variations['unit_weight_per_item_fe'] = '<div class="woocommerce_unit_weight_per_item_fe">Unit Weight Per Item: <span>' . get_post_meta($variations['variation_id'], 'unit_weight_per_item_fe', true) . '</span></div>';
    return $variations;
}

add_action('wp_ajax_nopriv_get_variation_product_by_category', 'get_variation_product_by_category');
add_action('wp_ajax_get_variation_product_by_category', 'get_variation_product_by_category');

function get_variation_product_by_category()
{
    $products = get_posts(array(
        'post_type' => 'product',
        'numberposts' => -1,
        'post_status' => 'publish',
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $_POST['category'], /*category name*/
                'operator' => 'IN',
            ),
        ),
    ));
    $html = '';
    if ($products) {
        foreach ($products as $key => $product) {
            $productData = wc_get_product($product->ID);
            if (empty($productData) || !$productData->is_visible()) {
                return;
            }

            $variations = $productData->get_children();
            if ($variations):
                foreach ($variations as $value):
                    $single_variation = new WC_Product_Variation($value);
                    $variant_id = $single_variation->get_id();
                    $btn_format = apply_filters('pvtfw_row_cart_btn_is', sprintf(
                        '<button data-product-id="%s" data-url="%s" data-product="%s" data-variant="%s" class="pvtfw_variant_table_cart_btn button alt">
																											<span class="pvtfw-btn-text">%s</span>
																											<div class="spinner-wrap"><span class="flaticon-spinner-of-dots"></span></div>
																											</button>',
                        $productData->get_ID(),
                        home_url(),
                        get_permalink($productData->get_ID()),
                        $variant_id,
                        sprintf(_x('%s', 'Button Text: Add To Cart', 'product-variant-table-for-woocommerce'), 'Add')
                    ), $productData->get_ID(), home_url(), get_permalink($productData->get_ID()), $variant_id, 'Add');
                    $qty = '<div class="pvtfw-quantity"><select id="' . $variant_id . '" class="input-text qty text"  name="quantity"><option value="10">10</option><option value="20">20</option><option value="30">30</option><option value="40">30+</option></select></div>';
                    $seal_jacket_material = get_post_meta($variant_id, 'seal_jacket_material', true);
                    if ($seal_jacket_material == $_POST['selected_matrerial']):
                        $pa_cross_section = $single_variation->get_attribute('pa_seal-cross-section');
                        $pa_inner_diameter = $single_variation->get_attribute('pa_constrained-inside-diameter');
                        $pa_seal_material = $single_variation->get_attribute('pa_constrained-outside-diameter');
                        //$skuNew = $single_variation->get_sku();
                        $btn_short_code = do_shortcode('[add_to_cart style="border:none;" id="' . $variant_id . '" show_price="false" quantity="10"]');
                        $productData_link = $productData->get_permalink() . '?variation_id=' . $variant_id . '&attribute_pa_seal-cross-section=' . $pa_cross_section . '&attribute_pa_constrained-inside-diameter=' . $pa_inner_diameter . '&attribute_pa_constrained-outside-diameter=' . $pa_seal_material;
                        $html .= '<tr>
																																		<td><a href="' . $productData_link . '">' . $single_variation->get_sku() . '</a></td>
																																		<td>' . $pa_inner_diameter . '</td>
																																		<td>' . $pa_cross_section . '</td>
																																		<td>' . $pa_seal_material . '</td>
																																		<td>' . get_post_meta($productData->get_ID(), "maximum_surface_speed", true) . '</td>
																																		<td>' . get_post_meta($productData->get_ID(), "max_pressure", true) . '</td>
																																		<td>' . get_post_meta($productData->get_ID(), "temperature_range", true) . '</td>
																																		<td><input type="hidden" class="single-product-price" value="' . $single_variation->get_price() . '" /> ' . wc_price($single_variation->get_price() * 10) . '</td>
																																		<td><div class="qty-add-to-cart-container"> ' . $qty . ' ' . str_replace("Add to cart", "Add", $btn_short_code) . '</div></td>
																																	</tr>';
                    endif;
                endforeach;
            endif;
        }
    }
    wp_send_json_success($html);
}

add_filter('woocommerce_update_cart_action_cart_updated', 'on_action_cart_updated', 20, 1);
function on_action_cart_updated($cart_updated)
{

    // if ($cart_updated) {
    $cart_content = WC()->cart->get_cart_contents();
    $update_cart = false;
    $cart_totals = isset($_POST['cart']) ? wp_unslash($_POST['cart']) : '';
    //$variable_product = wc_get_product( absint( $_POST['product_id'] ) );
    if (!empty($cart_content) && is_array($cart_totals)) {

        foreach ($cart_content as $key => $item) {
            $cross_section = $cart_totals[$key]['attribute_pa_seal-cross-section'];
            $inner_diameter = $cart_totals[$key]['attribute_pa_constrained-inside-diameter'];
            $seal_material = $cart_totals[$key]['attribute_pa_constrained-outside-diameter'];
            $item_quantity = $cart_totals[$key]['qty'];

            if (!empty($cross_section)) {
                $cart_content[$key]['variation']['attribute_pa_seal-cross-section'] = $cross_section;
                $update_cart = true;
            }
            if (!empty($inner_diameter)) {
                $cart_content[$key]['variation']['attribute_pa_constrained-inside-diameter'] = $inner_diameter;
                $update_cart = true;
            }
            if (!empty($seal_material)) {
                $cart_content[$key]['variation']['attribute_pa_constrained-outside-diameter'] = $seal_material;
                $update_cart = true;
            }
            $variable_product = wc_get_product(absint($cart_content[$key]['product_id']));
            $data_store = WC_Data_Store::load('product');
            $varray = [
                'attribute_pa_seal-cross-section' => $cross_section,
                'attribute_pa_constrained-inside-diameter' => $inner_diameter,
                'attribute_pa_constrained-outside-diameter' => $seal_material,
            ];
            $variation_id = $data_store->find_matching_product_variation($variable_product, wp_unslash($varray));
            $cart_content[$key]['variation_id'] = $variation_id;
            WC()->cart->remove_cart_item($key);
            WC()->cart->add_to_cart(absint($cart_content[$key]['product_id']), $item_quantity, $variation_id);
        }

        // if ($update_cart) {
        //     WC()->cart->set_cart_contents($cart_content);
        // }
    }
    // }
}

/** to change the position of excerpt **/
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);
add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 20);

add_action('woocommerce_variable_add_to_cart', 'balsel_update_price_with_variation_price');

function balsel_update_price_with_variation_price()
{
    global $product;
    $price = $product->get_price_html();
    wc_enqueue_js("
      $(document).on('found_variation', 'form.cart', function( event, variation ) {
         if(variation.price_html) $('.summary > p.price').html(variation.price_html);
         $('.woocommerce-variation-price').hide();
		 var singleVPrice = $('.woocommerce-variation-price').find('.price').text().match(/[0-9.]+/g);
		 var newSingleVPrice = (singleVPrice * 10).toFixed(2);
         $('.summary').find('p.price').html(variation.price_html.replace(singleVPrice, newSingleVPrice));
		 $('.summary').find('.unit-price-single').html(variation.price_html);
      });
   ");
};

// checkout thumbnail
function isa_woo_cart_attributes($cart_item, $cart_item_key)
{
    global $product;
    if (is_cart()) {
        echo "<style>#checkout_thumbnail{display:none;}</style>";
    }
    $item_data = $cart_item_key['data'];
    $post = get_post($item_data->id);
    $thumb = get_the_post_thumbnail($item_data->id, array(32, 50));
    echo '<div id="checkout_thumbnail">' . $thumb . '</div> ' . $post->post_title;
}
add_filter('woocommerce_cart_item_name', 'isa_woo_cart_attributes', 10, 2);

// adding custome field in checkout form
add_filter('woocommerce_checkout_fields', 'woocommerce_checkout_field_editor');
// Our hooked in function - $fields is passed via the filter!
function woocommerce_checkout_field_editor($fields)
{
    $fields['billing']['billing_middle_name'] = array(
        'label' => __('Middle Name', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => false,
    );
    $fields['shipping']['shipping_middle_name'] = array(
        'label' => __('Middle Name', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => false,
    );
    $fields['billing']['billing_areacode'] = array(
        'label' => __('Area Code', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => true,
    );
    $fields['shipping']['shipping_areacode'] = array(
        'label' => __('Area Code', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => true,
    );
    $fields['shipping']['shipping_email'] = array(
        'label' => __('Email Address', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => true,
    );
    $fields['shipping']['shipping_phone'] = array(
        'label' => __('Phone', 'woocommerce'),
        'placeholder' => _x('', 'placeholder', 'woocommerce'),
        'required' => true,
    );
    return $fields;
};

// changing position of field
add_filter('woocommerce_checkout_fields', 'checkout_fields_reorder');

function checkout_fields_reorder($checkout_fields)
{
    $checkout_fields['billing']['billing_first_name']['priority'] = 10;
    $checkout_fields['billing']['billing_last_name']['priority'] = 20;
    $checkout_fields['billing']['billing_middle_name']['priority'] = 30;
    $checkout_fields['billing']['billing_email']['priority'] = 40;
    $checkout_fields['billing']['billing_company'] = array(
        'priority' => 50,
        'required' => true,
    );
    $checkout_fields['billing']['billing_areacode']['priority'] = 60;
    $checkout_fields['billing']['billing_phone']['priority'] = 70;
    $checkout_fields['billing']['billing_address_1']['priority'] = 80;
    $checkout_fields['billing']['billing_city']['priority'] = 90;
    $checkout_fields['billing']['billing_state']['priority'] = 100;
    $checkout_fields['billing']['billing_postcode']['priority'] = 110;
    $checkout_fields['billing']['billing_country']['priority'] = 120;
    $checkout_fields['shipping']['shipping_first_name']['priority'] = 10;
    $checkout_fields['shipping']['shipping_last_name']['priority'] = 20;
    $checkout_fields['shipping']['shipping_middle_name']['priority'] = 30;
    $checkout_fields['shipping']['shipping_company'] = array(
        'priority' => 40,
        'required' => true,
    );
    $checkout_fields['shipping']['shipping_email']['priority'] = 50;
    $checkout_fields['shipping']['shipping_areacode']['priority'] = 60;
    $checkout_fields['shipping']['shipping_phone']['priority'] = 70;
    $checkout_fields['shipping']['shipping_address_1']['priority'] = 80;
    $checkout_fields['shipping']['shipping_city']['priority'] = 90;
    $checkout_fields['shipping']['shipping_state']['priority'] = 100;
    $checkout_fields['shipping']['shipping_country']['priority'] = 110;
    $checkout_fields['shipping']['shipping_postcode']['priority'] = 120;
    return $checkout_fields;
};

// stylling-checkout-form
add_filter('woocommerce_checkout_fields', 'checkout_fields_styling', 999999);

function checkout_fields_styling($checkout_fields)
{
    // echo "<pre>"; print_r($checkout_fields); echo "</pre>";
    $checkout_fields['billing']['billing_address_2']['placeholder'] = 'Ex. 1234 Main St.';
    $checkout_fields['billing']['billing_address_2']['label'] = 'Ex. 1234 Main St.';
    $checkout_fields['billing']['billing_company']['label'] = 'Company';
    $checkout_fields['billing']['billing_company']['placeholder'] = 'Company';
    $checkout_fields['shipping']['shipping_company']['label'] = 'Company';
    $checkout_fields['shipping']['shipping_company']['placeholder'] = 'Company';
    $checkout_fields['billing']['billing_postcode']['class'][0] = 'form-row-first';
    $checkout_fields['billing']['billing_phone']['class'][0] = 'form-row-last';
    $checkout_fields['shipping']['shipping_middle_name']['class'][0] = 'form-row-last';
    $checkout_fields['billing']['billing_middle_name']['class'][0] = 'form-row-last';
    $checkout_fields['billing']['billing_areacode']['class'][0] = 'form-row-first';
    $checkout_fields['billing']['billing_email']['class'][0] = 'form-row-last';
    $checkout_fields['billing']['billing_country']['class'][0] = 'form-row-last';
    $checkout_fields['billing']['billing_postcode']['class'][0] = 'form-row-first';
    $checkout_fields['billing']['billing_city']['class'][0] = 'form-row-first';
    $checkout_fields['billing']['billing_state']['class'][0] = 'form-row-last';
    return $checkout_fields;
};

add_filter('woocommerce_default_address_fields', 'custom_override_default_checkout_fields', 10, 1);
function custom_override_default_checkout_fields($address_fields)
{
    $address_fields['address_1']['placeholder'] = __('Ex. 1234 Main St.', 'woocommerce');
    $address_fields['address_2']['placeholder'] = __('Suite, unit, etc. (optional)', 'woocommerce');
    $address_fields['areacode']['label'] = __('Area Code', 'woocommerce');
    $address_fields['company']['label'] = __('Company', 'woocommerce');
    return $address_fields;
}
// Remove them from under short description
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);

// Readd above product tabs
add_action('woocommerce_after_single_product_summary', 'woocommerce_template_single_sharing', 5);
// add_action('woocommerce_new_order', 'create_flat_file_wc_order',  1, 1);
// add_action('woocommerce_checkout_order_processed', 'create_flat_file_wc_order',  10, 3); latest hook
// function create_flat_file_wc_order($order_id)
//function create_flat_file_wc_order($order_id, $posted_data, $order) hook function

function create_flat_file_wc_order($order_id, $order)
{
    date_default_timezone_set('America/Los_Angeles');
    // get order details data...
    // $order = new WC_Order($order_id);
    $order_status = $order->get_status();
    if (!in_array(strtolower($order_status), ['pending payment', 'pending'])) {
        $items = $order->get_items(apply_filters('woocommerce_purchase_order_item_types', 'line_item'));

        $order_data = $order->get_data();

        $user_status = get_user_meta($order_data['customer_id'], 'ur_user_status', true);
        $user_status = ($user_status == 0) ? 'Unverified' : (($user_status == 1) ? 'Verified' : 'Rejected');

        // Get the Customer billing email
        $billing_email = $order->get_billing_email();

        // Get the Customer billing phone
        $billing_phone = $order->get_billing_phone();
        $billing_areacode = get_post_meta($order_id, '_billing_areacode', true);

        // Customer billing information details
        $billing_first_name = $order->get_billing_first_name();
        $billing_middle_name = get_post_meta($order_id, '_billing_middle_name', true);
        $billing_last_name = $order->get_billing_last_name();
        $billing_company = $order->get_billing_company();
        $billing_address_1 = $order->get_billing_address_1();
        $billing_address_2 = $order->get_billing_address_2();
        $billing_city = $order->get_billing_city();
        $billing_state = $order->get_billing_state();
        $billing_postcode = $order->get_billing_postcode();
        $billing_country = $order->get_billing_country();

        // Customer shipping information details
        $shipping_first_name = $order->get_shipping_first_name();
        $shipping_middle_name = get_post_meta($order_id, '_shipping_middle_name', true);
        $shipping_last_name = $order->get_shipping_last_name();
        $shipping_company = $order->get_shipping_company();
        $shipping_address_1 = $order->get_shipping_address_1();
        $shipping_address_2 = $order->get_shipping_address_2();
        $shipping_city = $order->get_shipping_city();
        $shipping_state = $order->get_shipping_state();
        $shipping_postcode = $order->get_shipping_postcode();
        $shipping_country = $order->get_shipping_country();

        $orderDateTime = $order->get_date_created();
        $orderDate = date('Y-m-d', strtotime($orderDateTime));
        $orderTime = date('H:i:s', strtotime($orderDateTime));

        $orderTotal = $order->get_total();
        $orderTax = $order->get_total_tax();
        $currency = $order->get_currency();
        //$weight_unit = get_option('woocommerce_weight_unit');
        $weight_unit = 'EA';
        $item_quantity = '';
        $item_sku = '';
        $unitPrice = '';
        // Iterating through each "line" items in the order
        foreach ($items as $item_id => $item) {

            $single_variation = new WC_Product_Variation($item->get_variation_id());
            // $unitPrice .= $single_variation->get_price();
            $unitPrice .= ($unitPrice == '') ? $single_variation->get_price() : ' - ' . $single_variation->get_price();
            // Get an instance of corresponding the WC_Product object
            // $product        = $item->get_product();

            // $active_price   = $product->get_price(); // The product active raw price

            // $regular_price  = $product->get_sale_price(); // The product raw sale price

            // $sale_price     = $product->get_regular_price(); // The product raw regular price

            // $product_name   = $item->get_name(); // Get the item name (product name)
            // $sku = get_post_meta( $item->get_product(), '_sku', true );
            $item_quantity .= ($item_quantity == '') ? $item->get_quantity() : ' - ' . $item->get_quantity(); // Get the item quantity
            $item_sku .= ($item_sku == '') ? $single_variation->get_sku() : ' - ' . $single_variation->get_sku(); // Get the item quantity

            // $item_subtotal  = $item->get_subtotal(); // Get the item line total non discounted

            // $item_subto_tax = $item->get_subtotal_tax(); // Get the item line total tax non discounted

            // $item_total     = $item->get_total(); // Get the item line total discounted

            // $item_total_tax = $item->get_total_tax(); // Get the item line total  tax discounted

            // $item_taxes     = $item->get_taxes(); // Get the item taxes array

            // $item_tax_class = $item->get_tax_class(); // Get the item tax class

            // $item_tax_status= $item->get_tax_status(); // Get the item tax status

            // $item_downloads = $item->get_item_downloads(); // Get the item downloads

            // Displaying this data (to check)
            // echo 'Product name: '.$product_name.' | Quantity: '.$item_quantity.' | Item total: '. number_format( $item_total, 2 );
        }

        //     $list[0] = array("First Name","Middle Name","Last Name","Email Address","Area Code","Phone Number","Company Name","Line Number","Item number","Quantity","Unit Price","Unit of Measure","Shipping Address 1","Shipping Address 2","ShippingCity","ShippingState","Shipping Zip Code","Shipping Country","Billing Address 1","Billing Address 2","Billing City","Billing State","Billing Zip Code","Billing Country","Shipping Carrier","Order's Freight Total Amount","Order's Tax Total  Amount", "Order's Total Amount", "Order Date","Order Time","Credit Card Payment ID", "Web Order ID","Currency","Compliance Status"
        // );
        $str = "First Name|Middle Name|Last Name|Email Address|Area Code|Phone Number|Company Name|Line Number|Item number|Quantity|Unit Price|Unit of Measure|Shipping Address 1|Shipping Address 2|ShippingCity|ShippingState|Shipping Zip Code|Shipping Country|Billing Address 1|Billing Address 2|Billing City|Billing State|Billing Zip Code|Billing Country|Shipping Carrier|Order's Freight Total Amount|Order's Tax Total  Amount|Order's Total Amount|Order Date|Order Time|Credit Card Payment ID|Web Order ID|Currency|Compliance Status\n";
        //$list[0] = explode(",",$str);
        // $list[1] = array(
        //     $billing_first_name, $billing_middle_name, $billing_last_name, $billing_email, $billing_areacode, $billing_phone, $billing_company,
        //     '1', 'E19720', $item_quantity, '50', $weight_unit, $shipping_address_1, $shipping_address_2, $shipping_city, $shipping_state, $shipping_postcode, $shipping_country, $billing_address_1, $billing_address_2, $billing_city, $billing_state, $billing_postcode, $billing_country, 'Fedex', $orderTotal, $orderTax, $orderTotal, $orderDate, $orderTime, '', $order_id, $currency, $user_status
        // );
        $lineNumber = 1;
        $sku = $item_sku;
        foreach ($order->get_items('shipping') as $item_id => $item) {
            // Get the data in an unprotected array
            $item_data = $item->get_data();

            $shipping_data_id = $item_data['id'];
            $shipping_data_order_id = $item_data['order_id'];
            $shipping_data_name = $item_data['name'];
            $shipping_data_method_title = $item_data['method_title'];
            $shipping_data_method_id = $item_data['method_id'];
            $shipping_data_instance_id = $item_data['instance_id'];
            $shipping_data_total = $item_data['total'];
            $shipping_data_total_tax = $item_data['total_tax'];
            $shipping_data_taxes = $item_data['taxes'];
        }

        $shippingMethod = $shipping_data_method_title;
        $shipping_total = $order->get_shipping_total();
        $paymentId = $order->get_payment_method_title();
        $explodeSku = explode(' - ', $item_sku);
        $explodeUnitPrice = explode(' - ', $unitPrice);
        $explodeItemQuantity = explode(' - ', $item_quantity);
        // $data = $billing_first_name . "|" . $billing_middle_name . "|" . $billing_last_name . "|" . $billing_email . "|" . $billing_areacode . "|" . $billing_phone . "|" . $billing_company . "|" . $lineNumber . "|" . $sku . "|" . $item_quantity . "|" . $unitPrice . "|" . $weight_unit . "|" . $shipping_address_1 . "|" . $shipping_address_2 . "|" . $shipping_city . "|" . $shipping_state . "|" . $shipping_postcode . "|" . $shipping_country . "|" . $billing_address_1 . "|" . $billing_address_2 . "|" . $billing_city . "|" . $billing_state . "|" . $billing_postcode . "|" . $billing_country . "|" . $shippingMethod . "|" . $shipping_total . "|" . $orderTax . "|" . $orderTotal . "|" . $orderDate . "|" . $orderTime . "|" . $paymentId . "|" . $order_id . "|" . $currency . "|" . $user_status . "\n";

        // $file = fopen(wp_upload_dir()['basedir'] . '/orders-'.date('Y-m-d', strtotime($orderDateTime)).'-'.date('H-i-s', strtotime($orderDateTime)).'.csv', 'a');  // 'a' for append to file - created if doesn't exit
        //$file = fopen(wp_upload_dir()['basedir'] . '/orders-' . date('Y-m-d', strtotime($orderDateTime)) . '-' . date('H-i-s', strtotime($orderDateTime)) . '.txt', 'a');  // 'a' for append to file - created if doesn't exit
        $is_file_exist = file_exists(wp_upload_dir()['basedir'] . '/orders-' . date('Y-m-d', strtotime($orderDateTime)) . '.txt');
        $file = fopen(wp_upload_dir()['basedir'] . '/orders-' . date('Y-m-d', strtotime($orderDateTime)) . '.txt', 'a'); // 'a' for append to file - created if doesn't exit

        if (!$is_file_exist) {
            fwrite($file, $str);
        }

        foreach ($explodeSku as $key => $newData) {
            $data = $billing_first_name . "|" . $billing_middle_name . "|" . $billing_last_name . "|" . $billing_email . "|" . $billing_areacode . "|" . $billing_phone . "|" . $billing_company . "|" . ($key + 1) . "|" . $newData . "|" . $explodeItemQuantity[$key] . "|" . $explodeUnitPrice[$key] . "|" . $weight_unit . "|" . $shipping_address_1 . "|" . $shipping_address_2 . "|" . $shipping_city . "|" . $shipping_state . "|" . $shipping_postcode . "|" . $shipping_country . "|" . $billing_address_1 . "|" . $billing_address_2 . "|" . $billing_city . "|" . $billing_state . "|" . $billing_postcode . "|" . $billing_country . "|" . $shippingMethod . "|" . $shipping_total . "|" . $orderTax . "|" . $orderTotal . "|" . $orderDate . "|" . $orderTime . "|" . $paymentId . "|" . $order_id . "|" . $currency . "|" . $user_status . "\n";
            fwrite($file, $data);
        }

        // fwrite($file, $data);
        // foreach ($list as $line)
        // {
        //     fputcsv($file, $line, '|');
        // }
        fclose($file);

        // $ftp_server = "45.33.89.171";
        // $server_port = 22;
        // $serverUser = "root";
        // $serverPassword = "E&{X4d.+FB(C";
        // // $fileName = 'orders-' . date('Y-m-d', strtotime($orderDateTime)) . '-' . date('H-i-s', strtotime($orderDateTime)) . '.txt';
        // $fileName = 'orders-' . date('Y-m-d', strtotime($orderDateTime)) . '.txt';
        // $remote_file = "/var/www/html/readytek.balseal.com/E-Commerce/Test/" . $fileName;
        // $pathLocalFile = wp_upload_dir()['basedir'] . '/' . $fileName;

        // try {
        //     $ch = curl_init('sftp://' . $ftp_server . ':' . $server_port . $remote_file);
        //     $fh = fopen($pathLocalFile, 'r');
        //     if ($fh) {
        //         curl_setopt($ch, CURLOPT_USERPWD, $serverUser . ':' . $serverPassword);
        //         curl_setopt($ch, CURLOPT_UPLOAD, true);
        //         curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);
        //         curl_setopt($ch, CURLOPT_INFILE, $fh);
        //         curl_setopt($ch, CURLOPT_INFILESIZE, filesize($pathLocalFile));
        //         curl_setopt($ch, CURLOPT_VERBOSE, true);
        //         $verbose = fopen('php://temp', 'w+');
        //         curl_setopt($ch, CURLOPT_STDERR, $verbose);
        //         $response = curl_exec($ch);
        //         $error = curl_error($ch);
        //         curl_close($ch);
        //         if ($response) {
        //             //echo "Success";
        //         } else {
        //             //echo "Failure";
        //             rewind($verbose);
        //             $verboseLog = stream_get_contents($verbose);
        //             //echo "Verbose information:\n" . $verboseLog . "\n";
        //         }
        //     }
        // } catch (Exception $e) {
        //     //echo "error exception".$e->getMessage();
        // }
    }
};
// add_action( 'woocommerce_after_single_product_summary' , 'bbloomer_add_below_prod_gallery', 5 );

// function bbloomer_add_below_prod_gallery() {
//    echo '<div class="elfsight-app-d6d3a7e0-7c03-4952-974d-b9e705f60f7b"></div>';
// };

/**
 * @snippet       Alter Cart Counter @ WooCommerce Cart Widget
 * @author        Kamran Shah
 */

// add_filter('woocommerce_cart_contents_count', 'alter_cart_contents_count', 9999, 1);

// function alter_cart_contents_count($count)
// {
//     $count = $count / 10;
//     return $count;
// }

/**
 * Remove the order field from checkout.
 */
function remove_checkout_note_field($fields)
{
    unset($fields['order']['order_comments']);
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'remove_checkout_note_field');

//enabled fedex sandbox mode
add_filter('flexible_shipping_fedex_testing', '__return_true');

add_action('user_register', 'vq_registration_save', 101, 1);

function vq_registration_save($user_id)
{
    // Get the user object.
    $user = get_userdata($user_id);

    // Get all the user roles as an array.
    $user_roles = $user->roles;

    if (in_array('customer', $user_roles, true) || in_array('subscriber', $user_roles, true)) {
        update_user_meta($user_id, 'ur_user_status', 0);
//         wp_set_current_user($user_id);
        //         wp_set_auth_cookie($user_id);
        //         wp_redirect( home_url('/my-account') ); // You can change home_url() to the specific URL,such as "wp_redirect( 'http://www.wpcoke.com' )";
        //         exit();
    }

};

add_filter('user_registration_after_register_user_action', 'vq_after_registration_user', 102, 3);
function vq_after_registration_user($form_data, $form_id, $user_id)
{
    // Get the user object.
    $user = get_userdata($user_id);

    // Get all the user roles as an array.
    $user_roles = $user->roles;
    if (in_array('customer', $user_roles, true) || in_array('subscriber', $user_roles, true)) {
        update_user_meta($user_id, 'ur_user_status', 0);
//         wp_set_current_user($user_id);
        //         wp_set_auth_cookie($user_id);
        //         wp_redirect( home_url('/my-account') ); // You can change home_url() to the specific URL,such as "wp_redirect( 'http://www.wpcoke.com' )";
        //         exit();
    }

}

add_filter('woocommerce_return_to_shop_text', 'prefix_store_button');
/**
 * Change 'Return to Shop' text on button
 */
function prefix_store_button()
{
    $store_button = "Return to products"; // Change text as required

    return $store_button;
}

function update_stock_api(WP_REST_Request $request)
{
    $sku = $request->get_param('item-number');
    $qty = $request->get_param('quantity');
    if (!empty($sku) && !empty($qty)) {

        $product_id = wc_get_product_id_by_sku($sku);
        if ($product_id) {
            $product = wc_get_product($product_id);
            $parent_product_id = $product->get_parent_id();
            // Make changes to stock quantity and save
            $product->set_manage_stock(true);
            $product->set_stock_quantity($qty);
            $product->save();
            wp_send_json_success(['message' => 'Item quantity has been updated successfully.'], 200);
        } else {
            return new WP_Error(
                'no_content',
                __('Sorry, No item found with the requested item number.'),
                array('status' => 204)
            );
        }
    }
    return new WP_Error(
        'bad_request',
        __('Sorry, item-number or quantity should not be empty.'),
        array('status' => 400)
    );
}

add_action('rest_api_init', function () {
    register_rest_route('wc/v3', 'add-stock', array(
        'methods' => 'PUT', // array( 'GET', 'POST', 'PUT', )
        'callback' => 'update_stock_api',
        //'permission_callback' => 'check_access'
    ));
    register_rest_route('wc/v3', 'update-order-info', array(
        'methods' => 'PUT', // array( 'GET', 'POST', 'PUT', )
        'callback' => 'update_order_info_api',
        //'permission_callback' => 'check_access'
    ));
});

function update_order_info_api(WP_REST_Request $request)
{
    $orderId = $request->get_param('web-order-id');
    $E1OrderNumber = $request->get_param('e1-order-number');
    $trackingNumber = $request->get_param('freight-tracking-number');
    $orderStatus = $request->get_param('status');
    $note = '';

    if (!empty($orderId)) {

        $order = wc_get_order($orderId);
        if ($order) {
            if (!empty($E1OrderNumber)) {
                $note .= "E1 Order Number: " . $E1OrderNumber . "\n";
            }

            if (!empty($trackingNumber)) {
                $note .= "Freight Tracking Number: " . $trackingNumber . "\n";
            }

            if (!empty($note)) {
                // Add the note
                $order->add_order_note($note);
            }

            if (!empty($orderStatus)) {
                $order->update_status(strtolower($orderStatus));
            }
            wp_send_json_success(['message' => 'Order has been updated successfully.'], 200);
        } else {
            return new WP_Error(
                'no_content',
                __('Sorry, No item found with the requested order number.'),
                array('status' => 204)
            );
        }
    }
    return new WP_Error(
        'bad_request',
        __('Sorry, Web order ID should not be empty.'),
        array('status' => 400)
    );
}

function register_shipped_order_status()
{
    register_post_status('wc-shipped', array(
        'label' => 'Shipped',
        'public' => true,
        'show_in_admin_status_list' => true,
        'show_in_admin_all_list' => true,
        'exclude_from_search' => false,
        'label_count' => _n_noop('Shipped <span class="count">(%s)</span>', 'Shipped <span class="count">(%s)</span>'),
    ));
}
add_action('init', 'register_shipped_order_status');

function add_shipped_status_to_order_statuses($order_statuses)
{
    $new_order_statuses = array();
    foreach ($order_statuses as $key => $status) {
        $new_order_statuses[$key] = $status;
        if ('wc-processing' === $key) {
            $new_order_statuses['wc-shipped'] = 'Shipped';
        }
    }
    return $new_order_statuses;
}
add_filter('wc_order_statuses', 'add_shipped_status_to_order_statuses');

//ajax for filter variation dropdowns cart page
add_action('wp_ajax_nopriv_get_variation_available_options', 'get_variation_available_options');
add_action('wp_ajax_get_variation_available_options', 'get_variation_available_options');

function get_variation_available_options()
{
    $parentProductId = $_POST['product_id'];
    $attributeName = $_POST['variation_name'];
    $variationValue = $_POST['variation_value'];

    $availableOptions = get_posts(array(
        // 'post_parent' => absint($_product->get_parent_id()),
        'post_parent' => absint($parentProductId),
        'post_status' => 'publish',
        'post_type' => 'product_variation',
        'posts_per_page' => -1,
        'meta_query' => array(array(
            // 'key' => 'attribute_pa_constrained-outside-diameter',
            // 'value' => '0-3125-in',
            'key' => $attributeName,
            'value' => $variationValue,
        )),
    ));
    $html = '';
    if ($availableOptions) {
        foreach ($availableOptions as $key => $value) {
            if ($attributeName == 'attribute_pa_constrained-inside-diameter') {
                $availableAttrValue = get_post_meta($value->ID, 'attribute_pa_constrained-outside-diameter', true);
                $availableAttrLabel = preg_replace('/-/', '.', $availableAttrValue, 1);
                $availableAttrLabel = preg_replace('/-/', ' ', $availableAttrLabel, 1);
            } elseif ($attributeName == 'attribute_pa_constrained-outside-diameter') {
                $availableAttrValue = get_post_meta($value->ID, 'attribute_pa_seal-cross-section', true);
                $availableAttrLabel = preg_replace('/-/', '.', $availableAttrValue, 1);
                $availableAttrLabel = preg_replace('/-/', ' ', $availableAttrLabel, 1);
            }
            // elseif ($attributeName = 'attribute_pa_seal-cross-section') {
            //     $availableAttrValue = get_post_meta($value->ID, 'attribute_pa_seal-cross-section', true);
            //     $availableAttrValue =get_post_meta($value->ID, 'attribute_pa_constrained-outside-diameter', true);
            // }
            $html .= '<option value="' . $availableAttrValue . '" class="attached enabled" >' . $availableAttrLabel . '.</option>';
        }

        wp_send_json_success($html);
    }
    wp_send_json_error($html);
};
add_shortcode('featured_product', 'featured_product_slider');
function featured_product_slider()
{
    $html = '<div class="popular-product">
    <h1>Featured Products</h1>
</div>
<div class="products">
    <div class="product">
        <div class="product-thumb">
            <a href="/product/symmetrical-lip-seal-with-graphite-fiber-reinforced-ptfe-jacket/?variation_id=833&attribute_pa_seal-cross-section=0.0625%20in.&attribute_pa_constrained-inside-diameter=1%20in.&attribute_pa_constrained-outside-diameter=1.125%20in. ">
                <img width="200" height="250" src="/wp-content/uploads/2023/04/readytek-v01-570x450-3d-seal-black-15x-e19669-300x300.png" alt="">
            </a>
        </div>
        <div class="product-name">
            <a href="/product/symmetrical-lip-seal-with-graphite-fiber-reinforced-ptfe-jacket/?variation_id=833&attribute_pa_seal-cross-section=0.0625%20in.&attribute_pa_constrained-inside-diameter=1%20in.&attribute_pa_constrained-outside-diameter=1.125%20in. ">
                1 in. ID Symmetrical Lip Seal with Graphite Fiber-Reinforced PTFE Jacket
            </a>
        </div>
    </div>
    <div class="product">
        <div class="product-thumb">
            <a href="/product/short-lip-seal-with-graphite-fiber-reinforced-ptfe-jacket/?variation_id=726&attribute_pa_seal-cross-section=0.0625%20in.&attribute_pa_constrained-inside-diameter=0.5%20in.&attribute_pa_constrained-outside-diameter=0.625%20in. ">
                <img width="200" height="250" src="/wp-content/uploads/2023/04/readytek-v01-570x450-3d-seal-black-13x-e19701-300x300.png" alt="">
            </a>
        </div>
        <div class="product-name">
            <a href="/product/short-lip-seal-with-graphite-fiber-reinforced-ptfe-jacket/?variation_id=726&attribute_pa_seal-cross-section=0.0625%20in.&attribute_pa_constrained-inside-diameter=0.5%20in.&attribute_pa_constrained-outside-diameter=0.625%20in. ">
                0.5 in. ID Short Lip Seal with Graphite Fiber-Reinforced PTFE Jacket
            </a>
        </div>
    </div>
    <div class="product">
        <div class="product-thumb">
            <a href="/product/short-lip-seal-with-polymer-filled-ptfe-jacket/?variation_id=671&attribute_pa_seal-cross-section=0.0625%20in.&attribute_pa_constrained-inside-diameter=0.125%20in.&attribute_pa_constrained-outside-diameter=0.25%20in. ">
                <img width="200" height="250" src="/wp-content/uploads/2023/04/readytek-v01-570x450-3d-seal-green-13x-e19685-300x300.png" alt="">
            </a>
        </div>
        <div class="product-name">
            <a href="/product/short-lip-seal-with-polymer-filled-ptfe-jacket/?variation_id=671&attribute_pa_seal-cross-section=0.0625%20in.&attribute_pa_constrained-inside-diameter=0.125%20in.&attribute_pa_constrained-outside-diameter=0.25%20in. ">
                0.125 in. ID Short Lip Seal with Polymer-Filled PTFE Jacket
            </a>
        </div>
    </div>
    <div class="product">
        <div class="product-thumb">
            <a href="/product/single-point-contact-lip-seal-with-graphite-fiber-reinforced-ptfe-jacket/?variation_id=935&attribute_pa_seal-cross-section=0.0625%20in.&attribute_pa_constrained-inside-diameter=0.75%20in.&attribute_pa_constrained-outside-diameter=0.875%20in. ">
                <img width="200" height="250" src="/wp-content/uploads/2023/04/readytek-v01-570x450-3d-seal-black-31x-e19733-300x300.png" alt="">
            </a>
        </div>
        <div class="product-name">
            <a href="/product/single-point-contact-lip-seal-with-graphite-fiber-reinforced-ptfe-jacket/?variation_id=935&attribute_pa_seal-cross-section=0.0625%20in.&attribute_pa_constrained-inside-diameter=0.75%20in.&attribute_pa_constrained-outside-diameter=0.875%20in. ">
                0.75 in. ID Single Point Contact Lip Seal with Graphite Fiber-Reinforced PTFE Jacket
            </a>
        </div>
    </div>
    <div class="product">
        <div class="product-thumb">
            <a href="/product/single-point-contact-lip-ptfe-seal-with-polymer-filled-ptfe-jacket/?variation_id=888&attribute_pa_seal-cross-section=0.09375%20in.&attribute_pa_constrained-inside-diameter=0.5%20in.&attribute_pa_constrained-outside-diameter=0.6875%20in. ">
                <img width="200" height="250" src="/wp-content/uploads/2023/04/readytek-v01-570x450-3d-seal-green-31x-e19717-300x300.png" alt="">
            </a>
        </div>
        <div class="product-name">
            <a href="/product/single-point-contact-lip-ptfe-seal-with-polymer-filled-ptfe-jacket/?variation_id=888&attribute_pa_seal-cross-section=0.09375%20in.&attribute_pa_constrained-inside-diameter=0.5%20in.&attribute_pa_constrained-outside-diameter=0.6875%20in. ">
                0.5 in. ID Single Point Contact Lip Seal with Polymer-Filled PTFE Jacket
            </a>
        </div>
    </div>
    <div class="product">
        <div class="product-thumb">
            <a href="/product/symmetrical-lip-seal-with-graphite-fiber-reinforced-ptfe-jacket/?variation_id=841&attribute_pa_seal-cross-section=0.09375%20in.&attribute_pa_constrained-inside-diameter=1%20in.&attribute_pa_constrained-outside-diameter=1.1875%20in. ">
                <img width="200" height="250" src="/wp-content/uploads/2023/04/readytek-v01-570x450-3d-seal-black-15x-e19669-300x300.png" alt="">
            </a>
        </div>
        <div class="product-name">
            <a href="/product/symmetrical-lip-seal-with-graphite-fiber-reinforced-ptfe-jacket/?variation_id=841&attribute_pa_seal-cross-section=0.09375%20in.&attribute_pa_constrained-inside-diameter=1%20in.&attribute_pa_constrained-outside-diameter=1.1875%20in. ">
                1 in. ID Symmetrical Lip Seal with Graphite Fiber-Reinforced PTFE Jacket
            </a>
        </div>
    </div>
</div>';
    return $html;
};

add_action('manage_users_columns', 'account_verification_status_column');
function account_verification_status_column($column_headers)
{
    // unset($column_headers['posts']);

    $column_headers['ur_user_user_status'] = __('Export Compliance Status');

    return $column_headers;
}

add_filter('manage_users_custom_column', 'add_user_column_value', 100, 3);
function add_user_column_value($value, $column_name, $user_id)
{

    if ('ur_user_user_status' == $column_name) {
        if ($value == 'Approved') {
            $value = '<span style="color:green;font-weight:bold;">Verified</span>';
        } else if ($value == 'Pending') {
            $value = '<span class="na" style="color:grey;"><em>Un Verified</em></span>';
        } else {
            $value = '<span class="na" style="color:red;"><em>Rejected</em></span>';
        }
    }

    return $value;
};

function wpb_woo_endpoint_title($title, $id)
{
    if ($title == 'Pay for order' && is_page('checkout')) {
        $title = 'Accessibility';
    }
    //echo "<pre>"; print_r($title); echo "</pre>";
    return $title;
}
add_filter('the_title', 'wpb_woo_endpoint_title', 10, 2);

// add_action('flat_file_upload_to_ftp_cron', 'flat_file_upload_to_ftp');
// function flat_file_upload_to_ftp()
// {
//     get_today_orders_for_flatfile();
//     date_default_timezone_set('America/Los_Angeles');
//     $str = "First Name|Middle Name|Last Name|Email Address|Area Code|Phone Number|Company Name|Line Number|Item number|Quantity|Unit Price|Unit of Measure|Shipping Address 1|Shipping Address 2|ShippingCity|ShippingState|Shipping Zip Code|Shipping Country|Billing Address 1|Billing Address 2|Billing City|Billing State|Billing Zip Code|Billing Country|Shipping Carrier|Order's Freight Total Amount|Order's Tax Total  Amount|Order's Total Amount|Order Date|Order Time|Credit Card Payment ID|Web Order ID|Currency|Compliance Status\n";

//     $is_file_exist = file_exists(wp_upload_dir()['basedir'] . '/orders-' . date('Y-m-d', strtotime("yesterday")) . '.txt');
//     if (!$is_file_exist) {
//         $file = fopen(wp_upload_dir()['basedir'] . '/orders-' . date('Y-m-d', strtotime("yesterday")) . '.txt', 'a'); // 'a' for append to file - created if doesn't exit
//         fwrite($file, $str);
//         fclose($file);
//     }

//     // $ftp_server = "45.33.89.171";
//     $ftp_server = "45.63.8.104";
//     $server_port = 22;
//     $serverUser = "root";
//     // $serverPassword = "E&{X4d.+FB(C";
//     $serverPassword = "z5]QVJpn3_}5sX{7";
//     $fileName = 'orders-' . date('Y-m-d', strtotime("yesterday")) . '.txt';
//     // $remote_file = "/home/balseal/E-Commerce/Production/" . $fileName;
//     $remote_file = "/var/www/html/readytek.balseal.com/E-Commerce/Production/" . $fileName;
//     $pathLocalFile = wp_upload_dir()['basedir'] . '/' . $fileName;

//     try {

//         $ssh = new Net_SSH2($ftp_server);
//         if (!$ssh->login($serverUser, $serverPassword)) {
//             exit('bad login');
//         }

//         $scp = new Net_SCP($ssh);
//         $scp->put($remote_file, $pathLocalFile, NET_SCP_LOCAL_FILE);

//         // $ch = curl_init('sftp://' . $ftp_server . ':' . $server_port . $remote_file);
//         // $fh = fopen($pathLocalFile, 'r');
//         // if ($fh) {
//         //     curl_setopt($ch, CURLOPT_USERPWD, $serverUser . ':' . $serverPassword);
//         //     curl_setopt($ch, CURLOPT_UPLOAD, true);
//         //     curl_setopt($ch, CURLOPT_PROTOCOLS, CURLPROTO_SFTP);
//         //     curl_setopt($ch, CURLOPT_INFILE, $fh);
//         //     curl_setopt($ch, CURLOPT_INFILESIZE, filesize($pathLocalFile));
//         //     curl_setopt($ch, CURLOPT_VERBOSE, true);
//         //     $verbose = fopen('php://temp', 'w+');
//         //     curl_setopt($ch, CURLOPT_STDERR, $verbose);
//         //     $response = curl_exec($ch);
//         //     $error = curl_error($ch);
//         //     curl_close($ch);
//         //     if ($response) {
//         //         //echo "Success";
//         //     } else {
//         //         //echo "Failure";
//         //         rewind($verbose);
//         //         $verboseLog = stream_get_contents($verbose);
//         //         //echo "Verbose information:\n" . $verboseLog . "\n";
//         //     }
//         // }

//         //save copy to development server
//         $ftp_server2 = "144.202.79.222";
//         $server_port2 = 21;
//         $serverUser2 = "developer@uat.balseal.agencypartnerinteractive.com";
//         $serverPassword2 = "hNCL5-UG]Eff";
//         $fileName2 = 'orders-' . date('Y-m-d', strtotime("yesterday")) . '.txt';
//         $remote_file2 = "/public_html/flatfiles/Production/" . $fileName2;
//         $pathLocalFile2 = wp_upload_dir()['basedir'] . '/' . $fileName2;

//         try {
//             // $ssh2 = new Net_SSH2($ftp_server2);
//             // if (!$ssh2->login($serverUser2, $serverPassword2)) {
//             //     exit('bad login');
//             // }

//             // $scp2 = new Net_SCP($ssh2);
//             // $scp2->put($remote_file2, $pathLocalFile2, NET_SCP_LOCAL_FILE);
//             $ch2 = curl_init('ftp://' . $ftp_server2 . ':' . $server_port2 . $remote_file2);
//             $fh2 = fopen($pathLocalFile2, 'r');
//             if ($fh2) {
//                 curl_setopt($ch2, CURLOPT_USERPWD, $serverUser2 . ':' . $serverPassword2);
//                 curl_setopt($ch2, CURLOPT_UPLOAD, true);
//                 curl_setopt($ch2, CURLOPT_PROTOCOLS, CURLPROTO_FTP);
//                 curl_setopt($ch2, CURLOPT_INFILE, $fh2);
//                 curl_setopt($ch2, CURLOPT_INFILESIZE, filesize($pathLocalFile2));
//                 curl_setopt($ch2, CURLOPT_VERBOSE, true);
//                 $verbose2 = fopen('php://temp', 'w+');
//                 curl_setopt($ch2, CURLOPT_STDERR, $verbose2);
//                 $response2 = curl_exec($ch2);
//                 $error2 = curl_error($ch2);
//                 curl_close($ch2);
//                 if ($response2) {
//                     //echo "Success";
//                 } else {
//                     //echo "Failure";
//                     rewind($verbose2);
//                     $verboseLog2 = stream_get_contents($verbose2);
//                     //echo "Verbose information:\n" . $verboseLog . "\n";
//                 }
//             }
//         } catch (Exception $e) {
//             //echo "error exception".$e->getMessage();
//         }
//         //end copy to development

//         // $to = 'kamran.shah@venturequeue.com, fawad@agencypartner.com, readytek@balseal.com, ohsiao@balseal.com';
//         $to = 'kamran.shah@venturequeue.com';
//         $subject = 'Alert Flat file uploaded to Production FTP server';
//         $body = 'Flat file upload on ftp at ' . date('Y-m-d H:i:s');
//         $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ReadyTek <readytek@balseal.com>');
//         $attachments = array(wp_upload_dir()['basedir'] . '/orders-' . date('Y-m-d', strtotime("yesterday")) . '.txt');

//         wp_mail($to, $subject, $body, $headers, $attachments);
//     } catch (Exception $e) {
//         //echo "error exception".$e->getMessage();
//     }

// }

add_filter('woocommerce_order_item_get_formatted_meta_data', 'unset_specific_order_item_meta_data');
function unset_specific_order_item_meta_data($formatted_meta)
{
    foreach ($formatted_meta as $key => $meta) {
        if ($meta->key == 'pa_constrained-outside-diameter') {
            unset($formatted_meta[$key]);
        }

    }
    return $formatted_meta;
}

add_action('ur_user_status_updated', 'cancel_customer_orders_on_reject', 10, 3);
function cancel_customer_orders_on_reject($status, $user_id, $alert_user)
{
    // Get the user object.
    $user = get_userdata($user_id);

    // Get all the user roles as an array.
    $user_roles = $user->roles;

    if ($status == '-1' && (in_array('customer', $user_roles, true) || in_array('subscriber', $user_roles, true))) {

        $customer_orders = get_posts(array(
            'numberposts' => -1,
            'meta_key' => '_customer_user',
            'orderby' => 'date',
            'order' => 'DESC',
            'meta_value' => $user_id,
            'post_type' => wc_get_order_types(),
            'post_status' => array_keys(wc_get_order_statuses()), 'post_status' => array('wc-processing', 'wc-pending', 'wc-on-hold', 'wc-failed'),
        ));

        if ($customer_orders) {
            foreach ($customer_orders as $key => $customer_order) {
                $wc_order = new WC_Order($customer_order->ID);
                $wc_order->update_status('cancelled');
            }
        }
    }

}

// function flat_file_upload_to_ftp_dev_test()
// {
//     if (isset($_GET['dev'])) {

//         date_default_timezone_set('America/Los_Angeles');
//         $str = "First Name|Middle Name|Last Name|Email Address|Area Code|Phone Number|Company Name|Line Number|Item number|Quantity|Unit Price|Unit of Measure|Shipping Address 1|Shipping Address 2|ShippingCity|ShippingState|Shipping Zip Code|Shipping Country|Billing Address 1|Billing Address 2|Billing City|Billing State|Billing Zip Code|Billing Country|Shipping Carrier|Order's Freight Total Amount|Order's Tax Total  Amount|Order's Total Amount|Order Date|Order Time|Credit Card Payment ID|Web Order ID|Currency|Compliance Status\n";

//         $is_file_exist = file_exists(wp_upload_dir()['basedir'] . '/orders-' . date('Y-m-d', strtotime("yesterday")) . '.txt');
//         if (!$is_file_exist) {
//             $file = fopen(wp_upload_dir()['basedir'] . '/orders-' . date('Y-m-d', strtotime("yesterday")) . '.txt', 'a'); // 'a' for append to file - created if doesn't exit
//             fwrite($file, $str);
//             fclose($file);
//         }

//         $ftp_server = "45.33.89.171";
//         $server_port = 22;
//         $serverUser = "root";
//         $serverPassword = "E&{X4d.+FB(C";
//         $fileName = 'orders-' . date('Y-m-d', strtotime("yesterday")) . '.txt';
// //         $remote_file = "/var/www/html/readytek.balseal.com/E-Commerce/Production/" . $fileName;
//         $remote_file = "/home/balseal/E-Commerce/Production/" . $fileName;
//         $pathLocalFile = wp_upload_dir()['basedir'] . '/' . $fileName;

//         try {
//             $ssh = new Net_SSH2($ftp_server);
//             if (!$ssh->login($serverUser, $serverPassword)) {
//                 exit('bad login');
//             }

//             $scp = new Net_SCP($ssh);
//             $scp->put($remote_file, $pathLocalFile, NET_SCP_LOCAL_FILE);

//         } catch (Exception $e) {
//             echo "error exception" . $e->getMessage();
//         }
//         die;
//     }
// }

// flat_file_upload_to_ftp_dev_test();

function get_today_orders_for_flatfile()
{

    date_default_timezone_set('America/Los_Angeles');

    $year = date('Y', strtotime("yesterday"));
    $month = date('m', strtotime("yesterday"));
    $day = date('d', strtotime("yesterday"));
    $orderStatus = wc_get_order_statuses();
    unset($orderStatus['wc-pending']);

    $orders = get_posts(array(
        'numberposts' => -1,
        'orderby' => 'date',
        'order' => 'DESC',
        'post_type' => 'shop_order',
        'post_status' => array_keys($orderStatus),
        'date_query' => array(
            array(
                'year' => $year,
                'month' => $month,
                'day' => $day,
            ),
        ),
    ));
    if ($orders) {
        foreach ($orders as $key => $order) {
            $wc_order = wc_get_order((int) $order->ID);
            create_flat_file_wc_order((int) $order->ID, $wc_order);
        }
    }

}

//test cron for time check
// add_action('flat_file_upload_to_ftp_cron_test', 'flat_file_upload_to_ftp_test');
// function flat_file_upload_to_ftp_test()
// {

//     try {

//         // $to = 'kamran.shah@venturequeue.com, mashab@agencypartner.com';
//         $to = 'kamran.shah@venturequeue.com';
//         $subject = 'Alert Test Cron Time';
//         $body = 'Cron run at ' . date('Y-m-d H:i:s');
//         $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ReadyTek <readytek@balseal.com>');
//         //$attachments = array( wp_upload_dir()['basedir'] . '/orders-' . date('Y-m-d', strtotime("yesterday")) . '.txt' );

//         wp_mail($to, $subject, $body, $headers);
//     } catch (Exception $e) {
//         //echo "error exception".$e->getMessage();
//     }

// }

// function limit_product_quantity_in_cart($passed, $product_id, $quantity, $variation_id = 0)
// {
//     $max_allowed_quantity = 30;
//     $new_variation_id = ($variation_id) ? $variation_id : $product_id;
//     $cart = WC()->cart->get_cart();

//     // Calculate the total quantity of the product already in the cart
//     $total_quantity_in_cart = 0;
//     foreach ($cart as $cart_item_key => $cart_item) {
//         if ($cart_item['variation_id'] === $new_variation_id) {
//             $total_quantity_in_cart += (int) $cart_item['quantity'];
//         }
//     }

//     // Check if adding the new quantity would exceed the limit
//     if ($total_quantity_in_cart + $quantity > $max_allowed_quantity) {
//         wc_add_notice(sprintf('You can only add up to %d units of this product to the cart.', $max_allowed_quantity), 'error');
//         return false;
//     }

//     return $passed;
// }
// add_filter('woocommerce_add_to_cart_validation', 'limit_product_quantity_in_cart', 101, 4);

function limit_product_quantity_in_cart($passed, $product_id, $quantity, $variation_id = 0)
{
    $max_allowed_quantity = 30;
    $new_variation_id = ($variation_id) ? $variation_id : $product_id;
    $cart = WC()->cart->get_cart();

    // Calculate the total quantity of the product already in the cart
    $total_quantity_in_cart = 0;
    foreach ($cart as $cart_item_key => $cart_item) {
        if ($cart_item['variation_id'] === $new_variation_id) {
            $total_quantity_in_cart += (int) $cart_item['quantity'];
        }
    }
    // Check if adding the new quantity would exceed the limit
    if ($total_quantity_in_cart + $quantity > $max_allowed_quantity) {
        // Start a session
        if (!session_id()) {
            session_start();
        }

        // Set a session variable to indicate the popup should be shown
        $_SESSION['show_quantity_limit_popup'] = 1;
        // print_r($_SESSION); die;

        // Add an error notice
        // wc_add_notice(sprintf('You can only add up to %d units of this product to the cart.', $max_allowed_quantity), 'error');
        return false;
    }

    return $passed;
}
add_filter('woocommerce_add_to_cart_validation', 'limit_product_quantity_in_cart', 101, 4);

// Check for the session variable and display a JavaScript alert
function show_quantity_limit_popup()
{
    // if(!session_id()) {
    ob_start();
    session_start();
    ob_get_clean();

    // }
    if (is_product() && isset($_SESSION['show_quantity_limit_popup'])) {
        ?>
            <script>
            setTimeout(() =>
            {
                jQuery(".wpb-pcf-form-fire.wpb-pcf-btn-large.wpb-pcf-btn.wpb-pcf-btn-default").click();
            }, 1000);
            </script>
            <?php
unset($_SESSION['show_quantity_limit_popup']); // Remove the session variable
    }
}
add_action('wp_head', 'show_quantity_limit_popup');

function mailtrap($phpmailer)
{
    $phpmailer->isSMTP();
    $phpmailer->Host = 'sandbox.smtp.mailtrap.io';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Port = 2525;
    $phpmailer->Username = '5b9d03e1b77563';
    $phpmailer->Password = 'ffccffc89ceac9';
}

add_action('phpmailer_init', 'mailtrap');

// add_action('wp_head', send_emails_for_7_days_old_orders());
function send_emails_for_7_days_old_orders()
{
    // Calculate the date 7 days ago from today
    $seven_days_ago = strtotime('-7 days');
    $seven_days_ago_start = date('Y-m-d', $seven_days_ago) . ' 00:00:00';
    $seven_days_ago_end = date('Y-m-d', $seven_days_ago) . ' 23:59:59';

    $orders = get_posts(array(
        'post_type' => 'shop_order',
        'post_status' => array_keys(wc_get_order_statuses()), // All statuses
        'date_query' => array(
            array(
                'after' => $seven_days_ago_start,
                'before' => $seven_days_ago_end,
                'inclusive' => true,
            ),
        ),
        'posts_per_page' => -1,
    ));
    // echo '<pre>'; print_r($orders); echo '</pre>'; die;
    foreach ($orders as $order_post) {

        global $wpdb;
        $order_id = $order_post->ID;

        // Query to retrieve the billing email of the order
        $email = $wpdb->get_var(
            $wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key = '_billing_email'", $order_id)
        );
        $first_name = $wpdb->get_var(
            $wpdb->prepare("SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = %d AND meta_key = '_billing_first_name'", $order_id)
        );

        // Send email to the customer
        $subject = 'Will your next seal be custom-engineered?';
        $message = '<div class="customer-support" style="width: 50%; margin: 0 auto; border: 1px solid #A6A6A6; padding: 30px 50px 0px 50px;">
    <div class="site-logo" style="text-align: center; padding-bottom: 20px;"> <img src="https://readytek.balseal.com/wp-content/uploads/2023/03/logo.svg" alt="" style="width: 50%;"> </div> <hr> <h2 style="font-size: 18px; color: black; font-weight: 600; "; text-align: center;"> LET’S TALK CUSTOM SEALING AND SUPPORT.</h2><img src="" alt=""><div class="support-content"> <h2 style="font-size: 16px; color: black; font-weight: 400; ";">'.$first_name.',</h2><p style="font-size: 16px; color: black; font-weight: 400; ";">Now that you’ve
            experienced the world’s most advanced off-theshelf seals, are you ready to take your designs to the next
            level?</p>
        <p style="font-size: 16px; color: black; font-weight: 400; ";">We’re powered by
            Bal Seal Engineering, a global leader in seal application and material science. That means we can also
            connect you with:</p>
        <ul style="font-size: 16px; color: black; font-weight: 400;">
            <li>Custom-engineered Bal Seal® products</li>
            <li>Expert design assistance</li>
            <li>A nearly limitless combination of materials, energizers & geometries</li>
        </ul>
        <p style="font-size: 16px; color: black; font-weight: 400; ";">If you’ve set
            your sights on more performance and reliability, talk with a Bal Seal technical specialist. They’ll show you
            how a custom seal can get you there.</p>
        <div class="support-footer" style="text-align: center; margin-top: 50px;">
            <a href="https://readytek.balseal.com/customer-support-request/
"
                style="text-decoration: none; font-size: 18px; color: #fff; font-weight: 400; background-color: #0092D5; border-radius: 21px; border: none; padding: 10px 30px; ";">Get
                Technical Support</a>
            <img src="https://readytek.balseal.com/wp-content/uploads/2023/03/ft-sec-1.jpg" alt="" style="margin-top: 20px;width: 100%;">
            <h2><a href="https://readytek.balseal.com/my-account/"
                    style="font-size: 18px; color: #0092D5; font-weight: 600; ";">Manage
                    Your Subscriptions</a> | <a href="https://readytek.balseal.com/privacy-policy/"
                    style="font-size: 18px; color: #0092D5; font-weight: 600; ";">Privacy
                    Policy</a></h2>
            <h2 style="font-size: 16px; color: black; font-weight: 400; ";">ReadyTek OTS
            </h2>
            <h2 style="font-size: 16px; color: black; font-weight: 400; ";">Foothill
                Ranch, CA 92610</h2>
        </div>
    </div>
</div>';
        $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ReadyTek <readytek@balseal.com>');
        wp_mail($email, $subject, $message, $headers);

        // Log that the email was sent
        error_log('Email sent for Order ID: ' . $order_id);
    }
}

// Schedule the function to run daily at a specific time
add_action('init', 'schedule_emails_for_7_days_old_orders');
function schedule_emails_for_7_days_old_orders()
{
    if (!wp_next_scheduled('send_emails_for_7_days_old_orders')) {
        wp_schedule_event(strtotime('03:00:00'), 'daily', 'send_emails_for_7_days_old_orders');
    }
}

// Hook to the scheduled action to execute the function
add_action('send_emails_for_7_days_old_orders', 'send_emails_for_7_days_old_orders');