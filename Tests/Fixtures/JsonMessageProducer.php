<?php
namespace Skrz\Bundle\BunnyBundle\Tests\Fixtures;

use Skrz\Bundle\BunnyBundle\AbstractProducer;
use Skrz\Bundle\BunnyBundle\Annotation\Producer;

/**
 * @author Jakub Kulhan <jakub.kulhan@gmail.com>
 *
 * @Producer(
 *     exchange="producer_test_exchange",
 *     routingKey="test.protobuf",
 *     meta="Skrz\Bundle\BunnyBundle\Tests\Fixtures\Meta\MessageMeta",
 *     contentType="application/json"
 * )
 */
class JsonMessageProducer extends AbstractProducer
{
}
