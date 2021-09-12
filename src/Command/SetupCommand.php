<?php

declare(strict_types=1);

namespace Skrz\Bundle\BunnyBundle\Command;

use Skrz\Bundle\BunnyBundle\Service\BunnyManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends Command
{

	/**
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
	 * @var string|null The default command name
	 */
	protected static $defaultName = 'bunny:setup';
	private BunnyManager $manager;

	public function __construct(BunnyManager $manager)
	{
		parent::__construct();
		$this->manager = $manager;
	}

	protected function configure(): void
	{
		$this->setDescription("Sets up exchange-queue topology as specified on bunny configuration.");
	}

	protected function execute(InputInterface $input, OutputInterface $output): void
	{
		$this->manager->setUp();
	}
}
