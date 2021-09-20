<?php

declare(strict_types=1);

namespace Skrz\Bundle\BunnyBundle\Command;

use InvalidArgumentException;
use Skrz\Bundle\BunnyBundle\Exception\BunnyException;
use Skrz\Bundle\BunnyBundle\Queue\BunnyProducerInterface;
use Skrz\Bundle\BunnyBundle\Service\BunnyManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProducerCommand extends Command
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
	 * @var string|null The default command name
	 */
	protected static $defaultName = 'bunny:producer';

	private BunnyManager $manager;

	/** @var array<string, BunnyProducerInterface> */
	private array $producers;

	/** @param BunnyProducerInterface[] $producers */
	public function __construct(BunnyManager $manager, iterable $producers)
	{
		$this->manager = $manager;
		$this->producers = [];

		foreach ($producers as $producer) {
			$this->producers[$producer->getName()] = $producer;
		}

		parent::__construct();
	}

	protected function configure(): void
	{
		$this
			->setHelp("Available producers are:\n" . implode(PHP_EOL, array_keys($this->producers)))
			->setDescription("Send message through producer.")
			->addArgument("producer-name", InputArgument::REQUIRED, "Name of the producer.")
			->addArgument("message", InputArgument::REQUIRED, "Message JSON string.")
			->addArgument("routing-key", InputArgument::OPTIONAL, "Message's routing key.", null)
			->addOption("count", "c", InputOption::VALUE_REQUIRED, "Message will be published X times.", 1)
			->addOption("listFile", "f", InputOption::VALUE_OPTIONAL, "line separated list of ids to produce");
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$producerName = $input->getArgument("producer-name");
		$message = $input->getArgument("message");
		$routingKey = $input->getArgument("routing-key");

		if (!isset($this->producers[$producerName])) {
			throw new InvalidArgumentException("Producer '{$producerName}' does not exist.");
		}

		$producer = $this->producers[$producerName];
		$this->manager->setUp();

		if ($input->getOption("listFile") !== null) {
			$handle = fopen($input->getOption("listFile"), 'rb');

			if ($handle !== false) {
				while (($line = fgets($handle)) !== false) {
					$producer->publish(sprintf($message, trim($line)), $routingKey);
				}

				fclose($handle);
			} else {
				throw new BunnyException("error reading file");
			}
		} else {
			for ($i = 0, $count = $input->getOption("count"); $i < $count; ++$i) {
				$producer->publish($message, $routingKey);
			}
		}

		return 0;
	}
}
