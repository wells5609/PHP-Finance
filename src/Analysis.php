<?php

namespace Finance;

class Analysis {
	
	/**
	 * Calculate a security's earnings-per-share.
	 * 
	 * @param float $net_income Net income available to common shareholders.
	 * @param float $shares_oustanding
	 * @param float $preferred_dividends
	 * @return string Earnings per share
	 */
	public static function EPS($net_income, $shares_outstanding, $preferred_dividends = 0) {
		return strval(($net_income-$preferred_dividends)/$shares_outstanding);
	}
	
	/* ================================
				Profitability
	 =============================== */
	
	/**
	 * Calculate return on assets.
	 * 
	 * @param float $ebit Earnings before interest and taxes.
	 * @param float $avg_tot_assets Average total assets over the period.
	 * @return string Return on assets.
	 */
	public static function ROA($ebit, $avg_tot_assets) {
		return strval($ebit/$avg_tot_assets);
	}
	
	/**
	 * Calculate return on equity.
	 * 
	 * @param float $net_income Net income.
	 * @param float $avg_shareholder_equity Average shareholder equity over the period.
	 * @return string Return on equity.
	 */
	public static function ROE($net_income, $avg_shareholder_equity) {
		return strval($net_income/$avg_shareholder_equity);
	}
	
	/* ================================
				Leverage 
	 ================================ */
	
	/**
	 * Calculate interest burden.
	 * 
	 * @param float $ebit Earnings before interest and taxes.
	 * @param float $interest_expense Interest expense.
	 * @return string Interest burden
	 */
	public static function intBurden($ebit, $interest_expense) {
		return strval(($ebit-$interest_expense)/$ebit);
	}
	
	/**
	 * Calculate times interest earned (interest coverage).
	 * 
	 * @param float $ebit
	 * @param float $interest_expense
	 * @return string Times interest earned
	 */
	public static function timesIntEarned($ebit, $interest_expense) {
		return strval($ebit/$interest_expense);
	}
	
	/**
	 * Calculate leverage.
	 * 
	 * @param float $assets_or_debt Total assets or debt. If debt, set 3rd param = true.
	 * @param float $equity Total shareholder equity.
	 * @param boolean $is_debt Whether argument 1 is debt. Default false.
	 * @return string Leverage
	 */
	public static function leverage($assets_or_debt, $equity, $is_debt = false) {
		if ($is_debt) {
			return strval(1 + $assets_or_debt/$equity);
		}
		return strval($assets_or_debt/$equity);
	}
	
	/* ====================================
				Asset Utilization 
	 =================================== */
	
	/**
	 * Calculate total asset turnover.
	 * 
	 * @param float $revenue Sales revenue.
	 * @param float $avg_tot_assets Average total assets over the period.
	 * @return string Total asset turnover.
	 */
	public static function assetTurnover($revenue, $avg_tot_assets) {
		return strval($revenue/$avg_tot_assets);
	}
	
	/**
	 * Calculate inventory turnover.
	 * 
	 * @param float $cogs Cost of goods sold (cost of revenue).
	 * @param float $avg_inventory Average inventory over the period.
	 * @return string Total inventory turnover.
	 */
	public static function invTurnover($cogs, $avg_inventory) {
		return strval($cogs/$avg_inventory);
	}
	
	/**
	 * Calculate days receivable.
	 * 
	 * @param float $avg_receivables Average accounts receivables over the period.
	 * @param float $revenue Sales revenue.
	 * @return string Days receivables
	 */
	public static function daysReceivable($avg_receivables, $revenue) {
		return strval($avg_receivables/$revenue*365);
	}
	
	/* ================================
				Liquidity
	 =============================== */
	
	/**
	 * Calculate current ratio.
	 * 
	 * @param float $curr_assets Current assets.
	 * @param float $curr_liabilities Current liabilities.
	 * @return string Current ratio.
	 */
	public static function currentRatio($curr_assets, $curr_liabilities) {
		return strval($curr_assets/$curr_liabilities);
	}
	
	/**
	 * Calculate quick ratio.
	 * 
	 * @param float $cash Cash and cash equivalents.
	 * @param float $marketable_securities Marketable securities.
	 * @param float $receivables Accounts receivable.
	 * @param float $curr_liabilities Current liabilities.
	 * @return string Quick ratio.
	 */
	public static function quickRatio($cash, $marketable_securities, $receivables, $curr_liabilities) {
		return strval(($cash+$marketable_securities+$receivables)/$curr_liabilities);
	}
	
	/* ====================================
				Market Price
	 =================================== */
	
	/**
	 * Calculate price-to-earnings ratio.
	 * 
	 * @param float $price
	 * @param float $eps
	 * @return string Price-to-earnings ratio
	 */
	public static function PE($price, $eps) {
		return strval($price/$eps);
	}
	
	/**
	 * Calculate price-to-sales ratio.
	 * 
	 * Method A: Marketcap / Revenue
	 * Method B: Share price / Revenue per share
	 * 
	 * @param float $marketcap Market cap, or stock price if using method B.
	 * @param float $revenue Revenue, or revenue per share if using method B.
	 * @return string Price-to-sales ratio
	 */
	public static function PS($marketcap, $revenue) {
		return strval($marketcap/$revenue);
	}
	
	/**
	 * Calculate price-to-book ratio.
	 * 
	 * @param float $price
	 * @param float $book_value
	 * @return string Price-to-book ratio
	 */
	public static function PB($price, $book_value) {
		return strval($price/$book_value);
	}
	 
	/**
	 * Calculate earnings yield.
	 * 
	 * @param float $eps Earnings per share.
	 * @param float $price Current stock price.
	 * @return string Earnings yield.
	 */
	public static function earningsYield($eps, $price) {
		return strval($eps/$price);
	}
	
	/**
	 * Calculate free cash flow yield.
	 * 
	 * @param float $oper_cashflow Operating cash flow.
	 * @param float $capex Capital expenditures.
	 * @param float $marketcap Market capitalization.
	 * @return string Free cash flow yield.
	 */
	public static function fcfYield($oper_cashflow, $capex, $marketcap) {
		return strval(($oper_cashflow-$capex)/$marketcap);
	}
	
}
