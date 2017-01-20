<?php

namespace Imamuseum\ProficioClient\Traits;

use DB;
use Exception;

trait SqliteFunctions
{

    /**
     * Create sqlite functions
     *
     * @return int
     */
    public function createSqlFunctions()
    {
        // HASH - create a hash from given string
        DB::connection(config('proficio.database_connection'))
            ->getPdo()
            ->sqliteCreateFunction('HASH', function ($string, $method = 'md5') {
                return hash($method, $string);
            });

        // REGEXCAPALL - perform preg_match_all and return all values
        DB::connection(config('proficio.database_connection'))
            ->getPdo()
            ->sqliteCreateFunction('REGEXCAPALL', function ($pattern, $value, $function = null, $set_order = 'true') {
                if (!$value) {
                    return null;
                }

                $order = $set_order == 'true' ? PREG_SET_ORDER : PREG_PATTERN_ORDER;

                preg_match_all($pattern, $value, $matches, $order);

                if ($function) {
                    $config = config('proficio.field_transform_class');
                    $class = new $config;
                    $matches = $class->$function($matches);
                }

                return json_encode($matches);
            });


        // REGEXCAP - perform preg_match and return first value
        DB::connection(config('proficio.database_connection'))
            ->getPdo()
            ->sqliteCreateFunction('REGEXCAP', function ($pattern, $value) {

                preg_match($pattern, $value, $match);
                return isset($match[0]) ? $match[0] : null;
            });


        // REGEXREPLACE - perform preg_replace return all
        DB::connection(config('proficio.database_connection'))
            ->getPdo()
            ->sqliteCreateFunction('REGEXREPLACE', function ($pattern, $replacement, $value, $limit = -1) {
                return preg_replace($pattern, $replacement, $value, $limit);
            });

        // REGEXSPLIT - perform preg_split return all
        DB::connection(config('proficio.database_connection'))
            ->getPdo()
            ->sqliteCreateFunction('REGEXSPLIT', function ($pattern, $value) {
                return json_encode(preg_split($pattern, $value));
            });

        /**
         * COMBINE - combines arrays values and types
         * @param $types must be a json array
         * @param $values must be a json array
         */
        DB::connection(config('proficio.database_connection'))
            ->getPdo()
            ->sqliteCreateFunction('COMBINE', function ($values, $types) {
                $types = json_decode($types);
                $decoded = json_decode($values);
                $temp = [];

                // If values is not a json array then combine it with the first type
                if (json_last_error() != JSON_ERROR_NONE) {
                    throw new Exception($values . "Is not valid json");
                }

                // Combine decoded values with all types
                if ($types) {
                    while (!empty($decoded)) {
                        foreach ($types as $type) {
                            if (empty($decoded)) {
                                break;
                            }
                            $temp[] = [
                                'value' => array_shift($decoded),
                                'type' => $type
                            ];
                        }
                    }
                }

                return json_encode($temp);
            });

        // CUSTOM - perform custom function
        DB::connection(config('proficio.database_connection'))
            ->getPdo()
            ->sqliteCreateFunction('CUSTOM', function () {
                // fetch arguments and function
                $params = func_get_args();
                $function = array_pop($params);

                $config = config('proficio.field_transform_class');
                // if there is only one arguement only pass it.
                $params = sizeof($params) > 1 ? $params : $params[0];
                $class = new $config;
                $results = $class->$function($params);

                return json_encode($results);
            });

        // JSONARRAY - json encode a value
        DB::connection(config('proficio.database_connection'))
            ->getPdo()
            ->sqliteCreateFunction('JSONARRAY', function ($value) {
                return json_encode([$value]);
            });
    }
}
