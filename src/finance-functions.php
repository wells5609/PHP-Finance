<?php
/**
 * Financial functions
 */

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
	$x = percent_change_array($asset_prices);
	$y = percent_change_array($market_prices);
	
	// calculate residual sum of squares
	$ss_res = sumofsquares($y, $x);
	// calculate total sum of squares
	$ss_tot = sumofsquares($y);
	// calculate R2
	$rsquared = floatval( 1 - ($ss_res/$ss_tot) );
	
	return covariance($x, $y)/variance($y);
}

/**
 * Calculate the adjusted beta.
 * 
 * Simply multiplies raw beta by 2/3 and adds 1/3.
 * 
 * @param float $rawbeta Raw beta.
 * @return float Adjusted beta.
 */
function beta_adjusted($rawbeta) {
	return floatval( ((2/3) * $rawbeta) + (1/3) );
}

/**
 * Returns an array of present values for the given series of cashflows.
 * 
 * @param array $cashflows Indexed array of cashflows ordered from oldest to newest.
 * @param float $required_return Required return, often cost of equity or WACC.
 * @return array Array of cashflow present values.
 */
function pv_cashflows(array $cashflows, $required_return) {
	$r = floatval($required_return);
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
	$g = floatval($growth_rate);
	$cashflows = array(floatval($first_cashflow));
	foreach(range(1, $num_years) as $year) {
		$cashflows[$year] = $cashflows[$year-1]*(1+$g);
	}
	return $cashflows;
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
	$g = floatval($terminal_growth_rate);
	return floatval($cashflow)*(1+$g)/(floatval($required_return)-$g);
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
 * @param float $terminal_growth_rate The rate at which cashflows grow for infinity.
 * @param float $required_return The required return, often the cost of equity or WACC.
 * @param float|null $terminal_cashflow Set by reference to the future value of the terminal cash flow.
 * @return float Present value of the terminal value.
 */
function terminal_pv($cashflow, $period, $terminal_growth_rate, $required_return, &$terminal_cashflow = null) {
	$terminal_cashflow = terminal_cashflow($cashflow, $terminal_growth_rate, $required_return);
	return pv($terminal_cashflow, floatval($required_return), intval($period));
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
	return floatval($rM)-floatval($riskfree_rate);
}

/**
 * Returns a firm's effective tax rate.
 * 
 * @param float $tax_expense Provision for income taxes.
 * @param float $pretax_income Income before taxes.
 * @return float Effective tax rate.
 */
function tax_rate($tax_expense, $pretax_income) {
	return percent($tax_expense, $pretax_income);
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
	return floatval($riskfree_rate+$credit_spread);
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
	return floatval($riskfree_rate)+($beta*floatval($risk_prem));
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
	return floatval($terminal_growth_rate+($dividend/$price));
}

/**
 * Returns the return required on a firm's debt.
 * 
 * @param float $interest_expense Interest expense payments for the most recent year.
 * @param float $avg_tot_debt Average total debt of the firm over the year.
 */
function debt_required_return($interest_expense, $avg_tot_debt) {
	return percent($interest_expense, $avg_tot_debt);
}

/**
 * Calculate a security's earnings-per-share.
 * 
 * @param float $net_income Net income available to common shareholders.
 * @param float $shares_oustanding
 * @param float $preferred_dividends
 * @return float Earnings per share
 */
function finance_eps($net_income, $shares_outstanding, $preferred_dividends = 0) {
	return floatval(($net_income-$preferred_dividends)/$shares_outstanding);
}

/* ================================
			Leverage 
 ================================ */

/**
 * Calculate interest burden.
 * 
 * @param float $ebit Earnings before interest and taxes.
 * @param float $interest_expense Interest expense.
 * @return float Interest burden
 */
function finance_interest_burden($ebit, $interest_expense) {
	return floatval(($ebit-$interest_expense)/$ebit);
}

/**
 * Calculate times interest earned (interest coverage).
 * 
 * @param float $ebit
 * @param float $interest_expense
 * @return float Times interest earned
 */
function finance_times_interest_earned($ebit, $interest_expense) {
	return floatval($ebit/$interest_expense);
}

/**
 * Calculate leverage.
 * 
 * @param float $assets_or_debt Total assets or debt. If debt, set 3rd param = true.
 * @param float $equity Total shareholder equity.
 * @param boolean $is_debt Whether argument 1 is debt. Default false.
 * @return float Leverage
 */
function finance_leverage($assets_or_debt, $equity, $is_debt = false) {
	if ($is_debt) {
		return 1+floatval($assets_or_debt/$equity);
	}
	return floatval($assets_or_debt/$equity);
}

/* ====================================
			Asset Utilization 
 =================================== */

/**
 * Calculate total asset turnover.
 * 
 * @param float $revenue Sales revenue.
 * @param float $avg_tot_assets Average total assets over the period.
 * @return float Total asset turnover.
 */
function finance_asset_turnover($revenue, $avg_tot_assets) {
	return floatval($revenue/$avg_tot_assets);
}

/**
 * Calculate inventory turnover.
 * 
 * @param float $cogs Cost of goods sold (cost of revenue).
 * @param float $avg_inventory Average inventory over the period.
 * @return float Total inventory turnover.
 */
function finance_inventory_turnover($cogs, $avg_inventory) {
	return floatval($cogs/$avg_inventory);
}

/**
 * Calculate days receivable.
 * 
 * @param float $avg_receivables Average accounts receivables over the period.
 * @param float $revenue Sales revenue.
 * @return float Days receivables
 */
function finance_days_receivable($avg_receivables, $revenue) {
	return floatval($avg_receivables/$revenue)*365;
}

/* ================================
			Liquidity
 =============================== */

/**
 * Calculate current ratio.
 * 
 * @param float $curr_assets Current assets.
 * @param float $curr_liabilities Current liabilities.
 * @return float Current ratio.
 */
function finance_current_ratio($curr_assets, $curr_liabilities) {
	return floatval($curr_assets/$curr_liabilities);
}

/**
 * Calculate quick ratio.
 * 
 * @param float $cash Cash and cash equivalents.
 * @param float $marketable_securities Marketable securities.
 * @param float $receivables Accounts receivable.
 * @param float $curr_liabilities Current liabilities.
 * @return float Quick ratio.
 */
function finance_quick_ratio($cash, $marketable_securities, $receivables, $curr_liabilities) {
	return floatval(($cash+$marketable_securities+$receivables)/$curr_liabilities);
}

/* ================================
			Profitability
 =============================== */

/**
 * Calculate return on assets.
 * 
 * @param float $ebit Earnings before interest and taxes.
 * @param float $avg_tot_assets Average total assets over the period.
 * @return float Return on assets.
 */
function finance_roa($ebit, $avg_tot_assets) {
	return floatval($ebit/$avg_tot_assets);
}

/**
 * Calculate return on equity.
 * 
 * @param float $net_income Net income.
 * @param float $avg_shareholder_equity Average shareholder equity over the period.
 * @return float Return on equity.
 */
function finance_roe($net_income, $avg_shareholder_equity) {
	return floatval($net_income/$avg_shareholder_equity);
}

/* ====================================
			Market Price
 =================================== */

/**
 * Calculate a stock's price-to-earnings ratio.
 * 
 * @param float $price
 * @param float $eps
 * @return float Price-to-earnings ratio
 */
function finance_pe($price, $eps) {
	return floatval($price/$eps);
}

/**
 * Calculate a stock's price-to-sales ratio.
 * 
 * Method A: Marketcap / Revenue
 * Method B: Share price / Revenue per share
 * 
 * @param float $marketcap Market cap, or stock price if using method B.
 * @param float $revenue Revenue, or revenue per share if using method B.
 * @return float Price-to-sales ratio
 */
function finance_ps($marketcap, $revenue) {
	return floatval($marketcap/$revenue);
}

/**
 * Calculate a stock's price-to-book ratio.
 * 
 * @param float $price
 * @param float $book_value
 * @return float Price-to-book ratio
 */
function finance_pb($price, $book_value) {
	return floatval($price/$book_value);
}
 
/**
 * Calculate earnings yield.
 * 
 * @param float $eps Earnings per share.
 * @param float $price Current stock price.
 * @return float Earnings yield.
 */
function finance_earnings_yield($eps, $price) {
	return floatval($eps/$price);
}

/**
 * Calculate free cash flow yield.
 * 
 * @param float $oper_cashflow Operating cash flow.
 * @param float $capex Capital expenditures.
 * @param float $marketcap Market capitalization.
 * @return float Free cash flow yield.
 */
function finance_fcf_yield($oper_cashflow, $capex, $marketcap) {
	return floatval(($oper_cashflow-$capex)/$marketcap);
}
