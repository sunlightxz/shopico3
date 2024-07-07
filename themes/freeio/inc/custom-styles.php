<?php
if ( !function_exists ('freeio_custom_styles') ) {
	function freeio_custom_styles() {
		global $post;	
		
		ob_start();	
		?>
		
			<?php if ( freeio_get_config('main_color') != "" ) {
				$main_color = freeio_get_config('main_color');
			} else {
				$main_color = '#5BBB7B';
			}
			if ( freeio_get_config('second_color') != "" ) {
				$second_color = freeio_get_config('second_color');
			} else {
				$second_color = '#1F4B3F';
			}

			if ( freeio_get_config('main_hover_color') != "" ) {
				$main_hover_color = freeio_get_config('main_hover_color');
			} else {
				$main_hover_color = '#43a062';
			}

			if ( freeio_get_config('second_hover_color') != "" ) {
				$second_hover_color = freeio_get_config('second_hover_color');
			} else {
				$second_hover_color = '#222222';
			}

			if ( freeio_get_config('text_color') != "" ) {
				$text_color = freeio_get_config('text_color');
			} else {
				$text_color = '#6B7177';
			}

			if ( freeio_get_config('link_color') != "" ) {
				$link_color = freeio_get_config('link_color');
			} else {
				$link_color = '#222222';
			}

			if ( freeio_get_config('link_hover_color') != "" ) {
				$link_hover_color = freeio_get_config('link_hover_color');
			} else {
				$link_hover_color = '#5BBB7B';
			}

			if ( freeio_get_config('heading_color') != "" ) {
				$heading_color = freeio_get_config('heading_color');
			} else {
				$heading_color = '#222222';
			}

			$main_color_rgb = freeio_hex2rgb($main_color);
			$second_color_rgb = freeio_hex2rgb($second_color);
			
			// font
			$main_font = freeio_get_config('main-font');
			$main_font = !empty($main_font) ? json_decode($main_font, true) : array();
			$main_font_family = !empty($main_font['fontfamily']) ? $main_font['fontfamily'] : 'DM Sans';
			$main_font_weight = !empty($main_font['fontweight']) ? $main_font['fontweight'] : 400;
			$main_font_size = !empty(freeio_get_config('main-font-size')) ? freeio_get_config('main-font-size').'px' : '15px';

			$main_font_arr = explode(',', $main_font_family);
			if ( count($main_font_arr) == 1 ) {
				$main_font_family = "'".$main_font_family."'";
			}
			
			$heading_font = freeio_get_config('heading-font');
			$heading_font = !empty($heading_font) ? json_decode($heading_font, true) : array();
			$heading_font_family = !empty($heading_font['fontfamily']) ? $heading_font['fontfamily'] : 'DM Sans';
			$heading_font_weight = !empty($heading_font['fontweight']) ? $heading_font['fontweight'] : 700;

			$heading_font_arr = explode(',', $heading_font_family);
			if ( count($heading_font_arr) == 1 ) {
				$heading_font_family = "'".$heading_font_family."'";
			}
			?>
			:root {
			  --freeio-theme-color: <?php echo trim($main_color); ?>;
			  --freeio-second-color: <?php echo trim($second_color); ?>;
			  --freeio-text-color: <?php echo trim($text_color); ?>;
			  --freeio-link-color: <?php echo trim($link_color); ?>;
			  --freeio-link_hover_color: <?php echo trim($link_hover_color); ?>;
			  --freeio-heading-color: <?php echo trim($heading_color); ?>;
			  --freeio-theme-hover-color: <?php echo trim($main_hover_color); ?>;
			  --freeio-second-hover-color: <?php echo trim($second_hover_color); ?>;

			  --freeio-main-font: <?php echo trim($main_font_family); ?>;
			  --freeio-main-font-size: <?php echo trim($main_font_size); ?>;
			  --freeio-main-font-weight: <?php echo trim($main_font_weight); ?>;
			  --freeio-heading-font: <?php echo trim($heading_font_family); ?>;
			  --freeio-heading-font-weight: <?php echo trim($heading_font_weight); ?>;

			  --freeio-theme-color-005: <?php echo freeio_generate_rgba($main_color_rgb, 0.05); ?>
			  --freeio-theme-color-007: <?php echo freeio_generate_rgba($main_color_rgb, 0.07); ?>
			  --freeio-theme-color-010: <?php echo freeio_generate_rgba($main_color_rgb, 0.1); ?>
			  --freeio-theme-color-015: <?php echo freeio_generate_rgba($main_color_rgb, 0.15); ?>
			  --freeio-theme-color-020: <?php echo freeio_generate_rgba($main_color_rgb, 0.2); ?>
			  --freeio-second-color-050: <?php echo freeio_generate_rgba($second_color_rgb, 0.5); ?>
			}
			
			<?php if (  freeio_get_config('header_mobile_color') != "" ) : ?>
				#apus-header-mobile {
					background-color: <?php echo esc_html( freeio_get_config('header_mobile_color') ); ?>;
				}
			<?php endif; ?>

	<?php
		$content = ob_get_clean();
		$content = str_replace(array("\r\n", "\r"), "\n", $content);
		$lines = explode("\n", $content);
		$new_lines = array();
		foreach ($lines as $i => $line) {
			if (!empty($line)) {
				$new_lines[] = trim($line);
			}
		}
		
		return implode($new_lines);
	}
}