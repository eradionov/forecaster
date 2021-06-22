# Developer

Yauheni Radzivonau

# Weather Forecast for musement cities

Weather Forecast is a symfony command, that displays forecast for each musement city for today and tomorrow

## Requirements
1. Docker v19.03.0+
2. Docker compose v3.8+

## Installation

To setup project it's needed to cd into scripts folder in cloned directory root and run **environment_setup.sh**  script.<br/>
You will be asked to pass some environment parameter values, that will be written into .env.dev file

```bash
./environment_setup.sh
```

## Usage

### To start development environment **environment_start.sh** script is used

```bash
./environment_start.sh           # Start dev environment
./environment_start.sh --help    # Display help
./environment_start.sh --detach  # Start dev environment in detached mode
./environment_start.sh --stop    # Stop dev environment
./environment_start.sh --help    # Display help
```

### To run available code-style fixing and analyzing tools  **environment_tools.sh** script is used

```bash
./environment_tools.sh --phpunit         # Run phpunit tests on project code
./environment_tools.sh --fixer           # Run code style php-cs-fixer on project code
./environment_tools.sh --phpstan         # Run phpstan analyzer on project code
./environment_start.sh --all             # Run all tools at once
./environment_start.sh --help            # Display help
```

### To run weather-forecast generation **run_forecast.sh** script is used

All forecasts will be displayed in a console.

```bash
./run_forecast.sh   # Run weather-forecast command
```

## Points for improvement
Redis key-value in-memory storage can be used to store forecasts, that will prevent API request hit if command is invoked
several times on the same day.
