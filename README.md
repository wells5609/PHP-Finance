Corporate-Finance-PHP
=====================

PHP library for doing various corporate finance... things.

##Features
 * Host a stock data API on your own server using data from Yahoo Finance (**see note below**)
 * Calculate (estimate) a stock's intrinsic price using the Dividend Discount Model
 * Calculate various metrics used in corporate finance/valuation, such as:
   * Present value (`finance_pv()`)
   * Net present value (`finance_npv()`)
   * Standard deviation (`finance_stddev()`)
   * (Market) risk premium (`finance_risk_premium()`)
   * Cost of equity (`finance_capm_cost_of_equity()`)

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

