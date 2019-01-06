<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class Post extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\Nullable;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_PENDING = 'pending';
    
    public $table = 'shohabbos_board_posts';

    public $jsonable = ['attrs'];

    public $guarded = ['id', 'url'];

    public $slugs = ['slug' => 'title'];

    public $nullable = ['amount'];

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

    public static $allowedStatusOptions = [
        self::STATUS_ACTIVE => 'Активный',
        self::STATUS_INACTIVE => 'Неактивный',
        self::STATUS_PENDING => 'В ожидании',
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
        'category' => Category::class,
        'location' => Location::class,
    ];
    
    public $belongsToMany = [
        'plans' => [
            Plan::class, 
            'table' => 'shohabbos_board_post_plan', 
            'timestamps' => true,
            'pivot' => ['created_at', 'updated_at', 'expires_at']
        ]
    ];
    
    public $hasMany = [
        'properties' => PostProperty::class,
    ];

    public function getStatusOptions() {
        return self::$allowedStatusOptions;
    }

    //
    // Mutators
    //

    public function getAmountAttribute($value) {
        return number_format($value);
    }

    public function getPlan($id) {
        return $this->plans()->where('plan_id', $id)->first();
    }

    public function isFav() {
        $posts = \Cookie::get('wishlist', []);
        return isset($posts[$this->id]) ? 'active' : '';
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
            'category'         => null,
            'search'           => '',
            'published'        => true,
            'location'         => null,
            'properties'       => null,
            'user_id'          => null,
            'status'           => null,
        ], $options));

        $searchableFields = ['title', 'content', 'slug'];

        /*
         * Properties filter
         */
        if ($properties && is_array($properties)) {
            $postProperty = PostProperty::where(1);

            if ($category) {
                $postProperty = PostProperty::where('category_id', $category);
            }


            foreach ($properties as $key => $value) {
                if (empty($value)) {
                    continue;
                }

                if (is_array($value)) {
                    $postProperty->where('property_id', $key)
                        ->whereBetween('value', $value);
                } else {
                    $postProperty->where('property_id', $key)->where('value', $value);
                }
            }

            $ids = $postProperty->lists('post_id');

            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        /*
         * Properties filter
         */
        if ($user_id) {
            $query->where('user_id', $user_id);
        }
        
        if ($status) {
            $query->where('status', $status);
        }

        /*
         * Location filter
         */
        if ($location) {
            $query->where('location_id', $location);
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
         * Category, including children
         */
        if ($category !== null) {
            $category = Category::find($category);
            $categories = $category->getAllChildren()->lists('id');
            $categories[] = $category->id;
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
