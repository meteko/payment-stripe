<?php

namespace Meteko\Stripe\Webhooks\Controller;

use Neos\Flow\Annotations as Flow;
use Meteko\Stripe\Webhooks\Webhook\EventHandlerResolver;
use Neos\Flow\Mvc\Controller\ActionController;

class WebhookController extends ActionController {

	/**
	 * @var EventHandlerResolver
	 * @Flow\Inject
	 */
	protected $eventHandlerResolver;

	/**
	 * Webhook
	 */
	public function eventAction() {
		$input = $this->request->getHttpRequest()->getContent();
		$payload = json_decode($input);

		try {
			\Stripe\Stripe::setApiKey($this->settings['apiKey']);
			$event = \Stripe\Event::retrieve($payload->id);
			$handler = $this->eventHandlerResolver->resolve($event->type);
			$handler($event);
			$this->response->setStatus(200);

		} catch (\Exception $exception) {
			$this->response->setStatus(500, $exception->getMessage());
		}

		#$this->response->send();

		return $this->response;


	}
}
