			<?php
				// If Single or Archive (Category, Tag, Author or a Date based page).
				if ( is_single() || is_archive() ) :
			?>
					</div><!-- /.col -->

					<?php
						get_sidebar();
					?>

				</div><!-- /.row -->
			<?php
				endif;
			?>
		</main><!-- /#main -->
		<footer id="footer" class="noprint">
			<div class="container">
				<div class="row">
				    <div class="col-md-3 footer-1">
				        <?php
							dynamic_sidebar( 'footer_1' );
						?>
				    </div>
				    <div class="col-md-4 footer-2">
				        <?php
							dynamic_sidebar( 'footer_2' );
						?>
				    </div>
				    <div class="col-md-5">
				        <?php
							dynamic_sidebar( 'footer_3' );
						?>
				    </div>
			    </div>
			    <div class="row">
			        <div class="col-sm-12">
			        <hr>
			        </div>
			    </div>
			    <div class="row copy">
			        <div class="col-md-6">
			            <?php
							dynamic_sidebar( 'footer_copy' );
						?>
			        </div>
			        
			    </div>
			</div><!-- /.container -->
		</footer><!-- /#footer -->
	</div><!-- /#wrapper -->
    <div class="request-qoute-btn">
		<?php echo do_shortcode('[wpb-pcf-button]'); ?>
	</div>
	<?php
		wp_footer();
	?>
	
	<script type="text/javascript" src="/wp-content/themes/readytek/assets/js/owl.carousel.min.js"></script>
	<script type="text/javascript" src="/wp-content/themes/readytek/assets/js/custom.js"></script>
    <!-- The Modal -->
<div id="vq-social-modal" class="vq-social-modal">

  <!-- Modal content -->
  <div class="vq-social-modal-content">
  <header class="share-modal-header">
    <span class="vq-social-modal-close">&times;</span>
    <h2 class="share-pop-deading">Share Via</h2>
    </header>
    <div class="sharedaddy sd-sharing-enabled">
    <div class="robots-nocontent sd-block sd-social sd-social-icon sd-sharing">
        <div class="sd-content">
            <ul data-sharing-events-added="true"></ul>
        </div>
    </div>
</div>
  </div>

</div>
</body>
</html>