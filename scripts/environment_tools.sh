#!/bin/bash
set -euo pipefail
COLOUR_GREEN=$(tput setaf 2)
RESET=$(tput sgr0)

case $1 in
     '--phpunit'|'-pu')
          docker-compose run weather_forecast_php_cli composer phpunit
          ;;
     '--fixer'|'-cs')
          docker-compose run weather_forecast_php_cli composer cs-fixer
          ;;
     '--phpstan'|'-ps')
          docker-compose run weather_forecast_php_cli composer phpstan
          ;;
     '--all'|'-a')
          docker-compose run weather_forecast_php_cli composer phpunit
          docker-compose run weather_forecast_php_cli composer cs-fixer
          docker-compose run weather_forecast_php_cli composer phpstan
          ;;
     '--help'|'-h')
          echo "Usage: environment_tools ${COLOUR_GREEN}[option]${RESET}"
          echo
          echo
          echo "  -pu,  --phpunit         Run phpunit tests"
          echo "  -cs,  --fixer           Run php-cs-fixer"
          echo "  -ps,  --phpstan         Run phpstan analyzer"
          echo "  -a,   --all             Run all tools"
          echo "  -h,   --help            Run help"
          exit 1
          ;;
esac
