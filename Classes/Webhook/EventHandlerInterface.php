<?php

namespace Meteko\Stripe\Webhooks\Webhook;

use Stripe\Event;

interface EventHandlerInterface {
	/**
	 * @param Event $event
	 * @return voids
	 */
	public function handle(Event $event);
}
