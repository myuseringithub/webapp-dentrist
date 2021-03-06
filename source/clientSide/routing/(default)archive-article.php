<?php get_header(); ?>

<!-- SECONDARY TOP MENU -->
<?php echo SZN_template_includer('navigation/top-secondary-navigation.php');	?>

<div class="container-fluid">
	
    <!-- Silence is Gold. -->
    <h1 style="text-align:center; width:100%; "> <?php post_type_archive_title(); ?> </h1>
    <h3 style="text-align:center; width:100%; "> </h3>

	<!-- Loader masonry  -->
	<div id="ajax-loader-masonry" class="ajax-loader"></div>
	
<!-----------------------------------------  in case AUTHOR ARCHIVE PAGE  -------------------------------------------->
	<?php
    // Echo author information on the top of author archive pages, using the plugin "wp-about-author" functions.
    if ( is_author() ) {/* If this is an author archive */
            //echo '<div align="center" style="margin:auto; margin-top: -6px;"><h3 class="pagetitle">Posts by Author'; 
            //echo $author->display_name;
            //echo '</h3> </div><br>'; 
        if(function_exists('wp_about_author_display')){
            $for_feed = false;
            echo '<div id="author-box" style="max-width: 800px; margin: auto; margin-bottom: 0; margin-top:-15px; border-radius:120px;overflow:hidden;">';
            echo wp_about_author_display();
            echo '</div>';
        } else {
            echo ""; // deat there is no function !!
        } 
    }
    ?>


<!------------------------------------- Masonry Posts Area  -------------------------------------------->
<?php include 'incphp_theme/masonry-wrapper.php';?>

<!-----------------------------------------  NAVIGATION FOR NEXT/PREVIOUS POSTS  -------------------------------------------->
	<div id="navigation">
		<ul class="pager">
			<li id="navigation-next"><?php next_posts_link(__('&laquo; Previous', 'ipin')) ?></li>
			<li id="navigation-previous"><?php previous_posts_link(__('Next &raquo;', 'ipin')) ?></li>
		</ul>
	</div>

<!-----------------------------------------  SCROLL TOP  -------------------------------------------->
	<div id="scrolltotop"><a href="#"><i class="fa fa-chevron-up"></i><br /><?php _e('Top', 'ipin'); ?></a></div>

</div>


<?php get_footer(); ?>