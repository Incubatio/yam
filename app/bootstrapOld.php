<?php
use Incube\Config,
    Incube\Db\DataModel,
    Incube\Resource,
    Nadeo\ManiaServer,
    Nadeo\ApiClient;

class Bootstrap extends Resource {

    public function init_configs() {

        // Init config loader and load configs
        $config_loader = new Config();
        $configs = $config_loader->load_by_folder(ROOT_PATH . '/app/etc', true);

        return $configs;
    }


    protected function init_orm() {
        $config = $this->_load('configs')->get('db')->get(ENV)->get('orm');

        $ORMOptions = Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration($config->get('options')->to_array(), true);
        return Doctrine\ORM\EntityManager::create($config->get('connection')->to_array(), $ORMOptions);
    }

    protected function init_odm() {
        $config = $this->_load('configs')->get('db')->get(ENV)->get('odm');


        $ODMConfig = new \Doctrine\ODM\MongoDB\Configuration();
        if($config->has('options')) {
            foreach ( $config->get('options')->to_array() as $key => $value) {
                $ODMConfig->{"set" . ucfirst($key)}($value);
            }
        }


        Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver::registerAnnotationClasses();

        if($config->get('connection')->has('options')) {
            if($config->get('connection')->get('options')->has('bool')) {
                foreach ($config->get('connection')->get('options')->get('bool') as $key => $option) { 
                    $config->get('connection')->get('options')->set($key, (bool) $option); 
                }
                unset($config->connection->options->bool);
            }
        }
        $connectionOptions = ($config->get( 'connection' )->get( 'options' )) ? $config->get( 'connection' )->get( 'options' )->to_array() : array();
        $connection = new \Doctrine\MongoDB\Connection($config->get('connection')->get('server' ), $connectionOptions);

        return Doctrine\ODM\MongoDB\DocumentManager::create($connection, $ODMConfig);
    }

    protected function init_event_manager() {

        $manager = new \Incube\Event\EventManager();
        $fsIterator = new FilesystemIterator(ROOT_PATH .'/app/Services/Listeners');
        foreach( $fsIterator as $fileInfo){
            list($fileName,$ext) = explode('.',$fileInfo->getFileName());
            if($fileInfo->isFile() && $ext == 'php') {
                $listenerClassName = '\Services\Listeners\\'. $fileName;
                foreach($listenerClassName::getEvents() as $namespace => $event) {
                    foreach($event as $eventName => $callableNames) {
                        $manager->attach(array($namespace, $eventName), array($listenerClassName, $callableNames));
                    }
                }
            }
        }
        return $manager;
    }

}

$resource = new Bootstrap();
return $resource->load();
