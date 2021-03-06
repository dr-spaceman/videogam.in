<?php

namespace Vgsite\API;

use Vgsite\AlbumMapper;
use Vgsite\API\Exceptions\APIException;
use Vgsite\Registry;
use Vgsite\API\Exceptions\APIInvalidArgumentException;
use Vgsite\API\Exceptions\APINotFoundException;
use Vgsite\HTTP\Request;

class SearchController extends Controller
{
    private $pdo;

    const SORTABLE_FIELDS = ['title', 'title_sort', 'type', 'category'];

    const BASE_URI = API_BASE_URI . '/search';

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->pdo = Registry::get('pdo');
    }

    protected function getOne($id): void
    {
        throw new APINotFoundException();
    }

    /**
     * @OA\Get(
     *     path="/search",
     *     description="Search all the things",
     *     operationId="Search",
     *     @OA\Parameter(ref="#/components/parameters/q", required=true),
     *     @OA\Parameter(ref="#/components/parameters/sort"),
     *     @OA\Response(response=200,
     *         description="Things matching request query {q}",
     *         @OA\JsonContent(type="object",
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="title_sort", type="string"),
     *             @OA\Property(property="type", type="string"),
     *             @OA\Property(property="category", type="string"),
     *             @OA\Property(property="links", type="array", @OA\Items(type="string")),
     *             @OA\Property(property="href", type="string"),
     *         )
     *     )
     * )
     */
    protected function getAll(): void
    {
        $query = $this->parseQuery('q', '');
        if (empty($query)) {
            throw new APIInvalidArgumentException('No search term given. Try using the `q` parameter.', '?q');
        }
        $sort_sql = $this->parseQuery('sort', "`title_sort` ASC");
        [$sort, $sort_by] = $this->parseSortSql($sort_sql);

        // This might be implemented in future
        $filter = null;

        // Populate with SQL sql queries
        $sql = Array();
        if (! $filter) {
            $sql[] = "SELECT `id`,`title`,`title_sort`,`subcategory`,`type`,`index_data` 
				FROM `pages` WHERE `redirect_to`='' AND (`title` LIKE CONCAT('%', :query, '%') OR `keywords` LIKE CONCAT('%', :query, '%')) 
				ORDER BY {$sort_sql} LIMIT 100";
        } else {
            $tables = array(
                "categories" => "SELECT `title`, title_sort, `subcategory`, `type`, index_data FROM pages WHERE `type` = 'category' AND `redirect_to` = '' AND (`title` LIKE CONCAT('%', :query, '%') OR `keywords` LIKE CONCAT('%', :query, '%')) ORDER BY {$sort_sql} LIMIT 100",
                "games" => "SELECT `title`, title_sort, `subcategory`, `type`, index_data FROM pages WHERE `type` = 'game' AND `redirect_to` = '' AND (`title` LIKE CONCAT('%', :query, '%') OR `keywords` LIKE CONCAT('%', :query, '%')) ORDER BY {$sort_sql} LIMIT 100",
                "people" => "SELECT `title`, title_sort, `subcategory`, `type`, index_data FROM pages WHERE `type` = 'person' AND `redirect_to` = '' AND (`title` LIKE CONCAT('%', :query, '%') OR `keywords` LIKE CONCAT('%', :query, '%')) ORDER BY {$sort_sql} LIMIT 100",
                "characters" => "SELECT `title`, title_sort, `subcategory`, `type`, index_data FROM pages_links LEFT JOIN pages ON (pages_links.from_pgid = pages.pgid) WHERE (`to` = 'Game character') AND `namespace` = 'Category' AND `redirect_to` = '' AND (`title` LIKE CONCAT('%', :query, '%') OR `keywords` LIKE CONCAT('%', :query, '%')) ORDER BY {$sort_sql}",
                "locations" => "SELECT `title`, title_sort, `subcategory`, `type`, index_data FROM pages_links LEFT JOIN pages ON (pages_links.from_pgid = pages.pgid) WHERE (`to` = 'Game location') AND `namespace` = 'Category' AND `redirect_to` = '' AND (`title` LIKE CONCAT('%', :query, '%') OR `keywords` LIKE CONCAT('%', :query, '%')) ORDER BY {$sort_sql}",
                "publishers" => "SELECT `title`, title_sort, `subcategory`, `type`, index_data FROM pages_links LEFT JOIN pages ON (pages_links.from_pgid = pages.pgid) WHERE (`to` = 'Game publisher') AND `namespace` = 'Category' AND `redirect_to` = '' AND (`title` LIKE CONCAT('%', :query, '%') OR `keywords` LIKE CONCAT('%', :query, '%')) ORDER BY {$sort_sql}"
            );
            foreach ($tables as $table => $query) {
                if (stristr($filter, $table)) {
                    $sql[] = $query;
                }
            }
        }

        // Populate $results with results
        $results = [];

        foreach ($sql as $sql) {
            $statement = $this->pdo->prepare($sql);
            $statement->execute(['query' => $query]);

            while ($row = $statement->fetch()) {
                $title_sort = strtolower($row['title_sort']);
                $title = $row['title'];

                if ($row['subcategory']) {
                    $category = str_replace('Game ', '', $row['subcategory']);
                    $title = str_replace(' (' . $category . ')', '', $row['title']);
                } else {
                    $category = $row['type'];
                }

                $item = array(
                    'title' => $title,
                    'title_sort' => $row['title_sort'] . ' ' . $row['type'],
                    'type' => $row['type'],
                    'category' => $category,
                    'links' => array(
                        'page' => getenv('HOST_DOMAIN') . pageURL($row['title'], $row['type']),
                    ),
                    'href' => $this->parseLink($row['id'], $row['type']),
                );

                $results[] = $item;
            }
        }

        // Search albums
        if (! $filter || stristr($filter, "albums")) {
            $mapper = new AlbumMapper();
            $album_results = $mapper->searchBy("title", $query);
            
            foreach ($album_results->getGenerator() as $album) {
                $title_sort = strtolower(formatName($album->parseTitle(), "sortable"));
                $item = array(
                    'title' => $album->parseTitle(),
                    'title_sort' => $title_sort . ' album',
                    'type' => 'album',
                    'category' => 'music',
                    'tag' => 'AlbumID:' . $album->getProp('albumid'),
                    'release_date' => $album->getProp('release'),
                    'links' => array(
                        'page' => getenv('HOST_DOMAIN') . $album->getUrl(),
                    ),
                    'href' => $this->parseLink($album->getId(), 'album'),
                );

                $results[] = $item;
            }
        }
        
        if (empty($results)) {
            throw new APINotFoundException("The requested query `{$query}` returned no results.");
        }

        usort($results, function ($a, $b) use ($sort, $sort_by, $query) {
            if ($sort == 'title_sort') {
                // Prioritize results with exact matches in the title
                if (strtolower($a['title']) == strtolower($query)) return -1;
                if (strtolower($b['title']) == strtolower($query)) return 1;
            }
            
            if ($sort_by == 'DESC') {
                return strcmp($b[$sort], $a[$sort]);
            }
            return strcmp($a[$sort], $b[$sort]);
        });

        $this->setPayload($results)->render(200);
    }

    protected function createFromRequest($body): void
    {
        throw new APIException('Method not supported', null, 'METHOD_NOT_SUPPORTED', 405);
    }

    protected function updateFromRequest($id, $body): void
    {
        throw new APIException('Method not supported', null, 'METHOD_NOT_SUPPORTED', 405);
    }

    protected function delete($id): void
    {
        throw new APIException('Method not supported', null, 'METHOD_NOT_SUPPORTED', 405);
    }
}
