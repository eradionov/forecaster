#!/bin/bash
set -euo pipefail
echo "Run weather forecast command"

docker-compose run weather_forecast_php_cli bin/console app:weather_forecast
