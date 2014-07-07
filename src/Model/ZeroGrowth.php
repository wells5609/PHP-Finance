<?php

namespace Finance\Model;

class ZeroGrowth implements ValuationModel {
	
	protected $eps;
	protected $discount_rate;
	
	public function __construct($eps = null, $discount_rate = null) {
		isset($eps) and $this->setEPS($eps);
		isset($discount_rate) and $this->setDiscountRate($discount_rate);
	}
	
	public function setEPS($eps) {
		$this->eps = (string) $eps;
		return $this;
	}
	
	public function setDiscountRate($rate) {
		$this->discount_rate = (string) $rate;
		return $this;
	}
	
	public function calculate() {
		
		if (! isset($this->eps) || ! isset($this->discount_rate)) {
			throw new \RuntimeException("Must set EPS and discount rate.");
		}
		
		$value = $this->eps/$this->discount_rate;
		
		return is_finite($value) ? (string) $value : false;
	}
	
}
