<?php
/*
 *  This file is part of SNEP.
 *  Para território Brasileiro leia LICENCA_BR.txt
 *  All other countries read the following disclaimer
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


    require 'libs/autoloader.php';

    $available_modules = array(
        'bandwidth',
        'df',
        'dhcpleases',
        'hostname',
        'ip',
        'issue',
        'lastlog',
        'loadavg',
        'mem',
        'memcached',
        'netstat',
        'numberofcores',
        'online',
        'phpinfo',
        'ping',
        'ps',
        'sabnzbd',
        'speed',
        'swap',
        'time',
        'uptime',
        'users',
        'where',
		'arp',
		'redis_status'
    );


    /**
     * Populate a module loader with our enabled modules
     */
    $mods = new \ld\Modules\Loader;
    $mods->defaultNamespace('\\Modules');

    foreach($available_modules as $module) {
        $mods->addModule($module);
    }


    /**
     * If running on the terminal or via a script take the module name
     * from the passed in arguments. Otherwise, take it from the HTTP request
     */
    if (php_sapi_name() === 'cli') {
        $requested_module = isset($argv[1]) ? $argv[1] : false;
    } else {
        $requested_module = isset($_GET['module']) ? $_GET['module'] : false;
    }


    // The default JSON object to return
    $return = array(
        'module' => $requested_module,
        'data'   => false,
        'error'  => false,
    );


    try {
        if (!$requested_module || !$mods->moduleAvailable($requested_module)) {
            throw new Exception('module_not_found');
        }

        $module = $mods->module($requested_module);

        if (!$module) {
            throw new Exception('error_loading_module');
        }

        $return['data'] = $module->getData($_GET);
        unset($return['error']);

    } catch (Exception $e) {

        unset($return['data']);
        $return['error'] = $e->getMessage();
    }

    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($return);
