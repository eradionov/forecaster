<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\HttpResponseException;
use App\Exception\MusementCityProcessingException;
use App\WeatherForecast\WeatherForecastDetector;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

final class WeatherForecast extends Command
{
    private const ARGUMENT_DAYS = 'days';

    private WeatherForecastDetector $weatherForecastDetector;
    private LoggerInterface $logger;

    /**
     * @param WeatherForecastDetector $weatherForecastDetector
     * @param LoggerInterface $logger
     * @param string|null $name
     */
    public function __construct(
        WeatherForecastDetector $weatherForecastDetector,
        LoggerInterface $logger,
        string $name = null
    ) {
        parent::__construct($name);

        $this->weatherForecastDetector = $weatherForecastDetector;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this->addOption(
            self::ARGUMENT_DAYS,
            null,
            InputArgument::OPTIONAL,
            'Number of days to generate forecast.',
            '2'
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $days = $input->getOption(self::ARGUMENT_DAYS);

            if (!\is_string($days) || !is_numeric($days)) {
                $output->writeln('<error>Number of days should be numeric value.</error>');

                return self::FAILURE;
            }

            $this->weatherForecastDetector->detect((int) $days);

            return self::SUCCESS;
        } catch (HttpResponseException | ExceptionInterface | MusementCityProcessingException $exception) {
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
        } catch (\Throwable $exception) {
            $output->writeln('<error>Unexpected error occurred, please see logs for details.</error>');
            $this->logger->error($exception->getMessage());
        }

        return self::FAILURE;
    }
}
