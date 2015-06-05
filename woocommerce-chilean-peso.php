<?php

/*
  Plugin Name: WooCommerce Chilean Peso + Chilean States
  Plugin URI: http://plugins.svn.wordpress.org/woocommerce-chilean-peso-currency/
  Description: This plugin enables the payment with paypal for Chile and the Chilean states to WooCommerce.
  Version: 2.5.5
  Author: Cristian Tala Sánchez <cristian.tala@gmail.com>
  Author URI: http://www.cristiantala.cl
  License: GPLv3
  Requires at least: 3.0 +
  Tested up to: 4.2
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
register_activation_hook(__FILE__, 'ctala_install_cleancache');

/*
 * Esta funcion limpia el cache luego de instalar la nueva versión del plugin.
 */
function ctala_install_cleancache() {

    wp_cache_delete("clp_usd_ctala", "ctala");
}

function add_clp_currency($currencies) {

    $currencies["CLP"] = 'Pesos Chilenos';
    return $currencies;
}

function add_clp_currency_symbol($currency_symbol, $currency) {
    switch ($currency) {
        case 'CLP': $currency_symbol = '$';
            break;
    }
    return $currency_symbol;
}

function custom_woocommerce_states($states) {

    $states['CL'] = array(
        'I' => 'I de Tarapacá',
        'II' => 'II de Antofagasta',
        'III' => 'III de Atacama',
        'IV' => 'IV de Coquimbo',
        'V' => 'V de Valparaíso',
        'VI' => 'VI del Libertador General Bernardo O\'Higgins',
        'VII' => 'VII del Maule',
        'VIII' => 'VIII del Bío Bío',
        'IX' => 'IX de la Araucanía',
        'XIV' => 'XIV de los Ríos',
        'X' => 'X de los Lagos',
        'XI' => 'XI Aysén del General Carlos Ibáñez del Campo',
        'XII' => 'XII de Magallanes y Antártica Chilena',
        'RM' => 'Metropolitana de Santiago',
        'XV' => 'XV de Arica y Parinacota',
    );

    return $states;
}

function add_clp_paypal_valid_currency($currencies) {
    array_push($currencies, 'CLP');
    return $currencies;
}

/*
 * Hace el cambio del valor a dolares a través de OpenExchange
 * Se necesita tener curl instalado para que esto funcione.
 */

function convert_clp_to_usd($paypal_args) {
    //Grupo para el cache
    $ctala_group = "ctala";
//Segundos en una semana a cachear el valor.
    $ctala_expire = 604800;

    if ($paypal_args['currency_code'] == 'CLP') {

        $valorDolar = wp_cache_get('clp_usd_ctala', $ctala_group);
        if (false === $valorDolar) {
            if (function_exists('curl_version')) {
                $file = 'latest.json';
                $appId = '3d2f0769c4fc42278faffd3933a23dd6';
                $url = "http://openexchangerates.org/api/$file?app_id=$appId";
// Open CURL session:
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Get the data:
                $json = curl_exec($ch);
                curl_close($ch);

// Decode JSON response:
                $exchangeRates = json_decode($json);
                $valorDolar = $exchangeRates->rates->CLP;
            } else {
                $valorDolar = 630; // Este es el valor por defecto. 
            }
            wp_cache_set('clp_usd_ctala', $valorDolar, $ctala_group, $ctala_expire);
        }

        $convert_rate = $valorDolar; //set the converting rate
        $paypal_args['currency_code'] = 'USD'; //change CLP to USD
        $i = 1;

        while (isset($paypal_args['amount_' . $i])) {
            $paypal_args['amount_' . $i] = round($paypal_args['amount_' . $i] / $convert_rate, 2);
            ++$i;
        }
    }
    return $paypal_args;
}

// Se eliminan los datos postales como obligatorios.
function postalcode_override_default_address_fields($address_fields) {
    $address_fields['postcode']['required'] = false;

    return $address_fields;
}

//Se eliminan los códigos postales como obligatorios. (Filtro)
add_filter('woocommerce_default_address_fields', 'postalcode_override_default_address_fields');

add_filter('woocommerce_paypal_args', 'convert_clp_to_usd');
add_filter('woocommerce_states', 'custom_woocommerce_states');
add_filter('woocommerce_currencies', 'add_clp_currency', 10, 1);
add_filter('woocommerce_currency_symbol', 'add_clp_currency_symbol', 10, 2);
add_filter('woocommerce_paypal_supported_currencies', 'add_clp_paypal_valid_currency');
?>
