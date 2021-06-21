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
     '--d')
          docker-compose up -d
          ;;
     '--stop')
          docker-compose down
          ;;
     '--help')
          echo "Usage: environment_start ${COLOUR_GREEN}[options]${RESET}"
          echo
          echo "Examples:"
          echo
          echo "  ${COLOUR_GREEN}environment_start --d${RESET}     - Start docker-compose in detached mode"
          echo "  ${COLOUR_GREEN}environment_start --stop${RESET}  - Stop docker-compose"
          echo "  ${COLOUR_GREEN}environment_start --help${RESET}  - Display help"
          echo "  ${COLOUR_GREEN}environment_start${RESET}         - Start docker-compose and show output in console"
          ;;
     *)
          docker-compose up
          ;;
esac