<?php
/**
 * RESTfm - FileMaker RESTful Web Service
 *
 * @copyright
 *  Copyright (c) 2011-2016 Goya Pty Ltd.
 *
 * @license
 *  Licensed under The MIT License. For full copyright and license information,
 *  please see the LICENSE file distributed with this package.
 *  Redistributions of files must retain the above copyright notice.
 *
 * @link
 *  http://restfm.com
 *
 * @author
 *  Gavin Stewart
 */

 // Register an autoload function for RESTfm class files.
 spl_autoload_register(
    // Autoload function to register:
    function($class) {

        // Ignore autoload requests for these.
        static $ignore = NULL;
        if ($ignore === NULL) {
            $ignore = array(
                    '/^Tonic\\\/',
                    '/^Composer\\\/',
            );
        }
        foreach ($ignore as $ignoreRegex) {
            if (preg_match($ignoreRegex, $class) === 1) {
                # DEBUG log
                #error_log("Matched ignore regex: $ignoreRegex");
                return;
            }
        }

        // All php files that make up RESTfm.
        static $libPhpFiles = NULL;
        if ($libPhpFiles === NULL) {
            $libPhpFiles = array();

            // Traverse under $fqpn for $matches ending in $suffix.
            // $matches = array( <basename> => <fqpn>, ... )
            function traverseDirs($fqpn, $suffix, &$matches) {
                $dh = opendir($fqpn);
                while (($childName = readdir($dh))) {
                    if ( ($childName == '.' || $childName == '..')) {
                        continue;
                    }
                    $childFqpn = $fqpn . DIRECTORY_SEPARATOR . $childName;
                    if (is_file($childFqpn) &&
                          (substr($childName, -strlen($suffix)) === $suffix) ) {
                        $matches[basename($childName, $suffix)] = $childFqpn;
                    } elseif (is_dir($childFqpn)) {
                        traverseDirs($childFqpn, $suffix, $matches);
                    }
                }
                closedir($dh);
            }

            // Find all .php files under __DIR__
            traverseDirs(__DIR__, '.php', $libPhpFiles);
        }

        // See if we have a file that matches the name of the class.
        if (isset($libPhpFiles[$class])) {
            require_once $libPhpFiles[$class];
        } else {
            error_log("RESTfm autoload failed for class: $class");
        }
    },
    // Throw exception when autoload function fails to register:
    true,
    // Prepend function on the autoload queue instead of appending it:
    false
);