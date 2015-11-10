<?php
namespace Skrz\Bundle\BunnyBundle\Tests\Fixtures;

use Skrz\Bundle\BunnyBundle\AbstractProducer;
use Skrz\Bundle\BunnyBundle\Annotation\Producer;

/**
 * @author Jakub Kulhan <jakub.kulhan@gmail.com>
 *
 * @Producer(
 *     exchange="",
 *     routingKey="producer_test_queue",
 *     meta="Skrz\Bundle\BunnyBundle\Tests\Fixtures\Meta\MessageMeta"
 * )
 */
class EmptyExchangeProducer extends AbstractProducer
{
}
