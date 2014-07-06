<?php
/**
 * General mathematical functions.
 */

/**
 * Calculate mean (simple arithmetic average).
 * 
 * @param array $values
 * @return float Mean
 */
function mean(array $values) {
	return floatval(array_sum($values)/count($values));
}

/**
 * Calculate covariance.
 * 
 * @param array $x_values Dependent variable values.
 * @param array $y_values Independent variable values.
 * @return float Covariance of x and y.
 */
function covariance(array $x_values, array $y_values) {
	
	$n = count($x_values);
	
	if ($n !== count($y_values)) {
		// chop the # of y vals down to the # of x vals
		$y_values = array_slice($y_values, 0, $n, true);
	}
	
	$mean_x = mean($x_values);
	$mean_y = mean($y_values);
	
	$diffs_x = $diffs_y = $xy_diffs = array();
	
	foreach($x_values as $i => $x) {
		$diffs_x[$i] = $x - $mean_x;
	}
	foreach($y_values as $i => $y) {
		$diffs_y[$i] = $y - $mean_y;
	}
	foreach($diffs_x as $i => $diff_x) {
		$xy_diffs[$i] = $diff_x * $diffs_y[$i];
	}
	
	return floatval( 1 / ($n-1) * array_sum($xy_diffs) );
}

/**
 * Calculate variance.
 * 
 * @param array $values
 * @return float Variance of the values.
 */
function variance(array $values) {
	return floatval( sumofsquares($values) / (count($values)-1) );
}

/**
 * Compute standard deviation.
 * 
 * @param array $a The array of data to find the standard deviation for. 
 * Note that all values of the array will be cast to float.
 * @param bool $is_sample [Optional] Indicates if $a represents a sample of the 
 * population (otherwise its the population); Defaults to false.
 * @return float|bool The standard deviation or false on error.
 */
function stddev(array $a, $is_sample = false) {
	if ($n = count($a) < 2) {
		trigger_error("The array has $n elements", E_USER_NOTICE);
		return false;
	}
	$mean = array_sum($a)/$n;
	$carry = 0.0;
	foreach ($a as $val) {
		$d = floatval($val) - $mean;
		$carry += $d * $d;
	}
	if ($is_sample) {
		--$n;
	}
	return sqrt($carry/$n);
}

/**
 * Compute the sum of squares.
 * 
 * @param array $values An array of values.
 * @param null|scalar|array $values2 If given an array, computes the sum of squares
 * of the difference between $values[i] and $values2[i] (i.e. SUM{ ($values[i] - $values2[i])^2 })
 * If given a scalar value, computes the SS of the difference between each value 
 * in $values and the given value. If no value is given, uses the mean of the values.
 * @return float Sum of squares.
 */
function sumofsquares(array $values, $values2 = null) {
	if (! isset($values2)) {
		$values2 = array_fill_keys(array_keys($values), mean($values));
	} else if (! is_array($values2)) {
		$values2 = array_fill_keys(array_keys($values), $values2);
	}
	$sum = 0.0;
	foreach($values as $i => $val) {
		if (isset($values2[$i])) {
			$sum += pow($val - $values2[$i], 2);
		}
	}
	return $sum;
}

/**
 * Returns the present value of a cashflow, given a discount rate and time period.
 * 
 * @param int|float|string $cashflow Numeric quantity of currency.
 * @param float|string $rate Discount rate
 * @param int|float|string $period A number representing time period in which the 
 * cash flow occurs. e.g. for an annual cashflow, start a 0 and increase by 1 
 * each year (e.g. [Year] 0, [Year] 1, ...)
 * @return float Present value of the cash flow. 
 */
function pv($cashflow, $rate, $period = 0) {
	return (float) ($period < 1) ? $cashflow : $cashflow/pow(1+$rate, $period);
}

/**
 * Returns the Net Present Value of a series of cashflows.
 * 
 * @param array $cashflows Indexed array of cash flows.
 * @param float $rate Discount rate applied.
 * @return float NPV of $cashflows discounted at $rate.
 */
function npv(array $cashflows, $rate) {
	$npv = 0.0;
	foreach($cashflows as $index => $cashflow) {
		$npv += pv($cashflow, $rate, $index);
	}
	return floatval($npv);
}

/**
 * Returns the % of an amount of the total.
 * 
 * e.g. for operating margin, use operating income as 1st arg, revenue as 2nd.
 * e.g. for capex as a % of sales, use capex as 1st arg, revenue as 2nd.
 * 
 * @param float $amount An amount, a portion of the total.
 * @param float $total The total amount.
 * @return float %
 */
function percent($amount, $total) {
	return floatval($amount/$total);
}

/**
 * Returns the % change between two values.
 * 
 * @param float $current The current value.
 * @param float $previous The previous value.
 * @return float Percent change from previous to current.
 */
function percent_change($current, $previous) {
	return floatval(($current-$previous)/$previous);
}

/**
 * Convert an array of values to % change.
 * 
 * @param array $values Raw values ordered from oldest to newest.
 * @return array Array of the % change between values.
 */
function percent_change_array(array $values) {
	$pcts = array();
	$keys = array_keys($values);
	$vals = array_values($values);
	foreach($vals as $i => $value) {
		if (0 !== $i) {
			$prev = $vals[$i-1];
			$pcts[$i] = floatval(($value-$prev)/$prev);
		}
	}
	array_shift($keys);
	return array_combine($keys, $pcts);
}

/**
 * Returns the weighted average of a series of values.
 * 
 * @param array $values Indexed array of values.
 * @param array $weights Indexed array of weights corresponding to each value.
 * @return float Weighted average of values.
 */
function weighted_average(array $values, array $weights) {
	if (count($values) !== count($weights)) {
		trigger_error("Must pass the same number of weights and values.");
		return null;
	}
	$weighted_sum = 0.0;
	foreach($values as $i => $val) {
		$weighted_sum += $val*$weights[$i];
	}
	return floatval($weighted_sum/array_sum($weights));
}
