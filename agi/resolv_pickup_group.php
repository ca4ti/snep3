#!/usr/bin/php -q
<?php
/**
 *  This file is part of SNEP.
 *
 *  SNEP is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  SNEP is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with SNEP.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once('agi_base.php');

if(!isset($argv[1]) || !is_numeric($argv[1])) {
   $log->crit("Argumento invalido para primeiro argumento , $argv[1]. Espera-se um ramal");
}

if(isset($argv[2])) {
    $variable = $argv[2];
}
else {
    $variable = "PICKUPGROUP";
}

try {
    $ramal = PBX_Usuarios::get($argv[1]);
}
catch(PBX_Exception_NotFound $ex) {
    $log->info("Ramal {$argv[1]} não encontrado.");
    $asterisk->set_variable($variable, '-1');
}

$asterisk->set_variable($variable, $ramal->getPickupGroup());
