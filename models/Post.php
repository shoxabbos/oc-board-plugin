<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class Post extends Model
{
    use \October\Rain\Database\Traits\Validation;
    

    /**
     * @var string The database table used by the model.
     */
    public $table = 'shohabbos_board_posts';

    public $jsonable = ['attrs'];

    /**
     * The attributes on which the post list can be ordered
     * @var array
     */
    public static $allowedSortingOptions = [
        'title asc' => 'Title (ascending)',
        'title desc' => 'Title (descending)',
        'created_at asc' => 'Created (ascending)',
        'created_at desc' => 'Created (descending)',
        'updated_at asc' => 'Updated (ascending)',
        'updated_at desc' => 'Updated (descending)',
        'random' => 'Random'
    ];

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $attachMany = [
        'images' => 'System\Models\File'
    ];

    public $belongsTo = [
        'user' => 'RainLab\User\Models\User',
        'category' => Category::class
    ];
    
    public $belongsToMany = [
        'plans' => [Plan::class, 'table' => 'shohabbos_board_post_plan']
    ];
    
    public $hasMany = [
        'properties' => PostProperty::class,
    ];

    public function getStatusOptions() {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'pending' => 'Pending',
        ];
    }








    //
    // Scopes
    //

    /**
     * Lists posts for the front end
     *
     * @param        $query
     * @param  array $options Display options
     *
     * @return Post
     */
    public function scopeListFrontEnd($query, $options)
    {
        /*
         * Default options
         */
        extract(array_merge([
            'page'             => 1,
            'perPage'          => 30,
            'sort'             => 'created_at',
            'categories'       => null,
            'exceptCategories' => null,
            'category'         => null,
            'search'           => '',
            'published'        => true,
            'exceptPost'       => null,
        ], $options));

        $searchableFields = ['title', 'content'];
        
        /*
         * Ignore a post
         */
        if ($exceptPost) {
            if (is_numeric($exceptPost)) {
                $query->where('id', '<>', $exceptPost);
            }
            else {
                $query->where('slug', '<>', $exceptPost);
            }
        }
        /*
         * Sorting
         */
        if (in_array($sort, array_keys(static::$allowedSortingOptions))) {
            if ($sort == 'random') {
                $query->inRandomOrder();
            } else {
                @list($sortField, $sortDirection) = explode(' ', $sort);
                if (is_null($sortDirection)) {
                    $sortDirection = "desc";
                }
                $query->orderBy($sortField, $sortDirection);
            }
        }

        /*
         * Search
         */
        $search = trim($search);
        if (strlen($search)) {
            $query->searchWhere($search, $searchableFields);
        }
        /*
         * Categories
         */
        if ($categories !== null) {
            $categories = is_array($categories) ? $categories : [$categories];
            $query->whereIn('category_id', $categories);
        }

        /*
         * Except Categories
         */
        if ($exceptCategories !== null) {
            $exceptCategories = is_array($exceptCategories) ? $exceptCategories : [$exceptCategories];
            $query->whereNotIn('category_id', $exceptCategories);
        }

        /*
         * Category, including children
         */
        if ($category !== null) {
            $category = Category::find($category);
            $categories = $category->getAllChildrenAndSelf()->lists('id');
            $query->whereIn('category_id', $categories);
        }

        return $query->paginate($perPage, $page);
    }

    /**
     * Allows filtering for specifc categories
     * @param  Illuminate\Query\Builder  $query      QueryBuilder
     * @param  array                     $categories List of category ids
     * @return Illuminate\Query\Builder              QueryBuilder
     */
    public function scopeFilterCategories($query, $categories)
    {
        return $query->whereIn('category_id', $categories);
    }


    


    /**
     * Sets the "url" attribute with a URL to this object
     * @param string $pageName
     * @param Cms\Classes\Controller $controller
     */
    public function setUrl($pageName, $controller)
    {
        $params = [
            'id'   => $this->id,
            'slug' => $this->slug,
        ];
        $params['category'] = $this->category ? $this->category->slug : null;
        
        //expose published year, month and day as URL parameters
        if ($this->published) {
            $params['year'] = $this->published_at->format('Y');
            $params['month'] = $this->published_at->format('m');
            $params['day'] = $this->published_at->format('d');
        }

        return $this->url = $controller->pageUrl($pageName, $params);
    }


}
