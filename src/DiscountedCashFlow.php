<?php

namespace Finance;

class DiscountedCashFlow {
	
	protected $initial_cashflow;
	protected $discount_rate;
	protected $number_periods = 5;
	protected $growth_rate;
	
	protected $cashflows = array();
	protected $cashflows_pv = array();
	
	protected $terminal_growth_rate;
	protected $terminal_cashflow;
	protected $terminal_cashflow_pv;
	
	protected $npv;
	
	public function setInitialCashflow($cashflow) {
		$this->initial_cashflow = floatval($cashflow);
		return $this;
	}
	
	public function setDiscountRate($rate) {
		$this->discount_rate = floatval($rate);
		return $this;
	}
	
	public function setGrowthRate($rate) {
		$this->growth_rate = floatval($rate);
		return $this;
	}
	
	public function setNumberPeriods($n) {
		$this->number_periods = intval($n);
		return $this;
	}
	
	public function setTerminalGrowthRate($rate) {
		$this->terminal_growth_rate = floatval($rate);
		return $this;
	}
	
	public function getNPV() {
		
		if (! isset($this->npv)) {
			
			if (empty($this->cashflows)) {
				$this->buildCashflows();
			}
			
			$pv = array_sum($this->cashflows_pv);
			
			if (isset($this->terminal_cashflow_pv)) {
				$this->npv = $this->terminal_cashflow_pv + $pv;
			} else {
				$this->npv = $pv;
			}
		}
		
		return $this->npv;
	}
	
	protected function buildCashflows() {
		
		$this->cashflows = build_cashflows(
			$this->initial_cashflow, 
			$this->number_periods, 
			$this->growth_rate
		);
		
		$this->cashflows_pv = pv_cashflows(
			$this->cashflows, 
			$this->discount_rate
		);
		
		if (isset($this->terminal_growth_rate)) {
			
			$this->terminal_cashflow = terminal_cashflow(
				end($this->cashflows), 
				$this->terminal_growth_rate, 
				$this->discount_rate
			);
			
			$this->terminal_cashflow_pv = pv(
				$this->terminal_cashflow, 
				$this->discount_rate, 
				$this->number_periods
			);
		}
	}
	
}
