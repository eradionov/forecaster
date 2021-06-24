# Developer

Yauheni Radzivonau

# Weather Forecast for musement cities

Weather Forecast is a symfony command, that displays forecast for each musement city for today and tomorrow

## Requirements
1. Docker v19.03.0+
2. Docker compose v3.8+

## Installation

To setup project invoke **environment_setup.sh** script.<br/>
You will be needed to pass APP_SECRET and WEATHER_API_KEY values
If you passed incorrect values for WEATHER_API_KEY or APP_SECRET, please modify .env.local file and invoke this script again.

```bash
./scripts/environment_setup.sh
```

## Usage

### To start development environment use **environment_start.sh** script

```bash
./scripts/environment_start.sh           # Start dev environment
./scripts/environment_start.sh --help    # Display help
./scripts/environment_start.sh --detach  # Start dev environment in detached mode
./scripts/environment_start.sh --stop    # Stop dev environment
./scripts/environment_start.sh --help    # Display help
```

### To run available code-style fixing and analyzing tools use **environment_tools.sh** script

```bash
./scripts/environment_tools.sh --phpunit         # Run phpunit tests on project code
./scripts/environment_tools.sh --fixer           # Run code style php-cs-fixer on project code
./scripts/environment_tools.sh --phpstan         # Run phpstan analyzer on project code
./scripts/environment_start.sh --all             # Run all tools at once
./scripts/environment_start.sh --help            # Display help
```

### To run weather-forecast generation use **run_forecast.sh** script

All forecasts will be displayed in a console.

```bash
./scripts/run_forecast.sh   # Run weather-forecast command
```

## Possible improvement
Redis key-value in-memory storage can be used to store forecastsю
It will prevent API request hits if command is invoked several times on the same day.
