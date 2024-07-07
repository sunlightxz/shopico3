<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="clearfix form-group form-group-<?php echo esc_attr($key); ?> <?php echo esc_attr(!empty($field['toggle']) ? 'toggle-field' : ''); ?> <?php echo esc_attr(!empty($field['hide_field_content']) ? 'hide-content' : ''); ?>">
	<?php if ( !isset($field['show_title']) || $field['show_title'] ) { ?>
    	<label for="<?php echo esc_attr($name); ?>" class="heading-label">
    		<?php echo wp_kses_post($field['name']); ?>
    		<?php if ( !empty($field['toggle']) ) { ?>
                <i class="fas fa-angle-down"></i>
            <?php } ?>
    	</label>
    <?php } ?>
    <div class="form-group-inner">
		
		<?php
			$min_val = (!empty( $_GET[$name.'-from'] ) && $_GET[$name.'-from'] >= $min) ? $_GET[$name.'-from'] : $min;
			$max_val = (!empty( $_GET[$name.'-to'] ) && $_GET[$name.'-to'] <= $max) ? $_GET[$name.'-to'] : $max;

	    	
	    	$min_val_output = WP_Freeio_Price::format_price($min_val);
	    	$max_val_output = WP_Freeio_Price::format_price($max_val);
		    
	    ?>
	  	<div class="from-to-wrapper">
			<span class="inner">
				<span class="from-text"><?php echo $min_val_output; ?></span>
				<span class="space">-</span>
				<span class="to-text"><?php echo $max_val_output; ?></span>
			</span>
		</div>
		<div class="salary-range-slider" data-max="<?php echo esc_attr($max); ?>" data-min="<?php echo intval($min); ?>"></div>
	  	<input type="hidden" name="<?php echo esc_attr($name.'-from'); ?>" class="filter-from" value="<?php echo esc_attr($min_val); ?>">
	  	<input type="hidden" name="<?php echo esc_attr($name.'-to'); ?>" class="filter-to" value="<?php echo esc_attr($max_val); ?>">
	  </div>
</div><!-- /.form-group -->