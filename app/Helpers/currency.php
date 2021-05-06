<?php
/**
 * File function process currency
 * @author Naruto <lanhktc@gmail.com>
 */

use App\Models\Currency;
use SCart\Core\Front\Models\ShopCurrency;
// Format number with decimals
function decimalFormat($number = 0, $decimals = 2)
{
	return number_format((float)$number, $decimals, '.', '');
}
if (!function_exists('sc_currency_render')) {
    /**
     * Render currency: format number, change amount, add symbol
     *
     * @param   float  $money                 [$money description]
     * @param   [type] $currency              [$currency description]
     * @param   null   $rate                  [$rate description]
     * @param   null   $space_between_symbol  [$space_between_symbol description]
     * @param   false  $useSymbol             [$useSymbol description]
     * @param   true                          [ description]
     *
     * @return  [type]                        [return description]
     */
    function sc_currency_render(float $money, $currency = null, $rate = null, $space_between_symbol = false, $useSymbol = true)
    {
        return Currency::render($money, $currency, $rate, $space_between_symbol, $useSymbol);
    }
}

if (!function_exists('sc_currency_render_symbol')) {
    /**
     * Only render symbol, dont change amount
     *
     * @param   float  $money                 [$money description]
     * @param   [type] $currency              [$currency description]
     * @param   null   $space_between_symbol  [$space_between_symbol description]
     * @param   false  $include_symbol        [$include_symbol description]
     * @param   true                          [ description]
     *
     * @return  [type]                        [return description]
     */
    function sc_currency_render_symbol(float $money, $currency = null, $space_between_symbol = false, $include_symbol = true)
    {
        $currency = $currency ? $currency : sc_currency_code();
        return Currency::onlyRender($money, $currency, $space_between_symbol, $include_symbol);
    }
}


if (!function_exists('sc_currency_value')) {
/**
 * Get value of amount with specify exchange rate
 * if dont specify rate, will use exchange rate default
 *
 * @param   float  $money  [$money description]
 * @param   float  $rate   [$rate description]
 * @param   null           [ description]
 *
 * @return  [type]         [return description]
 */
    function sc_currency_value(float $money, float $rate = null)
    {
        return Currency::getValue($money, $rate);
    }
}

//Get code currency
if (!function_exists('sc_currency_code')) {
    function sc_currency_code()
    {
        return Currency::getCode();
    }
}

//Get rate currency
if (!function_exists('sc_currency_rate')) {
    function sc_currency_rate()
    {
        return Currency::getRate();
    }
}

//Format value without symbol
if (!function_exists('sc_currency_format')) {
    function sc_currency_format(float $money)
    {
        return Currency::format($money);
    }
}

//Get currency info
if (!function_exists('sc_currency_info')) {
    function sc_currency_info()
    {
        return Currency::getCurrency();
    }
}

//Get all currencies
if (!function_exists('sc_currency_all')) {
    function sc_currency_all()
    {
        return Currency::getListActive();
    }
}

//Get all currencies active
if (!function_exists('sc_currency_all_active')) {
    function sc_currency_all_active()
    {
        return Currency::getCodeActive();
    }
}
