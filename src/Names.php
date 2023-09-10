<?php
/**
 * @copyright Copyright © 2023 BeastBytes - All rights reserved
 * @license BSD 3-Clause
 */

namespace BeastBytes\OrdnanceSurvey\Names;

use BeastBytes\OrdnanceSurvey\OrdnanceSurvey;
use GuzzleHttp\RequestOptions;

/**
 * Implements the {@link https://osdatahub.os.uk/docs/names/overview Ordnance Survey Names API}.
 * See https://osdatahub.os.uk/docs/names/technicalSpecification for details.
 */
class Names extends OrdnanceSurvey
{
    private const BOUNDING_BOX = 'BBOX:';
    private const LOCAL_TYPE = 'LOCAL_TYPE:';
    private const API_NAME = 'names';
    private const API_VERSION = 'v1';

    /**
     * Finds the data associated with the query string.
     *
     * @param string $key API key
     * @param string $query The query to send
     * @param array $options Additional options; key=>value pairs
     * @param array $filter Local type filters or British National Grid bounding box
     * @return array|bool|null Response data on success, FALSE on error
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function find(string $key, string $query, array $options = [], array $filter = []): array|bool|null
    {
        $qry = $options;
        $qry['key'] = $key;
        $qry['query'] = $query;

        if (!empty($filter)) {
            if (is_int($filter[0])) {
                $qry['fq'] = self::BOUNDING_BOX . implode(',', $filter);
            } else {
                $qry['fq'] = self::LOCAL_TYPE . implode(' ' . self::LOCAL_TYPE, $filter);
            }
        }

        return self::get(self::API_NAME . '/' . self::API_VERSION . '/find', $qry);
    }

    /**
     * Returns the closest address to a given point.
     *
     * @param string $key API key
     * @param array $point BNG coordinates
     * @param array $options Additional options
     * @param array $filter Local type filters
     * @return array|bool|null Response data on success, FALSE on error
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public static function nearest(string $key, array $point, array $options = [], array $filter = []): array|bool|null
    {
        $qry = $options;
        $qry['key'] = $key;
        $qry['point'] = implode(',', $point);

        if (!empty($filter)) {
            $qry['fq'] = self::LOCAL_TYPE . implode(' ' . self::LOCAL_TYPE, $filter);
        }

        return self::get(self::API_NAME . '/' . self::API_VERSION . '/nearest', $qry);
    }
}
