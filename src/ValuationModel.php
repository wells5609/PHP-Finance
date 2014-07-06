<?php

namespace Finance;

interface ValuationModel {
	
	/**
	 * Calculates the intrinsic value as found by the model.
	 * 
	 * @return float Intrinsic value
	 */
	public function calculate();
	
}
