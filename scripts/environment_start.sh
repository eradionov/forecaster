#!/bin/bash
set -euo pipefail
COLOUR_GREEN=$(tput setaf 2)
RESET=$(tput sgr0)

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
