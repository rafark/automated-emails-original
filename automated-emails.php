<?php

use AutomatedEmails\App\Creation\Environment\EnvironmentFactory;
use AutomatedEmails\Original\Cache\MemoryCache;
use AutomatedEmails\Original\Collections\ByFileGettableCollection;
use AutomatedEmails\Original\Collections\GettableCollectionDecorator;
use AutomatedEmails\Original\Construction\Abilities\HandleableServiceExceptionFactory;
use AutomatedEmails\Original\Construction\Dependency\ProductionDependenciesContainerFactory;
use AutomatedEmails\Original\Construction\FactoryOverloader;
use AutomatedEmails\Original\Construction\Objects\ObjectsFromClassStringsFactory;
use AutomatedEmails\Original\Core\Application;
use AutomatedEmails\Original\Core\Exceptions\Handlers\OriginalServiceExceptionHandlerFactoryTypes;
use AutomatedEmails\Original\Core\Services\ActionsService;
use AutomatedEmails\Original\Core\Services\DependenciesService;
use AutomatedEmails\Original\Core\Services\MonitorService;
use AutomatedEmails\Original\Dependency\Framework\AppDependencyTypes;
use AutomatedEmails\Original\Dependency\Framework\OriginalDependencyTypes;
use AutomatedEmails\Original\Dependency\Framework\ValidDependencyTypes;
use AutomatedEmails\Original\Environment\Env;
use AutomatedEmails\Original\Files\RequireFileReader;

use function AutomatedEmails\Original\Utilities\Collection\_a;

/*
Plugin Name: Automated Emails
Plugin URI:  
Description: Send e-mails when actions happen in your site.
Version:      0.1.1
Author:       Neblabs
Author URI:   https://neblabs.com
Text Domain:  automated-emails
Domain Path:  /international
Requires at least: 4.7
Requires PHP: 7.2
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

require_once 'bootstrap.php';

 /**********************************************N*********************************************|*/
 /*|******************************************************************************************|*/
 /*|*                                                                                        *|*/
 /*|*/                                                                                      /*|*/
 /*|*                                   AUTOMATED EMAILS;                                    *|*/
 /*|*/                                                                                      /*|*/
 /*|*                                           ⇩                                            *|*/
 /*L*/                                                                                      /*A*/
 /*|*                                                                                        *|*/
 /*|********************************************B*********************************************|*/
 /*|********************************************S*********************************************|*/

require_once 'bootstrap.php';

(object) $environment = (new EnvironmentFactory)->create(Env::settings()->environment);
(object) $requireFileReader = new RequireFileReader(new MemoryCache);

(object) $overloadableServiceExceptionFactories = new FactoryOverloader(
    AutomatedEmails\Original\Utilities\Construction\create(
        new ObjectsFromClassStringsFactory(
            \AutomatedEmails\Original\Utilities\Collection\get(
                new ByFileGettableCollection(
                    new OriginalServiceExceptionHandlerFactoryTypes,
                    $requireFileReader
                )
            )
        )
    )
);

/** @var HandleableServiceExceptionFactory */
(object) $serviceExceptionFactory = $overloadableServiceExceptionFactories->overload($environment);

(object) $application = new Application(
    $serviceExceptionFactory->create()
);

$application->addService(
    new DependenciesService(
        new ProductionDependenciesContainerFactory,
        new ValidDependencyTypes(
            new GettableCollectionDecorator(
                new ByFileGettableCollection(
                    new OriginalDependencyTypes,
                    $requireFileReader
                ),
                new ByFileGettableCollection(
                    new AppDependencyTypes,
                    $requireFileReader
                )
            )
        )
    )  
);
$application->addService(new MonitorService);
$application->addService(new ActionsService);

$application->start();
