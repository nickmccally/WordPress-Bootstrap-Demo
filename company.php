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
	$result = $wpdb->get_results("SELECT DISTINCT pm.meta_value AS symbol FROM {$wpdb->postmeta} pm inner JOIN {$wpdb->posts} p ON p.ID = pm.post_id WHERE pm.meta_value = '{$symbol}' AND p.post_status = 'publish'", ARRAY_A);
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



				<?php if ( have_posts() ) : ?>
					<header class="page-header">
						<?php
							the_title( '<h1 class="page-title">', '</h1>' );
						?>
					</header><!-- .page-header -->

					<?php /* Start the Loop */ ?>


					<?php
					// print_r($companiesAPI);
					foreach($companiesAPI->companyProfiles as $companyData){ ?>
						<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">
							<header class="entry-header">

								<h2 class="entry-title"><a href="<?php echo get_permalink() . "{$companyData->symbol}/" ?>" rel="bookmark">
								<?php	echo $companyData->profile->companyName ?>
								</a></h2>

								<?php if ( 'post' == get_post_type() ) : ?>

									<div class="entry-meta">
										<?php // understrap_posted_on(); ?>
									</div><!-- .entry-meta -->

								<?php endif; ?>

							</header><!-- .entry-header -->

							<?php echo "<img src='{$companyData->profile->image}' alt='{$companyData->profile->companyName}'>"; ?>

							<div class="entry-content">
								<p><?php echo $companyData->profile->description; ?></p>



								<?php
											$color = ($companyData->profile->changes < 0) ? "text-danger" : "text-success";
											$companyData->profile->lastDiv = ($companyData->profile->lastDiv == 0) ? "N/A" : $companyData->profile->lastDiv;
											$companyData->profile->mktCap = "$" . currencyFormat($companyData->profile->mktCap);
											$companyData->profile->beta = number_format($companyData->profile->beta, 2);
											$companyData->profile->volAvg = currencyFormat($companyData->profile->volAvg);

											echo "<table>";
											echo "<tr><th>Price</th><td class='{$color}'>\${$companyData->profile->price}</td></tr>";
											echo "<tr><th>Price Change</th><td class='{$color}'>{$companyData->profile->changes}</td></tr>";
											echo "<tr><th>Price Change %</th><td class='{$color}'>{$companyData->profile->changesPercentage}</td></tr>";
											echo "<tr><th>52 week range</th><td>{$companyData->profile->range}</td></tr>";
											echo "<tr><th>Beta</th><td>{$companyData->profile->beta}</td></tr>";
											echo "<tr><th>Volume Average</th><td>{$companyData->profile->volAvg}</td></tr>";
											echo "<tr><th>Market Capitalisation</th><td>{$companyData->profile->mktCap}</td></tr>";
											echo "<tr><th>Last Dividend</th><td>{$companyData->profile->lastDiv}</td></tr>";
											echo "</table>";
								?>


								<?php
								wp_link_pages(
									array(
										'before' => '<div class="page-links">' . __( 'Pages:', 'understrap' ),
										'after'  => '</div>',
									)
								);
								?>

							</div><!-- .entry-content -->

							<footer class="entry-footer">

								<?php //understrap_entry_footer(); ?>

							</footer><!-- .entry-footer -->

						</article><!-- #post-## -->
						<?php

							//echo $companyData->symbol;
							// print_r($companyData);

							// $company = get_field('company');
							// $symbol = strtoupper(get_field('symbol'));
							// $companyClean = $result  = strtolower(trim(preg_replace('/[^a-zA-Z0-9_ -]/s','',$company)));
							// $companies[$symbol] = $company;
						/*
						 * Include the Post-Format-specific template for the content.
						 * If you want to override this in a child theme, then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */
						// get_template_part( 'loop-templates/content', get_post_format() );
						?>

					<?php } ?>

				<?php else : ?>

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
