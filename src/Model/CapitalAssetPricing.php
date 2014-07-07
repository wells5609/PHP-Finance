<?php

namespace Finance\Model;

class CapitalAssetPricing {
	
	protected $risk_free_rate;
	protected $expected_return_asset;
	protected $expected_return_market;
	protected $market_risk_premium;
	protected $beta;
	
	public function __construct($risk_free_rate = null) {
		if (isset($risk_free_rate)) {
			$this->setRiskFreeRate($risk_free_rate);
		}
	}
	
	public function setRiskFreeRate($rate) {
		$this->risk_free_rate = (string) $rate;
		return $this;
	}
	
	public function setExpectedMarketReturn($rate) {
		$this->expected_return_market = (string) $rate;
		return $this;
	}
	
	public function setExpectedAssetReturn($rate) {
		$this->expected_return_asset = (string) $rate;
		return $this;
	}
	
	public function setMarketRiskPremium($rate) {
		$this->market_risk_premium = (string) $rate;
		return $this;
	}
	
	public function setBeta($beta) {
		$this->beta = (string) $beta;
		return $this;
	}
	
	public function getRiskFreeRate() {
		return isset($this->risk_free_rate) ? $this->risk_free_rate : risk_free_rate();
	}
	
	public function getExpectedMarketReturn() {
		return isset($this->expected_return_market) ? $this->expected_return_market : null;
	}
	
	public function getExpectedAssetReturn() {
		return isset($this->expected_return_asset) ? $this->expected_return_asset : null;
	}
	
	public function getMarketRiskPremium() {
		
		if (! isset($this->market_risk_premium)) {
				
			if (! isset($this->expected_return_market)) {
				trigger_error("Must set expected return of market benchmark to find market risk premium.");
				return null;
			}
			
			$this->market_risk_premium = strval($this->expected_return_market-$this->getRiskFreeRate());
		}
		
		return $this->market_risk_premium; 
	}
	
	public function getBeta() {
		return isset($this->beta) ? $this->beta : null;
	}
	
	public function getRequiredReturn() {
		
		if (! isset($this->expected_return_market)) {
			trigger_error("Must set expected return of market benchmark to find required return.");
			return null;
		}
		
		if (! isset($this->beta)) {
			trigger_error("Must set beta to calculate required return.");
			return null;
		}
	
		return capm_cost_of_equity($this->beta, $this->getMarketRiskPremium(), $this->getRiskFreeRate());
	}
	
}
