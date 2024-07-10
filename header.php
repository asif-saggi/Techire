<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta name="robots" content="noindex,nofollow">
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<script src="https://apps.elfsight.com/p/platform.js" defer></script>
	<link rel="stylesheet" href="https://use.typekit.net/pou0gxy.css">
	<?php wp_head(); ?>
</head>

<?php
	$navbar_scheme   = get_theme_mod( 'navbar_scheme', 'navbar-light bg-light' ); // Get custom meta-value.
	$navbar_position = get_theme_mod( 'navbar_position', 'static' ); // Get custom meta-value.

	$search_enabled  = get_theme_mod( 'search_enabled', '1' ); // Get custom meta-value.
?>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<a href="#main" class="visually-hidden-focusable"><?php esc_html_e( 'Skip to main content', 'readytek' ); ?></a>

<div id="wrapper">
	<header class="noprint">
		<nav id="header" class="header">
			<div class="container">
			    <div class="row">
			        <div class="col-md-4">
			            <a class="navbar-brand" href="<?php echo esc_url( home_url() ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
        					<?php
        						$header_logo = get_theme_mod( 'header_logo' ); // Get custom meta-value.
        
        						if ( ! empty( $header_logo ) ) :
        					?>
						<img src="<?php echo esc_url( $header_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
        					<?php
        						else :
        							echo esc_attr( get_bloginfo( 'name', 'display' ) );
        						endif;
        					?>
        				</a>
			        </div>
					     <div class="top-right">
			                <div class="search">
			                    <form class="search-form my-2 my-lg-0" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
    								<div class="input-group">
    									<input type="text" name="s" class="form-control" placeholder="<?php esc_attr_e( 'Enter your search key....', 'readytek' ); ?>" title="<?php esc_attr_e( 'Search', 'readytek' ); ?>" />
    									<button type="submit" name="submit" class="btn btn-outline-secondary"><img src="/wp-content/themes/readytek/assets/images/search-ico.svg" alt="*"></button>
    								</div>
    							</form>
			                </div>
			                <ul class="info">
			                    <li>
			                        <a href="/my-account/"><img src="/wp-content/themes/readytek/assets/images/icon-account.svg" alt="*"></a>
			                    </li>
			                    <li><a class="cart-customlocation" href="<?php echo wc_get_cart_url(); ?>"><img src="/wp-content/themes/readytek/assets/images/icon-cart.svg" alt="*"> <span class="count"><?php echo sprintf(_n('%d', '%d', WC()->cart->get_cart_contents_count()), WC()->cart->get_cart_contents_count());?></span></a></li>
			                </ul>
			            </div>

			       
			            <div class="top-nav">
			                	<?php
						// Loading WordPress Custom Menu (theme_location).
						wp_nav_menu(
							array(
								'menu_class'     => 'nav-menu',
								'container'      => '',
								'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
								'walker'         => new WP_Bootstrap_Navwalker(),
								'theme_location' => 'main-menu',
							)
						);

					
					?>
			            </div>
					
			    </div>
			</div><!-- /.container -->
		</nav><!-- /#header -->
	</header>
	
	<section class="inner-head noprint">
	    <div class="container">
	        <div class="row">
	            <div class="col-md-12">
	                <h2><?php echo (is_product_category() || is_shop()) ? woocommerce_page_title() : str_replace("ID ","", get_the_title()); ?></h2>
	                <div class="breadcrumbs">
	                    <?php if(function_exists('bcn_display'))
                            {
                            bcn_display();
                        }?>
	                </div>
	            </div>
	        </div>
	    </div>
	</section>
	<?php if(!is_user_logged_in()){ ?>

<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header cus-modal-header-set">
			    <button type="button" class="btn btn-secondary btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <?php echo do_shortcode("[woocommerce_my_account]"); ?>
          </div>
        </div>
      </div>
    </div>
	
	<?php if(!is_page( 'my-account' )) : ?>
	<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header cus-modal-header-set">
			   <button type="button" class="btn btn-secondary btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <?php echo do_shortcode("[user_registration_form id='390']"); ?>
          </div>
        </div>
      </div>
    </div>
	<?php endif; ?>

	<?php } ?>
	<main class="container main-cont">
		<?php
			// If Single or Archive (Category, Tag, Author or a Date based page).
			if ( is_single() || is_archive() ) :
				$col = (is_product_category() || is_product() || is_cart() || is_checkout() || is_page('products') || is_shop()) ? "col-md-12" : "col-md-8";
		?>
			<div class="row">
				<div class="<?php echo $col; ?> col-sm-12">
		<?php
			endif;
		?>