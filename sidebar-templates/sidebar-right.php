<?php
/**
 * The right sidebar containing the main widget area
 *
 * @package understrap
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( 'right-sidebar' ) ) {
	return;
}

// when both sidebars turned on reduce col size to 3 from 4.
$sidebar_pos = get_theme_mod( 'understrap_sidebar_position' );
?>

<?php if ( 'both' === $sidebar_pos ) : ?>
	<div class="col-md-3 widget-area" id="right-sidebar" role="complementary">
<?php else : ?>
	<div class="col-md-4 widget-area" id="right-sidebar" role="complementary">
<?php endif; ?>
<?php dynamic_sidebar( 'right-sidebar' ); ?>


<?php
			if(get_query_var('ticker_symbol')){
				$tickerSymbol = get_query_var('ticker_symbol');
				$company = makeRequest("https://financialmodelingprep.com/api/v3/company/profile/{$tickerSymbol}");
?>
<aside>
	<ul class="list-group p-0 mt-4">
		<li class="list-group-item"><h3 class="m-0"><a href="/featured-companies/<?php echo $tickerSymbol ?>/"><?php echo $company->profile->companyName ?></a></h3></li>
		<li class="list-group-item"><?php echo $company->profile->description ?></li>
		<li class="list-group-item"><strong>Exchange:</strong> <?php echo $company->profile->exchange ?></li>
		<li class="list-group-item"><strong>Industry:</strong> <?php echo $company->profile->industry ?></li>
		<li class="list-group-item"><strong>Sector:</strong> <?php echo $company->profile->sector ?></li>
		<li class="list-group-item"><strong>CEO:</strong> <?php echo $company->profile->ceo ?></li>
		<li class="list-group-item"><strong>Website:</strong> <a href="<?php echo $company->profile->website ?>" target="_blank"><?php echo $company->profile->website ?></a></li>
		<li class="list-group-item text-center"><img src="<?php echo $company->profile->image ?>" alt="<?php echo $company->profile->companyName ?>"></li>
	</ul>
</aside>
<?php } ?>

<?php
			if(get_query_var('company_data')){
				$company = get_query_var('company_data');
				$color = ($company->profile->changes < 0) ? "text-danger" : "text-success";
				$company->profile->lastDiv = ($company->profile->lastDiv == 0) ? "N/A" : $company->profile->lastDiv;
				$company->profile->mktCap = "$" . currencyFormat($company->profile->mktCap);
				$company->profile->beta = number_format($company->profile->beta, 2);
				$company->profile->volAvg = currencyFormat($company->profile->volAvg);
?>
<aside>
	<ul class="list-group p-0 mt-4">
		<li class="list-group-item"><strong>Price:</strong><span class="pull-right <?php echo $color ?>"><?php echo $company->profile->price ?></span></li>
		<li class="list-group-item"><strong>Price Change:</strong><span class="pull-right <?php echo $color ?>"><?php echo $company->profile->changes ?></span></li>
		<li class="list-group-item"><strong>Price Change %</strong><span class="pull-right <?php echo $color ?>"><?php echo $company->profile->changesPercentage ?></span></li>
		<li class="list-group-item"><strong>52 week range:</strong><span class="pull-right"><?php echo $company->profile->range ?></span></li>
		<li class="list-group-item"><strong>Beta:</strong><span class="pull-right"><?php echo $company->profile->beta ?></span></li>
		<li class="list-group-item"><strong>Volume Average:</strong><span class="pull-right"><?php echo $company->profile->volAvg ?></span></li>
		<li class="list-group-item"><strong>Market Cap:</strong><span class="pull-right"><?php echo $company->profile->mktCap ?></span></li>
		<li class="list-group-item"><strong>Last Dividend:</strong><span class="pull-right"><?php echo $company->profile->lastDiv ?></span></li>
		<li class="list-group-item text-center"><img src='<?php echo $company->profile->image ?>' alt='<?php echo $company->profile->companyName ?>'></li>
	</ul>
</aside>
<?php } ?>


</div><!-- #right-sidebar -->
