#!/bin/bash

if [[ $_ != $0 ]]
then
  echo "Please, execute script from scripts directory directly"
  exit 1
fi

declare -A ENV_FILES=( [".env"]=".env.local" [".php-cs-fixer.dist.php"]=".php-cs-fixer.php" \
                  ["phpunit.xml.dist"]="phpunit.xml" ["phpstan.neon.dist"]="phpstan.neon" )

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

setupLocalEnvironmentFileConfiguration () {
    echo "Please pass WEATHER_API_KEY. Press [ENTER] or [SPACE] to continue: "
    WEATHER_API_KEY=$(getUserEnvParams)

    echo "Please pass SECRET_KEY. Press [ENTER] or [SPACE] to continue: "
    SECRET_KEY=$(getUserEnvParams)

    cp "$PROJECT_DIR/$1" "$PROJECT_DIR/$2"

    SECRET_KEY=$(echo "${SECRET_KEY}" | sed -e 's/[^a-zA-Z0-9]//g' )
    WEATHER_API_KEY=$(echo "${WEATHER_API_KEY}" | sed -e 's/[^a-zA-Z0-9]//g' )

    sed  -i -E 's/WEATHER_API_KEY=.*/WEATHER_API_KEY='"$WEATHER_API_KEY"'/' "$PROJECT_DIR/$2"
    sed -i -E 's/APP_SECRET=.*/APP_SECRET='"$SECRET_KEY"'/' "$PROJECT_DIR/$2"

    echo "${COLOUR_GREEN}Building docker image...${RESET}"

    docker-compose build

    echo "${COLOUR_GREEN}Install composer packages...${RESET}"

    docker-compose run weather_forecast_php_cli composer install
}

echo -e "${COLOUR_GREEN}Setting up environment...${RESET}"

for FILE in "${!ENV_FILES[@]}";
do
  if [ ! -f "$PROJECT_DIR/${ENV_FILES[$FILE]}" ] && [ -f "$PROJECT_DIR/$FILE" ]
  then
    if [[ $FILE = ".env" ]] && [[ ${ENV_FILES[$FILE]} = ".env.local" ]]
    then
      setupLocalEnvironmentFileConfiguration $FILE ${ENV_FILES[$FILE]}
    else
      echo -e "${COLOUR_GREEN}Configuring ${ENV_FILES[$FILE]} settings...${RESET}"
      cp "${PROJECT_DIR}/$FILE" "${PROJECT_DIR}/${ENV_FILES[$FILE]}"
    fi
  else
    echo -e "${RED}${ENV_FILES[$FILE]} already exists.${RESET}"
  fi
done
