#!/bin/bash

if [[ $_ != $0 ]]
then
  echo "Please, execute script from directory directly"
  exit 1
fi

PROJECT_DIR="$( cd "../$( dirname "$0" )/weather_forecast" && pwd )"
COLOUR_GREEN=`tput setaf 2`
RESET=`tput sgr0`
RED=`tput setaf 1`

echo -e "${COLOUR_GREEN}Setting up environment...${RESET}"

if [ ! -f "${PROJECT_DIR}/.env.dev" ] && [ -f "${PROJECT_DIR}/.env.dist" ]
then
    echo -e "${COLOUR_GREEN}Setting up .env.dev environment file${RESET}"

    cp "${PROJECT_DIR}/.env.dist" "${PROJECT_DIR}/.env.dev"


else
  echo -e "${RED}.env.dev already exists.${RESET}"
fi

if [ ! -f "${PROJECT_DIR}/phpunit.xml" ] && [ -f "${PROJECT_DIR}/phpunit.xml.dist" ]
then
    echo -e "${COLOUR_GREEN}Configuring phpunit settings...${RESET}"

    cp "${PROJECT_DIR}/phpunit.xml.dist" "${PROJECT_DIR}/phpunit.xml"
else
  echo -e "${RED}phpunit.xml already exists.${RESET}"
fi

if [ ! -f "${PROJECT_DIR}/phpstan.neon" ] && [ -f "${PROJECT_DIR}/phpstan.neon.dist" ]
then
    echo -e "${COLOUR_GREEN}Configuring phpstan settings...${RESET}"

    cp "${PROJECT_DIR}/phpstan.neon.dist" "${PROJECT_DIR}/phpstan.neon"
else
  echo -e "${RED}phpstan.neon already exists.${RESET}"
fi