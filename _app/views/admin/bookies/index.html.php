<h2>Bookies
	<small class="clearfix reset help">Use this list to order the bookies by priority (drag / drop) and to enable / disable mobile bookies.</small>
	<small id="status" class="clearfix reset"></small>
</h2>

<ul id="bookies">
	<li class="header round_corners grid_16">
		<div class="bookie_info grid_5">Bookie</div>
		<div class="bookie_murl grid_8">Mobile site URL</div>
		<div class="bookie_murl grid_2">Live</div>
	</li>
<? foreach ( $bookies as $bookie ): ?>
	<li class="grid_16" id="bookie_<?=$bookie['providerid']?>">
		<div class="bookie_info grid_5">
			<?=html_image_tag( array( 'src' => $bookie['logo'], 'alt' => $bookie['bookmaker'], 'height' => '16' ) )?>
			<label><?=$bookie['bookmaker']?></label>
		</div>
		<div class="bookie_murl grid_8"><?=html_a_tag( $bookie['murl'], array( 'href' => $bookie['murl'], 'target' => '_blank', 'title' => 'Check link in a new window' ) )?></div>
		<div class="actions grid_2"><input type="checkbox" id="chk_<?=$bookie['providerid']?>" data-bookieid="<?=$bookie['providerid']?>" class="grid_1 live_bookie" <?=( $bookie['live_mobile'] ? 'checked' : '' )?> /></div>
	</li>
<? endforeach; ?>
</ul>

<div class="bookie_info grid_5" style="padding: 4px; margin-top: 10px;"><a href="../odds/best" target="_blank" style="color: #333; font-weight: bold;">Regenerate best odds data</a></div>