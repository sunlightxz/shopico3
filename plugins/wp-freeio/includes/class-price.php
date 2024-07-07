<?php
/**
 * Price
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_Price {
	
	/**
	 * Formats price
	 *
	 * @access public
	 * @param $price
	 * @return bool|string
	 */
	public static function format_price( $price, $show_null = false, $symbol = '' ) {
		if (class_exists('WooCommerce')) {
			if(function_exists('wmc_get_price')){
				$price = wc_price( wmc_get_price( $price ) ); //WooCommerce Multi Currency Compatibility
			} else {
				$price = wc_price($price);
			}

			return $price;
		}

		if ( empty( $price ) || ! is_numeric( $price ) ) {
			if ( !$show_null ) {
				return false;
			}
			$price = 0;
		}
		$decimals = false;
		$money_decimals = '';

		if ( !$symbol ) {
			$symbol = self::currency_symbol();
		}
		$currency_symbol = ! empty( $symbol ) ? '<span class="suffix">'.$symbol.'</span>' : '<span class="suffix">$</span>';

		$price = WP_Freeio_Mixes::format_number( $price, $decimals, $money_decimals );

		$price = '<span class="price-text">'.$price.'</span> ' . $currency_symbol;

		return $price;
	}

	public static function format_price_without_html( $price, $show_null = false ) {

		if (class_exists('WooCommerce')) {

			if(function_exists('wmc_get_price')){
				$price = wc_price( wmc_get_price( $price ) ); //WooCommerce Multi Currency Compatibility
			} else {
				$price = wc_price($price);
			}

			return strip_tags($price);
		}
		
		$return = $price.' '.self::currency_symbol();

		return $return;
	}

	public static function get_current_currency() {
		if (class_exists('WooCommerce')) {
			$current_currency = get_woocommerce_currency();
		} else{
			$current_currency	= 'USD';
		}
		return $current_currency;
	}

	/**
	 * Get Currency symbol.
	 *
	 * Currency symbols and names should follow the Unicode CLDR recommendation (http://cldr.unicode.org/translation/currency-names)
	 *
	 * @param string $currency Currency. (default: '').
	 * @return string
	 */
	public static function currency_symbol() {
		if (class_exists('WooCommerce')) {
			$currency_symbol = get_woocommerce_currency_symbol();
		} else{
			$currency_symbol	= '$';
		}

		return apply_filters( 'wp-freeio-currency-symbol', $currency_symbol );
	}
	
}
