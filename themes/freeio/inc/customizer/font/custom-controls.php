<?php
/**
 * Freeio Customizer Custom Controls
 *
 */

if ( class_exists( 'WP_Customize_Control' ) ) {
	
	class Freeio_Google_Font_Select_Custom_Control extends WP_Customize_Control {
		/**
		 * The type of control being rendered
		 */
		public $type = 'google_fonts';
		/**
		 * The list of Google Fonts
		 */
		private $fontList = false;
		/**
		 * The saved font values decoded from json
		 */
		private $fontValues = [];

		/**
		 * Get our list of fonts from the json file
		 */
		public function __construct( $manager, $id, $args = array(), $options = array() ) {
			parent::__construct( $manager, $id, $args );

			
			$this->fontList = $this->getGoogleFonts( );
			// Decode the default json font value
			$this->fontValues = json_decode( $this->value() );

		}
		/**
		 * Enqueue our scripts and styles
		 */
		public function enqueue() {
			wp_enqueue_script( 'freeio-select2-js', get_template_directory_uri() . '/inc/customizer/js/select2.full.min.js', array( 'jquery' ), '4.0.13', true );
			wp_enqueue_script( 'freeio-customizer', get_template_directory_uri() . '/inc/customizer/js/customizer.js', array( 'freeio-select2-js' ), '1.0', true );
			wp_enqueue_style( 'freeio-customizer', get_template_directory_uri() . '/inc/customizer/css/customizer.css', array(), '1.1', 'all' );
			wp_enqueue_style( 'freeio-select2-css', get_template_directory_uri() . '/inc/customizer/css/select2.min.css', array(), '4.0.13', 'all' );
		}
		/**
		 * Export our List of Google Fonts to JavaScript
		 */
		public function to_json() {
			parent::to_json();
			$this->json['freeiofontslist'] = $this->fontList;
		}
		/**
		 * Render the control in the customizer
		 */
		public function render_content() {

			if( !empty($this->fontList) ) {
				?>
				<div class="google_fonts_select_control">

					<?php if( !empty( $this->label ) ) { ?>
						<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
					<?php } ?>
					<?php if( !empty( $this->description ) ) { ?>
						<span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
					<?php } ?>
					<input type="hidden" id="<?php echo esc_attr( $this->id ); ?>" name="<?php echo esc_attr( $this->id ); ?>" value="<?php echo esc_attr( $this->value() ); ?>" class="customize-control-google-font-selection" <?php $this->link(); ?> />
					<div class="google-fonts">
						<select class="google-fonts-list" control-name="<?php echo esc_attr( $this->id ); ?>" data-placeholder="<?php esc_attr_e('Choose a font', 'freeio'); ?>">
							<?php
								foreach( $this->fontList as $key => $value ) {
									echo '<option value="' . $key . '" ' . selected( $this->fontValues->fontfamily, $key, false ) . '>' . $key . '</option>';
								}
							?>
						</select>
					</div>
					<div class="customize-control-description"><?php esc_html_e( 'Select weight & style for regular text', 'freeio' ) ?></div>
					<div class="weight-style">
						<select class="google-fonts-fontweight-style">
							<option></option>
							<?php
								if ( !empty($this->fontList[$this->fontValues->fontfamily]['variants']) ) {
									foreach( $this->fontList[$this->fontValues->fontfamily]['variants'] as $key => $value ) {
										echo '<option value="' . $value['id'] . '" ' . selected( $this->fontValues->fontweight, $value['id'], false ) . '>' . $value['name'] . '</option>';
									}
								}
							?>
						</select>
					</div>

					<div class="customize-control-description"><?php esc_html_e( 'Font Subsets', 'freeio' ) ?></div>
					<div class="weight-style">
						<select class="google-fonts-subsets-style">
							<option></option>
							<?php
								if ( !empty($this->fontList[$this->fontValues->fontfamily]['subsets']) ) {
									foreach( $this->fontList[$this->fontValues->fontfamily]['subsets'] as $key => $value ) {
										echo '<option value="' . $value['id'] . '" ' . selected( $this->fontValues->subsets, $value['id'], false ) . '>' . $value['name'] . '</option>';
									}
								}
							?>
						</select>
					</div>
					<input type="hidden" class="google-fonts-category" value="<?php echo esc_attr($this->fontValues->category); ?>">

				</div>
				<?php
			}
		}

		/**
		 * Return the list of Google Fonts from our json file. Unless otherwise specfied, list will be limited to 30 fonts.
		 */
		public function getGoogleFonts() {
			$fonts = require dirname(__FILE__) . '/googlefonts.php';

			return $fonts;
		}
	}

	/**
	 * Google Font sanitization
	 *
	 * @param  string	JSON string to be sanitized
	 * @return string	Sanitized input
	 */
	if ( ! function_exists( 'freeio_google_font_sanitization' ) ) {
		function freeio_google_font_sanitization( $input ) {
			$val =  json_decode( $input, true );
			if( is_array( $val ) ) {
				foreach ( $val as $key => $value ) {
					$val[$key] = sanitize_text_field( $value );
				}
				$input = json_encode( $val );
			}
			else {
				$input = json_encode( sanitize_text_field( $val ) );
			}
			return $input;
		}
	}


}
