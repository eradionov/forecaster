#!/bin/bash

if [[ $_ != $0 ]]
then
  echo "Please, execute script from directory directly"
  exit 1
fi

PROJECT_DIR="$( cd "../$( dirname "$0" )" && pwd )"

COLOUR_GREEN=`tput setaf 2`
RESET=`tput sgr0`

case $1 in
     'phpunit')
          docker-compose run weather_forecast_php_cli composer phpunit
          ;;
     'fixer')
          docker-compose run weather_forecast_php_cli composer cs-fixer
          ;;
     'phpstan')
          docker-compose run weather_forecast_php_cli composer phpstan
          ;;
     '--help')
          echo "Usage: environment_tools ${COLOUR_GREEN}[options]${RESET}"
          echo
          echo "Examples:"
          echo
          echo "  ${COLOUR_GREEN}environment_tools phpstan${RESET}"
          echo "  ${COLOUR_GREEN}environment_tools fixer${RESET}"
          echo "  ${COLOUR_GREEN}environment_tools phpunit${RESET}"
          exit 1
          ;;
esac