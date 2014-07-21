Finance-PHP
=====================

Corporate finance PHP library.

##Features
 * Calculate various metrics used in corporate finance and valuation:
   * Beta (`beta()`)
   * Cost of equity per CAPM, earnings per share, asset turnover, inventory turnover, current & quick ratios, times interest earned, leverage, days receivables, ROA, ROE, P/E, P/S, P/B, earnings yield, free cash flow yield, and more
 * Estimate a stock's "intrinsic" value using the Dividend Discount Model, Constant Growth Model, or Zero Growth Model (more to come).

##Requirements
 * PHP 5.3+
 * `wells5609/php-math` - includes some required mathematical functions like `pv()` and `covariance()`

##Examples

###Valuation
_Example: Estimate the intrinsic value of Exxon Mobil (XOM) stock using three valuation methods (trading at 102 at the time of writing)._

Assume the following information (which is accurate as of July 2014):
 * Current annual dividend: $2.52
 * Cost of equity: 9%
 * Current earnings per share (EPS): $7.34
 * 12-mo. forward EPS (analyst consensus est.): $7.70

Also assume the following (which are, in my opinion, rather optimistic):
 * Number of projected periods: 10
 * Annual growth rate for projected periods: 10%
 * Terminal growth rate: 2.5%

#####Dividend Discount Model
The DDM states that a stock's current value is equal to the present value of the sum of its future dividends.
```php
$ddm = new \Finance\Model\DividendDiscount();

// set required info
$ddm
  ->setDividend(2.52)
  ->setCostOfEquity(0.09)
  ->setNumberYears(10)
  ->setGrowthRate(0.1)
  ->setTerminalGrowthRate(0.025);

$ddm_value = $ddm->calculate(); // returns "70.04"
```

#####Zero Growth Method
Often used for "mature" firms, this model basically assumes no growth in earnings. The resulting value is simply EPS divided by the discount rate.
```php
$zero = new \Finance\Model\ZeroGrowth();

$zero
  ->setEPS(7.34) // Use current EPS for zero growth
  ->setDiscountRate(0.09); // using cost of equity

$zgm_value = $zero->calculate(); // returns "81.5555..."
```

#####Constant Growth Model
The constant growth model assumes that the firm's earnings grow at a certain rate for infinity; obviously, this is hard to reconcile with reality, especially for mature or maturing firms.
```php
$cons = new \Finance\Model\ConstantGrowth();

$cons
  ->setEPS(7.7) // Use forward EPS for constant growth
  ->setDiscountRate(0.09)
  ->setGrowthRate(0.025); // should be less than the discount rate, otherwise you won't get a number

$cgm_value = $cons->calculate(); // returns "118.46"
```

#####Put it all together
Now we have 3 estimates for the intrinsic value. From here, you could take the simple average or a weighted average like so:
```php

$estimates = array($ddm_value, $zgm_value, $cgm_value);

$simp_avg = mean($estimates); // returns "90.02"

// Assign weights corresponding to each value
$weights = array(0.5, 0.3, 0.2);

$weighted_avg = weighted_avg($estimates, $weights); // returns "83.18"
```

