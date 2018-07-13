<?php

use ckuran\DoctrineWrapper;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

require_once 'bootstrap.php';

return ConsoleRunner::createHelperSet(DoctrineWrapper::getEntityManager('default'));
