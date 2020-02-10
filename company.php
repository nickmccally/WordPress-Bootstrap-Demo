<?php
/**
 * Template Name: Company
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// If symbol is defined in the query string
if($symbol = strtoupper(get_query_var('symbol'))){
	$query = $wpdb->prepare("SELECT DISTINCT pm.meta_value AS symbol FROM {$wpdb->postmeta} pm inner JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_value = '%s' AND p.post_status = 'publish'",
  $symbol);
	$result = $wpdb->get_results($query, ARRAY_A);
	if(!$result)
		generate_404();
} else {
// Returning all results
	$result = $wpdb->get_results("SELECT DISTINCT pm.meta_value AS symbol FROM {$wpdb->postmeta} pm inner JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_key = 'symbol' AND p.post_status = 'publish'", ARRAY_A);
}


$symbol = implode(',', array_column($result, 'symbol'));
$companiesAPI = makeRequest("https://financialmodelingprep.com/api/v3/company/profile/{$symbol}");
if(!isset($companiesAPI->companyProfiles)) {
	$companiesAPI->companyProfiles[] = $companiesAPI;
}


get_header();

$container = get_theme_mod( 'understrap_container_type' );
?>

<?php if ( is_front_page() && is_home() ) : ?>
	<?php get_template_part( 'global-templates/hero' ); ?>
<?php endif; ?>

<div class="wrapper" id="index-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<div class="row">

			<!-- Do the left sidebar check and opens the primary div -->
			<?php get_template_part( 'global-templates/left-sidebar-check' ); ?>

			<main class="site-main" id="main">




				<?php if ( !get_query_var('symbol') ) : ?>
					<header class="page-header">
						<?php
							the_title( '<h1 class="page-title">', '</h1>' );
						?>
					</header><!-- .page-header -->
					<ul class="h3">
					<?php foreach($companiesAPI->companyProfiles as $companyData){ ?>
					<li><h3 class="entry-title"><a href="<?php echo get_permalink() . "{$companyData->symbol}/" ?>" rel="bookmark">
					<?php	echo $companyData->profile->companyName . " (". $companyData->symbol . ")";  ?>
					</a></h3></li>
					<?php } ?>
					</ul>

					<?php else : ?>

					<?php foreach($companiesAPI->companyProfiles as $companyData){ ?>
						<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
							<header class="entry-header">

								<h2 class="entry-title">
								<?php	echo $companyData->profile->companyName . " (". $companyData->symbol . ")" ?>
								</h2>

								<?php if ( 'post' == get_post_type() ) : ?>

									<div class="entry-meta">
										<?php // understrap_posted_on(); ?>
									</div><!-- .entry-meta -->

								<?php endif; ?>

							</header><!-- .entry-header -->



							<div class="entry-content">
								<p><?php echo $companyData->profile->description; ?></p>
								<?php set_query_var( 'company_data', $companyData ); ?>

								<?php if( is_active_sidebar( 'widget-stock-recommendations' ) ) : ?>
									<aside class="widget-stock-recommendations">
										<?php dynamic_sidebar( 'widget-stock-recommendations' ); ?>
									</aside>
								<?php endif; ?>

								<?php if( is_active_sidebar( 'widget-related-articles' ) ) : ?>
									<aside class="widget-related-articles">
										<?php dynamic_sidebar( 'widget-related-articles' ); ?>
									</aside>
								<?php endif; ?>




							</div><!-- .entry-content -->



						</article><!-- #post-## -->

					<?php } ?>



					<?php //get_template_part( 'loop-templates/content', 'none' ); ?>

				<?php endif; ?>
				<ul class="h2">


				</ul>

			</main><!-- #main -->

			<!-- The pagination component -->
			<?php understrap_pagination(); ?>

			<!-- Do the right sidebar check -->
			<?php get_template_part( 'global-templates/right-sidebar-check' ); ?>

		</div><!-- .row -->

	</div><!-- #content -->

</div><!-- #index-wrapper -->

<?php get_footer();
