#!/bin/bash

if [[ $_ != $0 ]]
then
  echo "Please, execute script from scripts directory directly"
  exit 1
fi

PROJECT_DIR="$( cd "../$( dirname "$0" )/weather_forecast" && pwd )"
COLOUR_GREEN=`tput setaf 2`
RESET=`tput sgr0`
RED=`tput setaf 1`

getUserEnvParams () {
    USER_INPUT=''

    while IFS= read -r -N 1 ch; do
        case "$ch" in
            $'\n'|' '|$'\t') got_eot=1;break   ;&
            *)      USER_INPUT="$USER_INPUT$ch" ;;
        esac
    done

    echo "$USER_INPUT"
}

setupPackages () {
  echo "${COLOUR_GREEN}Building docker image...${RESET}"

  docker-compose build

  echo "${COLOUR_GREEN}Install composer packages...${RESET}"

  docker-compose run weather_forecast_php_cli composer install
}

echo -e "${COLOUR_GREEN}Setting up environment...${RESET}"

# Copying phpunit.xml.dist into phpunit.xml

if [ ! -f "${PROJECT_DIR}/phpunit.xml" ] && [ -f "${PROJECT_DIR}/phpunit.xml.dist" ]
then
    echo -e "${COLOUR_GREEN}Configuring phpunit settings...${RESET}"

    cp "${PROJECT_DIR}/phpunit.xml.dist" "${PROJECT_DIR}/phpunit.xml"
else
  echo -e "${RED}phpunit.xml already exists.${RESET}"
fi

# Copying phpstan.neon.dist into phpstan.neon

if [ ! -f "${PROJECT_DIR}/phpstan.neon" ] && [ -f "${PROJECT_DIR}/phpstan.neon.dist" ]
then
    echo -e "${COLOUR_GREEN}Configuring phpstan settings...${RESET}"

    cp "${PROJECT_DIR}/phpstan.neon.dist" "${PROJECT_DIR}/phpstan.neon"
else
  echo -e "${RED}phpstan.neon already exists.${RESET}"
fi

# Copying .php-cs-fixer.dist.php into .php-cs-fixer.php

if [ ! -f "${PROJECT_DIR}/.php-cs-fixer.php" ] && [ -f "${PROJECT_DIR}/.php-cs-fixer.dist.php" ]
then
    echo -e "${COLOUR_GREEN}Configuring php-cs-fixer settings...${RESET}"

    cp "${PROJECT_DIR}/.php-cs-fixer.dist.php" "${PROJECT_DIR}/.php-cs-fixer.php"
else
  echo -e "${RED}.php-cs-fixer.php already exists.${RESET}"
fi

# Copying .env.dist into .env.dev and setting up environment variables

if [ ! -f "${PROJECT_DIR}/.env.dev" ] && [ -f "${PROJECT_DIR}/.env.dist" ]
then
    echo -e "${COLOUR_GREEN}Setting up .env.dev environment file${RESET}"

    echo "Please pass WEATHER_API_KEY. Press [ENTER] or [SPACE] to continue: "
    WEATHER_API_KEY=$(getUserEnvParams)

    echo "Please pass SECRET_KEY. Press [ENTER] or [SPACE] to continue: "
    SECRET_KEY=$(getUserEnvParams)

    cp "${PROJECT_DIR}/.env.dist" "${PROJECT_DIR}/.env.dev"

    SECRET_KEY=$(echo "${SECRET_KEY}" | sed -e 's/[^a-zA-Z0-9]//g' )
    WEATHER_API_KEY=$(echo "${WEATHER_API_KEY}" | sed -e 's/[^a-zA-Z0-9]//g' )

    sed  -i -E 's/WEATHER_API_KEY=.*/WEATHER_API_KEY='"$WEATHER_API_KEY"'/' "${PROJECT_DIR}/.env.dev"
    sed -i -E 's/APP_SECRET=.*/APP_SECRET='"$SECRET_KEY"'/' "${PROJECT_DIR}/.env.dev"

    setupPackages
else
  echo -e "${RED}.env.dev already exists.${RESET}"
fi
