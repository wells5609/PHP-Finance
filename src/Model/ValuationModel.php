<?php

namespace Finance\Model;

/**
 * Contract for a model which calculates an asset's "intrinsic" value.
 */
interface ValuationModel {
	
	/**
	 * Calculates the intrinsic value as found by the model.
	 * 
	 * @return string Intrinsic value
	 */
	public function calculate();
	
}
