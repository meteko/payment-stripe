<?php

namespace Meteko\Stripe\Webhooks\Webhook;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Reflection\ReflectionService;
use Neos\Flow\Configuration\ConfigurationManager;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Stripe\Event;

class EventHandlerResolver {

	/**
	 * @var ObjectManagerInterface
	 * @Flow\Inject
	 */
	protected $objectManager;

	/**
	 * @var []
	 */
	protected $map;

	public function initializeObject() {
		$this->map = self::detectHandlers($this->objectManager);
	}

	/**
	 * @param string $type
	 * @return \Closure
	 */
	public function resolve($type)
	{
		if (!isset($this->map[$type])) {
			throw new \InvalidArgumentException(sprintf('No event handler found for "%s"', $type));
		}

		$className =  $this->map[$type];

		return function (Event $event) use ($className) {
			$handler = $this->objectManager->get($className);
			$handler->handle($event);
		};
	}

	/**
	 * @param ObjectManagerInterface $objectManager
	 * @return array
	 * @throws \Exception
	 * @Flow\CompileStatic
	 */
	public static function detectHandlers(ObjectManagerInterface $objectManager) {
		$handlers = [];

		$configurationManager = $objectManager->get(ConfigurationManager::class);
		$webhooks = $configurationManager->getConfiguration($configurationManager::CONFIGURATION_TYPE_SETTINGS, 'Meteko.Stripe.Webhooks.webhooks');

		/** @var ReflectionService $reflectionService */
		$reflectionService = $objectManager->get(ReflectionService::class);

		foreach ($webhooks as $type => $handlerClassName) {
			if (class_exists($handlerClassName) && $reflectionService->isClassImplementationOf($handlerClassName, EventHandlerInterface::class)) {
				$handlers[$type] = $handlerClassName;
			} else {
				throw new \Exception(sprintf('Class registered for "%s" is not valid', [$type]));
			}
		}

		return $handlers;
	}

}
