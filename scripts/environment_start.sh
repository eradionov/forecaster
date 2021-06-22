#!/bin/bash

if [[ $_ != $0 ]]
then
  echo "Please, execute script from scripts directory directly"
  exit 1
fi

PROJECT_DIR="$( cd "../$( dirname "$0" )" && pwd )"
COLOUR_GREEN=`tput setaf 2`
RESET=`tput sgr0`

case $1 in
     '--detach'|'-d')
          docker-compose up -d
          ;;
     '--stop'|'-s')
          docker-compose down
          ;;
     '--help'|'-h')
          echo "Usage: environment_start ${COLOUR_GREEN}[option]${RESET}"
          echo
          echo
          echo "  -d,  --detach           Run docker container in detached mode"
          echo "  -s,  --stop             Stop docker container"
          echo "  -h,  --help             Run help"
          ;;
     *)
          docker-compose up
          ;;
esac
