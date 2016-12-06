<?php

namespace Imamuseum\ProficioClient;

use Exception;

class ProficioTransformer
{
    public function __construct()
    {
        $config = config('proficio');

        // Transform Config items
        $this->field_addition = config('proficio.field_addition');
        $this->field_transform_class = new $config['field_transform_class'];
    }

    /**
     * transform the Proficio object into a Harvest object using
     * the field map config
     */
    public function transform($proficio_object, $source)
    {
        $field_map = $this->fetchFieldMapConfig($source);
        $harvest_object = $this->createFieldMap($field_map);

        foreach ($proficio_object as $proficio_key => $value) {
            if(array_key_exists($proficio_key, $field_map)) {
                $function = null;
                $data = null;

                // fetch harvest key
                $harvest_key = $field_map[$proficio_key];

                // if array extract harvest_key, function_name, data
                if (is_array($harvest_key)) {
                    $function = $harvest_key['func'];
                    $data = isset($harvest_key['data']) ? $harvest_key['data'] : null;
                    $harvest_key = $harvest_key['key'];
                }

                // trim whitespace and make utf8 complient
                $value = mb_convert_encoding(trim($value), "UTF-8", "auto");

                // transform value if needed. Passing harvest object index
                // allows developer to append data to existing harvest object.
                // e.g. actors appear in multiple columns
                if (isset($function)) {
                    $value = $this->field_transform_class
                        ->$function($value, $data, $harvest_object[$harvest_key]);
                }

                // map proficio to harvest
                $harvest_object[$harvest_key] = $value;
            }
        }

        // if (!empty($this->field_addition)) $harvest_object = $this->addFields($harvest_object);
        // $harvest_object['images'] = $images;
        return $harvest_object;
    }

    /**
     * Transform a collection of proficio objects
     */
    public function collection($data, $source)
    {
        $results = [];
        foreach ($data as $object) {
            $results[] = $this->transform($object, $source);
        }

        return [
            'results' => $results,
            'total' => count($results),
            // 'meta' => [
            //     'image_count' => $this->getImageCount($data),
            // ]
        ];
    }

    private function fetchFieldMapConfig($source)
    {
        if ($source == 'collection' || $source == 'monitor')
        {
            $source = 'collection_monitor';
        }

        return config("proficio.$source.field_map");
    }

    /**
      * Build empty field map from config
      * collection and monitor have the same field mapping
      */
    private function createFieldMap($field_map)
    {
        $fields = [];
        foreach ($field_map as $field) {
            if (is_array($field)) {
                $field = $field['key'];
            }

            $fields[$field] = [];
        }
        return $fields;
    }
}
