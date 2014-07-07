<?php

namespace Finance\Model;

class ConstantGrowth implements ValuationModel {
	
	protected $forward_eps;
	protected $discount_rate;
	protected $growth_rate;
	
	public function __construct($forward_eps = null, $discount_rate = null, $growth_rate = null) {
		isset($forward_eps) and $this->setEPS($forward_eps);
		isset($discount_rate) and $this->setDiscountRate($discount_rate);
		isset($growth_rate) and $this->setGrowthRate($growth_rate);
	}
	
	public function setEPS($eps) {
		$this->forward_eps = (string) $eps;
		return $this;
	}
	
	public function setDiscountRate($rate) {
		$this->discount_rate = (string) $rate;
		return $this;
	}
	
	public function setGrowthRate($rate) {
		$this->growth_rate = (string) $rate;
		return $this;
	}
	
	public function calculate() {
		
		if (! isset($this->forward_eps) || ! isset($this->discount_rate) || ! isset($this->growth_rate)) {
			throw new \RuntimeException("Must set EPS, discount rate, and growth rate.");
		}
		
		$value = $this->forward_eps/($this->discount_rate-$this->growth_rate);
		
		return is_finite($value) ? (string) $value : false;
	}
	
}
