<?php

namespace Meteko\Stripe\Webhooks\EventHandler;

use Meteko\Stripe\Webhooks\Webhook\EventHandlerInterface;
use Stripe\Event;

class InvoiceUpdated implements EventHandlerInterface {
	public function handle(Event $event)
	{
		\Neos\Flow\var_dump($event->data, 'data');
	}


}
