# Why
Why not, you can test it here https://fininv.varis.social <br>
Prices for stocks automatically update every minute. There is no realistic updating, just a random plus or minus between 0 and 10. <br>
Profit is calculated after you sell the stock. 

## How to use locally
- Clone the repo
- Create a database user & database for the application
- Update the .env file with your database user
- Run the following command
```
    php bin/console doctrine:migrations:migrate
```
- Create RSA Keys and move them to /config/jwt/
- Run the server with
``` 
    symfony serve
```
### Optional

- Create a cronjob for price.sh; This will cause prices to update everytime it's run
