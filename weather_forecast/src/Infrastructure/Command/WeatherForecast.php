<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Application\Exception\MusementCityProcessingException;
use App\Application\WeatherForecastDetector;
use App\Exception\HttpResponseException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

final class WeatherForecast extends Command
{
    private const ARGUMENT_DAYS = 'days';
    private const DEFAULT_DAYS = 2;

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
            strval(self::DEFAULT_DAYS)
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            if (is_string($input->getOption(self::ARGUMENT_DAYS))) {
                $days = (int) $input->getOption(self::ARGUMENT_DAYS);
            }

            $this->weatherForecastDetector->detect($days ?? self::DEFAULT_DAYS);

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
