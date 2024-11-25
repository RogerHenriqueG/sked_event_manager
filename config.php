<?php

setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

date_default_timezone_set('America/Sao_Paulo');

ini_set('default_charset', 'UTF-8');

$GLOBALS['TZ'] = new \DateTimeZone( 'America/Sao_Paulo');

define('APP_MODE','dev');

define('DEBUG', true);
