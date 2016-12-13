<?php

namespace Imamuseum\ProficioClient;

use DB;
use Exception;
use Carbon\Carbon;
use Imamuseum\ProficioClient\ProficioTransformer;
use Imamuseum\ProficioClient\Traits\SqliteFunctions;

class ProficioController
{
    use SqliteFunctions;

    public function __construct()
    {
        $this->createSqlFunctions();
        $this->since = Carbon::now()->subDays(config('proficio.since'))->toDateTimeString();
    }

    /**
     * Fetch all IDs from the given catalog
     * @param $catalog Proficio catalog in config
     * @param $start   Begin result pagination
     * @param $take    Number of results to return
     * @author Daniel Keller
     */
    public function getAllObjectIDs($catalog, $start=null, $take=null)
    {
        return $this->getIDs($catalog, $start, $take);
    }

    /**
     * Fetch all IDs from objects that have been updated since config date
     * @param $catalog Proficio catalog query from
     * @param $start   Begin result pagination
     * @param $take    Number of results to return
     * @author Daniel Keller
     */
    public function getUpdatedObjectIDs($catalog, $start=null, $take=null)
    {
        return $this->getIDs($catalog, $start, $take, $this->since);
    }

    /**
     * Get specific object in given catalog from proficio
     * @param $id       Object id in catalog
     * @param $catalog  Proficio catalog to query from
     * @author Daniel Keller
     */
    public function getSpecificObject($id, $catalog)
    {
        $tables = config("proficio.queries");
        $object = [];
        $exists = false;

        foreach ($tables as $table => $sources) {
            $object[$table] = $this->queryTable($catalog, $table, [$id]);
            if (!empty($object[$table])) {
                $exists = true;
            }
        }

        // If the object wasn't found return false
        return $exists ? $object : false;
    }


    public function doesObjectExist($id, $catalog)
    {
        return $this->queryTable($catalog, 'field_ids', [$id]);
    }
    /**
     * Run proficio query for given object_uids and return the results
     * @param $catalog Proficio catalog to query
     * @param $table   Harvest table to fetch for
     * @param $start   Begin result pagination
     * @param $take    Number of results to return
     * @author Daniel Keller
     */
    public function getTableForAllObjects($catalog, $table, $start=null, $take=null)
    {
        $ids = $this->getAllObjectIDs($catalog, $start, $take);
        return $this->queryTable($catalog, $table, $ids->results);
    }

    /**
     * Run proficio query for given object_uids and return the results
     * @param $catalog Proficio catalog to query
     * @param $table   Harvest table to fetch for
     * @param $ids     Object_uids to fetch
     * @author Daniel Keller
     */
    public function queryTable($catalog, $table, $ids)
    {
        $query = config("proficio.queries.$table."."$catalog");

        // If no config query is set skip
        // If we are updating and there are NO ids to be updated then return empty array
        if (!$query || !$ids) {
            return [];
        }

        // Build query
        $objects = DB::connection(config('proficio.database_connection'))
            ->table(DB::raw('(' . $query . ')'))
            ->whereIn('object_uid', $ids)
            ->get();

        return $objects;
    }

    /**
     * Fetch field ids from given catalog since given data
     * @param $catalog Proficio catalog to query
     * @param $start  Begin result pagination
     * @param $take   Number of results to return
     * @param $since   Date to fetch data since
     * @author Daniel Keller
     */
    private function getIDs($catalog, $start=null, $take=null, $since=null)
    {
        $config = config("proficio.queries.field_ids.$catalog");

        if (!$config) {
            throw new Exception("config setting 'proficio.queries.field_ids.$catalog' not found");
        }

        $query = DB::connection(config('proficio.database_connection'))
            ->table(
                DB::raw('(' . $config . ')')
            );

        if ($since)  $query = $query->where('updated_at', '>=', $since);
        if ($take)  $query = $query->limit($take);
        if ($start) $query = $query->offset($start);

        $ids = $query->pluck('object_uid');

        return (object) [
            'total' => count($ids),
            'results' => $ids
        ];
    }
}
