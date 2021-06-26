#!/bin/bash

if [[ $_ != $0 ]]
then
  echo "Please, execute script from scripts directory directly"
  exit 1
fi

echo "Run weather forecast command"

docker-compose run weather_forecast_php_cli bin/console app:weather_forecast
