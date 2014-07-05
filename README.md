Finance-PHP
=====================

Corporate finance PHP library.

##Features
 * Host a stock data API on your own server using data from Yahoo Finance (**see note below**)
 * Calculate (estimate) a stock's intrinsic price using the Dividend Discount Model
 * Calculate various statistics:
   * Variance (`variance()`)
   * Mean (`mean()`)
   * Covariance (`covariance()`)
   * Standard deviation (`stddev()`)
   * Sum of squares (`sumofsquares()`)
   * Present value (`pv()`)
   * Net present value (`npv()`)
 * Calculate various values for use in corporate finance and valuation:
   * Beta (`beta()`)
   * Cost of equity per CAPM (`capm_cost_of_equity()`)
   * Earnings per share, asset turnover, inventory turnover, current & quick ratios, days receivables, ROA, ROE, P/E, P/S, P/B, earnings yield, free cash flow yield, and more

_**Note on data**: This software package is for educational purposes only; it shall not be used to redistribute data  sourced from third parties unless the user is explicitly authorized to do so. The author will not be held responsible for any misuse of this software. By using this software, you are agreeing to Yahoo's Terms of Service._

###Stock API Application

Using this application might make sense if:
 1. You currently use a "free", "public" stock data API (see note below), or would like to;
 2. You use this data somewhat extensively in your application(s); and
 3. You want more control over how this data is retrieved, presented, or manipulated.

In addition, if you are accessing stock data from several different applications, the case for using this API is even stronger.

Using this application does not make sense if:
 1. You currently use a paid stock data service
 2. You access stock data only infrequently from a "free", "public" API

####_Note on "Free", "Public" Stock Data APIs_
They do not exist.

