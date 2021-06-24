#!/bin/bash
set -euo pipefail
cd "${0%/*}"
declare -A ENV_FILES=( [".env"]=".env.local" [".php-cs-fixer.dist.php"]=".php-cs-fixer.php" \
                  ["phpunit.xml.dist"]="phpunit.xml" ["phpstan.neon.dist"]="phpstan.neon" )

PROJECT_DIR="$( cd "../weather_forecast" && pwd )"

COLOUR_GREEN=$(tput setaf 2)
RESET=$(tput sgr0)
RED=$(tput setaf 1)

getUserEnvParams () {
    USER_INPUT=''

    while IFS= read -r -N 1 ch; do
        case "$ch" in
            $'\n'|' '|$'\t') break   ;&
            *)      USER_INPUT="$USER_INPUT$ch" ;;
        esac
    done

    echo "${USER_INPUT//[^a-zA-Z0-9]//g}"
}

setupLocalEnvironmentFileConfiguration () {
    echo "Please pass WEATHER_API_KEY. Press [ENTER] or [SPACE] to continue: "
    WEATHER_API_KEY=$(getUserEnvParams)

    echo "Please pass SECRET_KEY. Press [ENTER] or [SPACE] to continue: "
    SECRET_KEY=$(getUserEnvParams)

    cp "$PROJECT_DIR/$1" "$PROJECT_DIR/$2"

    sed  -i -E 's/WEATHER_API_KEY=.*/WEATHER_API_KEY='"$WEATHER_API_KEY"'/' "$PROJECT_DIR/$2"
    sed -i -E 's/APP_SECRET=.*/APP_SECRET='"$SECRET_KEY"'/' "$PROJECT_DIR/$2"
}

buildContainer () {
    echo "${COLOUR_GREEN}Building docker image...${RESET}"

    docker-compose rm -vf
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
      setupLocalEnvironmentFileConfiguration "$FILE" "${ENV_FILES[$FILE]}"
    else
      echo -e "${COLOUR_GREEN}Configuring ${ENV_FILES[$FILE]} settings...${RESET}"
      cp "${PROJECT_DIR}/$FILE" "${PROJECT_DIR}/${ENV_FILES[$FILE]}"
    fi
  else
    echo -e "${COLOUR_GREEN}${ENV_FILES[$FILE]} already exists and will not be copied.${RESET}"
  fi
done

buildContainer

echo -e "${RED}If you passed incorrect values for WEATHER_API_KEY or APP_SECRET, please modify .env.local file and invoke this script again.${RESET}"
