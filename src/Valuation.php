<?php

namespace Finance;

class Valuation {
		
	/**
	 * Calculates a security's intrinsic value using the zero growth model.
	 * 
	 * @param float $eps Earnings per share (this year).
	 * @param float $discount_rate The discount rate, possibly the expected return of the market.
	 * @return float Intrinsic value at zero growth.
	 */
	public static function zeroGrowth($eps, $discount_rate) {
		$model = new Model\ZeroGrowth($eps, $discount_rate);
		return $model->calculate();
	}
	
	/**
	 * Calculates a security's intrinsic value using the constant growth model.
	 * 
	 * @param float $forward_eps Earnings per share over the next 12 months.
	 * @param float $discount_rate Discount rate
	 * @param float $growth_rate Rate at which earnings grow for infinity.
	 * @return float Intrinsic value at constant growth.
	 */
	public static function constantGrowth($forward_eps, $discount_rate, $growth_rate) {
		$model = new Model\ConstantGrowth($forward_eps, $discount_rate, $growth_rate);
		return $model->calculate();
	}
	
	public static function dividendGrowth($annDivd, $costEquity, $growthRate, $termGrowthRate, $numYears = 5) {
		$ddm = new Model\DividendDiscount();
		return $ddm->setDividend($annDivd)
			->setCostOfEquity($costEquity)
			->setNumberYears($numYears)
			->setGrowthRate($growthRate)
			->setTerminalGrowthRate($termGrowthRate)
			->calculate();
	}
	
}
