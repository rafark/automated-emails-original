<?php

namespace AutomatedEmails\Original\Core\Services;

use AutomatedEmails\Original\Construction\Events\EventHandlerFactory;
use AutomatedEmails\Original\Core\Abilities\Service;
use AutomatedEmails\Original\Core\Abilities\ServicesContainer;
use AutomatedEmails\Original\Core\Application;
use AutomatedEmails\Original\Subscribers\ServicesRestarter;
use Closure;

/**
 * Restarts the services when 'init' gets called 
 * Very important if the 'init' action hook is called
 * more than once in the same request.
 *
 * By restarting the services, we make sure that the action hook subscribers
 * are only registered once.
 */
class MonitorService implements Service
{
    protected ?Closure $handler;

    public function id(): string
    {
        return 'monitor';
    } 

    /** @param Application $servicesContainer */
    public function start(ServicesContainer $servicesContainer): void
    {
        (object) $servicesRestarterSubscriber = new ServicesRestarter($servicesContainer);
        (object) $eventHandlerFactory = new EventHandlerFactory;
        (object) $eventHandler = $eventHandlerFactory->create($servicesRestarterSubscriber);

        $this->handler = $eventHandler->handle(...);

        add_action(hook_name: 'init', callback: $this->handler);
    } 

    public function stop(ServicesContainer $servicesContainer): void
    {
        remove_action(hook_name: 'init', callback: $this->handler);

        $this->handler = null;
    } 
}