# Skrz\Bundle\BunnyBundle

[![Build Status](https://travis-ci.org/skrz/bunny-bundle.svg?branch=master)](https://travis-ci.org/skrz/bunny-bundle)
[![Downloads this Month](https://img.shields.io/packagist/dm/skrz/bunny-bundle.svg)](https://packagist.org/packages/skrz/bunny-bundle)
[![Latest stable](https://img.shields.io/packagist/v/skrz/bunny-bundle.svg)](https://packagist.org/packages/skrz/bunny-bundle)

> Produce and consumer type-safe messages from RabbitMQ queues

## Installation

Add as [Composer](https://getcomposer.org/) dependency:

```sh
$ composer require skrz/bunny-bundle
```

Then add `BunnyBundle` to Symfony Kernel:

```php
use Skrz\Bundle\BunnyBundle\SkrzBunnyBundle;

class AppKernel
{

    public function registerBundles()
    {
        return [
            ...
            new SkrzBunnyBundle()
            ...
        ];
    }

}
```


## Usage

`BunnyBundle` connects `Skrz\Meta` and `Skrz\Bundle\AutowiringBundle`, so that you can produce and consume type-safe
messages to/from RabbitMQ.

`BunnyBundle` creates new 2 new stereotypes (see [`AutowiringBundle`'s description](https://github.com/skrz/autowiring-bundle#usage)):

- `@Consumer` - consumer starts listening for messages on given queue/exchange. Whenever message arrives, `handleMessage`
method is called.
- `@Producer` - producers must inherit from `Skrz\Bundle\BunnyBundle\AbstractProducer`. They publish type-safe messages 
to specified exchanges.

When `BunnyBundle` is added to the Symfony kernel, it registers 3 commands:

- `bunny:setup` - creates exchanges, queues and bindings between them according to configuration.
- `bunny:consumer` - starts given consumer.
- `bunny:producer` - utility command that takes JSON-serialized message, routing key and sends it using given producer.
Useful for debugging.

### Setup in `services.yml`

`BunnyBundle` uses `bunny` container extension key.

```yaml
bunny:
  host: %bunny.host%          # default: 127.0.0.1
  port: %bunny.port%          # default: 5672
  vhost: %bunny.vhost%        # default: /
  user: %bunny.user%          # default: guest
  password: %bunny.password%  # default: guest 
  
  # make heartbeat as long as longest message processing time in any consumer might take
  heartbeat: 120 # in seconds = 2 minutes, default: 60 seconds

  exchanges:
    change:
      durable: true  # durable means exchange won't be deleted on broker restart
      type: topic    # topic exchanges route messages by given routing key
                     # see https://www.rabbitmq.com/tutorials/amqp-concepts.html#exchange-topic
                     # other possible types: direct, fanout, headers
      
    change_done:
      durable: true
      type: topic
      bindings:
        - exchange: change  # RabbitMQ-specific functionality = exchange-to-exchange bindings
          routing_key: "#" 

  queues:
    product_categorize:
      durable: true
      bindings:
        - exchange: change
          routing_key: "change.product.#"
```

After you have configured all exchanges, queues and bindings between them, run `bunny:setup`:

```sh
$ ./console bunny:setup
```

Broker entities should be created as configured.

Note that `bunny:setup` does not try to resolve any conflicting declarations, e.g. one time you declare queue as durable
and the seconds time as not durable, you have to resolve these yourself.

### Writing producers

Our example will be async processing of changes in data. Suppose you have products an categories and want to automatically
categorize products according to product title and category title. However, the categorization algorithm is quite expensive, 
so it has to be done async. We will publish any change in product or category to `change` exchange.

Start with data model:

```php
class Product
{

    /** @var int */
    protected $id;
    
    /** @var string */
    protected $title;
    
    // ... getters, setters, etc.
    
}

class Category
{

    /** @var int */
    protected $id;
    
    /** @var string */
    protected $title;
    
    // ... getters, setters, etc.

}

class Change
{

    /** @var Product  change in product */
    protected $product;
    
    /** @var Category  change in category */
    protected $category;
    
    /** @var string  which hostname the change happened on */
    protected $hostname;
    
    /** @var int  which user made the change */
    protected $userId;
    
    // ... getters, setters, etc.
    
}
```

Producer - `ChangeProducer` - will publish changes to `change` exchange. Producers have `beforeMethod` setting - a method
on producer that is called before message is serialized and sent to broker. We will pre-process message and 
set `$hostname` and `$userId`.

```php
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

/**
 * @Producer(
 *     exchange="change",
 *     beforeMethod="preProcessMessage",
 *     meta="ChangeMeta"
 * )
 */
class ChangeProducer extends AbstractProducer
{

	/**
	 * @var TokenStorage
	 *
	 * @Autowired
	 */
	public $tokenStorage;

	public function preProcessMessage(Change $change)
	{
		$change
			->setHostname(gethostname())
			->setUserId($this->tokenStorage->getToken()->getUser()->getId());
	}

}
```

`meta` points to `*Meta` class, that will be used to serialized messages.

You can test producer from command line:

```sh
$ ./console b:p --help
Usage:
 bunny:producer producer-name message [routing-key]

Arguments:
 producer-name         Name of the producer.
 message               Message JSON string.
 routing-key           Message's routing key.
$ ./console bunny:producer Change '{"product":{"id":121,"title":"Razor blades"}}' change.product.test
```

### Writing consumers

When writing a consumer using `BunnyBundle`, think of following: consumers can fail - should messages of a failed consumer 
be redelivered? If so, you should create queue in `services.yml` and consume from given queue. If not, you should specify 
`exchange` in `@Consumer` annotation - an anonymous queue will be created on consumer startup.

We want messages to be redelivered, so `product_categorize` queue has been created, consumer will consumer from it.

```php
use Bunny\Client;
use Bunny\Message;

/**
 * @Consumer(
 *     queue="product_categorize",
 *     meta="ChangeMeta",
 *     maxMessages=1000,
 *     maxSeconds=3600.0,
 *     prefetchCount=1
 * )
 */
class ProductCategorizeConsumer
{
    
    public function handleMessage(Change $change, Message $message, Channel $channel)
    {
        // ... expensive product categorization algorithm ...
        
        $channel->ack($message);
    }
    
}
```

- `maxMessages` & `maxSeconds` - you should always run your consumer under some supervisor, e.g. [supervisord](http://supervisord.org/).
PHP can leak memory, after specified number of messages processed / seconds running, consumer will do clean shutdown (flush all messages,
disconnect from RabbitMQ) and exit with code `0` - supervisor should automatically restart it.
- `prefetchCount` - if you have more consumer processes consuming from the same queue in parallel, set `prefetchCount=1`
to evenly distribute the work between consumers 

## Known limitations

- If processing of a messages takes longer then heartbeat timeout, RabbitMQ will disconnect client a consumer will crash.
It is more of a limitation of PHP (no threads). Heartbeat has to be set high enough.

## License

The MIT license. See `LICENSE` file.
