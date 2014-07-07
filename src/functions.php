<?php
/**
 * Financial functions
 */

/**
 * Sets risk-free rate to use as the default rF in all calculations.
 * 
 * @param string $rate Risk-free rate.
 */
function set_risk_free_rate($rate) {
	define('FINANCE_RISK_FREE_RATE', strval($rate));
}

/**
 * Returns the risk-free rate.
 * 
 * Value of FINANCE_RISK_FREE_RATE constant, if defined, otherwise 0.0275 (2.75%).
 * 
 * @return string Risk-free rate.
 */
function risk_free_rate() {
	return defined('FINANCE_RISK_FREE_RATE') ? FINANCE_RISK_FREE_RATE : "0.0275";
}

/**
 * Calculate beta.
 * 
 * @param array $asset_prices Indexed array of ordered asset prices.
 * @param array $market_prices Indexed array of benchmark prices.
 * @param null &$rsquared Sets the R2 (coefficient of determination) by reference.
 * @return float Beta = cov(x,y)/var(y)
 */
function beta($asset_prices, $market_prices, &$rsquared = null) {
	
	// convert prices to % change
	$x = pct_change_array($asset_prices);
	$y = pct_change_array($market_prices);
	
	// calculate R2 = Residual SS / Total SS
	$rsquared = strval(1 - (sos($y, $x) / sos($y, mean($y))));
	
	return (string) round(covariance($x, $y)/variance($y), 3);
}

/**
 * Adjusts a raw beta to an "adjusted" beta.
 * 
 * Simply multiplies raw beta by 2/3 and adds 1/3.
 * 
 * @param float $rawbeta Raw beta.
 * @return float Adjusted beta.
 */
function adjbeta($rawbeta) {
	return (string) (2/3 * $rawbeta) + (1/3);
}

/**
 * Returns an array of present values for the given series of cashflows.
 * 
 * @param array $cashflows Indexed array of cashflows ordered from oldest to newest.
 * @param float $required_return Required return, often cost of equity or WACC.
 * @return array Array of cashflow present values.
 */
function pv_cashflows(array $cashflows, $required_return) {
	$r = (string) $required_return;
	$pv_cashflows = array();
	foreach($cashflows as $year => $flo) {
		if (0 != $year) {
			$pv_cashflows[$year] = pv($flo, $r, $year);
		}
	}
	return $pv_cashflows;
}

/**
 * Builds an array of cash flows from an initial value.
 * 
 * @param float $first_cashflow Initial cash flow from which others are projected.
 * @param int $num_years The number of years to grow the cash flows.
 * @param float $growth_rate The annual growth rate of the cash flows.
 * @return array Array of cashflows, with indexes corresponding to the year in which the CF occurs.
 */
function build_cashflows($first_cashflow, $num_years, $growth_rate) {
	$g = (string) $growth_rate;
	$cashflows = array((string)$first_cashflow);
	foreach(range(1, $num_years) as $year) {
		$cashflows[$year] = $cashflows[$year-1]*(1+$g);
	}
	return array_map('strval', $cashflows);
}

/**
 * Calculates the terminal cash flow from a final value.
 * 
 * @param float $cashflow The cashflow that occurs in the last year before terminal growth.
 * @param float $terminal_growth_rate THe rate at which cash flows grow for infinity.
 * @param float $required_return The required return, often cost of equity or WACC.
 * @return float The FUTURE value of the terminal cash flow.
 */
function terminal_cashflow($cashflow, $terminal_growth_rate, $required_return) {
	$g = (string) $terminal_growth_rate;
	return strval($cashflow*(1+$g)/((string)$required_return-$g));
}

/**
 * Returns the present value of a cashflow that occurs in the terminal year.
 * 
 * The cashflow should be given as the normal cash flow that would occur
 * in the terminal year (last year of projected growth). 
 * 
 * The future value of the terminal cash flow will be calculated and then 
 * discounted to the present at the required rate of return.
 * 
 * @param float $cashflow Cashflow that occurs the last year before terminal growth.
 * @param float $period Integer representing the period when terminal growth begins.
 * @param float $term_growth_rate Terminal rate at which cashflows grow for infinity.
 * @param float $reqd_return The required return, often the cost of equity or WACC.
 * @param float|null $term_cashflow Set by reference to the future value of the terminal cash flow.
 * @return float Present value of the terminal value.
 */
function terminal_pv($cashflow, $period, $term_growth_rate, $reqd_return, &$term_cashflow = null) {
	$term_cashflow = terminal_cashflow($cashflow, $term_growth_rate, $reqd_return);
	return pv($term_cashflow, (string)$reqd_return, (int)$period);
}

/**
 * Returns the premium for an asset over the risk-free rate.
 * 
 * Simply the given rate of return (e.g. market) minus the risk-free rate.
 * 
 * @param float $rM Rate of return - for market risk premium, this is the market return.
 * @param float $riskfree_rate [Optional]
 * @return float Risk premium
 */
function risk_premium($rM, $riskfree_rate = null) {
	if (! isset($riskfree_rate)) {
		$riskfree_rate = risk_free_rate();
	}
	return strval($rM)-strval($riskfree_rate);
}

/**
 * Returns a firm's effective tax rate.
 * 
 * @param float $tax_expense Provision for income taxes.
 * @param float $pretax_income Income before taxes.
 * @return float Effective tax rate.
 */
function tax_rate($tax_expense, $pretax_income) {
	return pct($tax_expense, $pretax_income);
}

/**
 * Returns a company's cost of debt, given its credit spread.
 * 
 * Credit spread is the difference between the interest rate that the
 * company pays on debt and the risk-free rate.
 * 
 * @param float $credit_spread
 * @param float $riskfree_rate [Optional]
 * @return float Cost of debt
 */
function cost_of_debt($credit_spread, $riskfree_rate = null) {
	if (! isset($riskfree_rate)) {
		$riskfree_rate = risk_free_rate();
	}
	return strval($riskfree_rate)+strval($credit_spread);
}

/**
 * Returns the cost of equity ("required return") for a risky asset using CAPM.
 * 
 * @param float $beta The asset's beta.
 * @param float $risk_prem Market risk premium.
 * @param float $riskfree_rate [Optional] The risk-free rate (e.g. 0.03)
 * @return float Cost of equity per CAPM as float (e.g. 0.135 = 13.5%)
 */
function capm_cost_of_equity($beta, $risk_prem, $riskfree_rate = null) {
	if (! isset($riskfree_rate)) {
		$riskfree_rate = risk_free_rate();
	}
	return strval((string)$riskfree_rate+($beta*(string)$risk_prem));
}

/**
 * Returns the required return given a security's price, dividend, and terminal growth rate.
 * 
 * Often used for cost of equity in dividend discount model.
 * 
 * @param float $price Security's current market price.
 * @param float $dividend Security's current (annual) dividend.
 * @param float $terminal_growth_rate Expected terminal growth rate.
 * @return float Required return = Terminal growth rate + (Dividend / Price)
 */
function ddm_required_return($price, $dividend, $terminal_growth_rate) {
	return (string) $terminal_growth_rate+($dividend/$price);
}

/**
 * Returns the return required on a firm's debt.
 * 
 * @param float $interest_expense Interest expense payments for the most recent year.
 * @param float $avg_tot_debt Average total debt of the firm over the year.
 * @return float The required return on the firm's debt.
 */
function required_return_on_debt($interest_expense, $avg_tot_debt) {
	return pct($interest_expense, $avg_tot_debt);
}
