<?php

namespace Finance\Model;

class DividendDiscount implements ValuationModel {
	
	protected $price;
	protected $dividend;
	protected $growth_rate;
	protected $terminal_growth_rate;
	protected $cost_of_equity;
	protected $required_return;
	protected $number_years = 5;
	protected $dcf;
	
	public function __construct($price = null) {
		if (isset($price)) {
			$this->setPrice($price);
		}
	}
	
	public function setPrice($price) {
		$this->price = (string) $price;
		return $this;
	}
	
	public function setDividend($div) {
		$this->dividend = (string) $div;
		return $this;
	}
	
	public function setNumberYears($num) {
		$this->number_years = (int) $num;
		return $this;
	}
	
	public function setGrowthRate($rate) {
		$this->growth_rate = (string) $rate;
		return $this;
	}
	
	public function setTerminalGrowthRate($rate) {
		$this->terminal_growth_rate = (string) $rate;
		return $this;
	}
	
	public function setCostOfEquity($rate) {
		$this->cost_of_equity = (string) $rate;
		return $this;
	}
	
	public function getRequiredReturn() {
		
		if (isset($this->cost_of_equity)) {
			return $this->cost_of_equity;
		}
			
		if (! isset($this->terminal_growth_rate)) {
			trigger_error("Must set terminal growth rate to calculate required return.");
			return null;
		}
		
		if (! isset($this->price)) {
			trigger_error("Must set current price to calculate required return.");
			return null;
		}
		
		if (! isset($this->dividend)) {
			trigger_error("Must set annual dividend to calculate required return.");
			return null;
		}
		
		return $this->required_return = strval($this->terminal_growth_rate + ($this->dividend/$this->price));
	}
	
	public function calculate() {
		
		$r = $this->getRequiredReturn();
		
		$this->dcf = new \Finance\DiscountedCashFlow();
		$this->dcf
			->setInitialCashFlow($this->dividend)
			->setDiscountRate($r)
			->setNumberPeriods($this->number_years)
			->setGrowthRate($this->growth_rate)
			->setTerminalGrowthRate($this->terminal_growth_rate);
		
		return $this->intrinsic_value = $this->dcf->getNPV();
	}
	
}
