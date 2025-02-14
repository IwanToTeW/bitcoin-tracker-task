<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Installation Guide
- Clone the repository
- Create a database and update the .env file
- php artisan key:generate
- php artisan migrate
- php artisan db:seed
- Run `composer install`
- Run `npm install`
- Run `npm run dev`
 
To run the tests:
- Run `php artisan test`

## Project Description

- This is a simple Laravel project that allows users to browse through historic data of BTC price and subscribe to price updates.
- To see it navigate to /login
- Use one of the following users to login:
    - email: ivan.totev@ampeco.com, password: password
    - email: maksim.atanasov@ampeco.com, password: password
  
- You should then be redirected to /dashboard
- You should be able to browse through the historic data of BTC price for USD/EURO. The data supports both daily and weekly charts.
- If you want to subscribe for a price you should fill in the form below the chart.
- If a period is not specified you are subscribing to a price and you will be notified when the BTC price exceeds the target.
- If you specify a period then you have to present a target percentage with the price. This will then notify you when the price exceeds the target percentage for the specified period.
After the period is over the subscription is automatically marked as expired.
