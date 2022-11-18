<?php

// src/AppBundle/Utils/MonologDBHandler.php

namespace App\Generales;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class MonologDBHandler extends AbstractProcessingHandler
{
   
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    public function __construct(EntityManagerInterface $em, $level = Logger::ERROR, $bubble = true)
    {
        $this->em = $em;
        parent::__construct($level, $bubble);
    }

    protected function write(array $record): void
    {
       /*  if (!$this->initialized) {
            $this->initialize();
        } */

        /* if ($this->channel != $record['channel']) {
            return;
        } */

        $this->em->getConnection()->beginTransaction();
        try {
            #codigo para evitar error de "EntityManager is closed"
            if (!$this->em->isOpen()) {
                $this->em = $this->em->create(
                    $this->em->getConnection(),
                    $this->em->getConfiguration()
                );
            }
            $log = new Log();
            //$log->setMessage($record['message']);
            //$log->setLevel($record['level_name']);
            $log->setMessage($record['message']);
            $log->setLevel($record['level']);
            $log->setLevelName($record['level_name']);
            $log->setExtra($record['extra']);
            $log->setContext($record['context']);

            $this->em->persist($log);
            $this->em->flush();
            $this->em->getConnection()->commit();
        }catch(\Exception $e){
            $this->em->getConnection()->rollBack();
            echo 'Error al guardar el error2: '.$e->getMessage();
        }
    }

    /* private function initialize()
    {
        $this->initialized = true;
    } */
}
