<?php

/**
 * Sets risk-free rate to use as the default rF in all calculations.
 * 
 * @param float $rate Risk-free rate.
 */
function set_risk_free_rate($rate) {
	define('FINANCE_RISK_FREE_RATE', floatval($rate));
}

/**
 * Returns the risk-free rate.
 * 
 * Value of FINANCE_RISK_FREE_RATE constant, if defined, otherwise 0.0275 (2.75%).
 * 
 * @return float Risk-free rate.
 */
function risk_free_rate() {
	return defined('FINANCE_RISK_FREE_RATE') ? FINANCE_RISK_FREE_RATE : 0.0275;
}

/**
 * Include math functions (e.g. stddev(), variance(), mean(), etc.)
 */
require __DIR__.'/math-functions.php';

/**
 * Include finance functions (e.g. beta(), build_cashflows(), finance_eps(), etc.)
 */
require __DIR__.'/finance-functions.php';
