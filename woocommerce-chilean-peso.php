<?php

/*
  Plugin Name: WooCommerce Chilean Peso
  Plugin URI: http://plugins.svn.wordpress.org/woocommerce-chilean-peso-currency/
  Description: This plugin add the chilean currency and symbol to WooCommerce. Este plugin agrega los Pesos Chilenos a WooCommerce.
  Version: 1.0
  Author: Cristian Tala Sánchez <cristian.tala@gmail.com>
  Author URI: http://www.cristiantala.cl
  License: GPLv3
  Requires at least: 3.0 +
  Tested up to: 3.3.1
 */
/*
 *      Copyright 2012 Cristian Tala Sánchez <cristian.tala@gmail.com>
 *
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 3 of the License, or
 *      (at your option) any later version.
 *
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */

function add_clp_currency($currencies) {
    $currencies["CLP"] = 'Pesos Chilenos';
    return $currencies;
}


function add_clp_currency_symbol( $currency_symbol, $currency ) {
     switch( $currency ) {
          case 'CLP': $currency_symbol = '$'; break;
     }
     return $currency_symbol;
}

add_filter('woocommerce_currencies', 'add_clp_currency', 10, 1);
add_filter('woocommerce_currency_symbol', 'add_clp_currency_symbol', 10, 2);

?>
