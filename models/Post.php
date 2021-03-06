<?php namespace Shohabbos\Board\Models;

use Model;
use Carbon\Carbon;

/**
 * Model
 */
class Post extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\Sluggable;
    use \October\Rain\Database\Traits\Nullable;
    
    public $table = 'shohabbos_board_posts';

    public $jsonable = ['attrs'];

    protected $slugs = ['slug' => 'title'];

    public $nullable = ['amount'];

    public $dates = ['published_at'];

    public $guarded = ['id'];

    /**
     * The attributes on which the post list can be ordered
     * @var array
     */
    public static $allowedSortingOptions = [
        'id asc' => 'ID (по возрастанию)',
        'id desc' => 'ID (по убыванию)',
        'title asc' => 'Название (по возрастанию)',
        'title desc' => 'Название (по убыванию)',
        'published_at asc' => 'Дата публикации (по возрастанию)',
        'published_at desc' => 'Дата публикации (по убыванию)',
        'random' => 'Случайный',
    ];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'title' => 'required',
        'content' => 'required',
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

    //
    // Mutators
    //

    public function getAmountAttribute($value) {
        $currency = ($this->currency == 'uzs') ? 'сум' : 'у.е';

        return number_format($value)." ".$currency;
    }

    public function getPlan($id) {
        return $this->plans()->where('plan_id', $id)->first();
    }

    public function isFav() {
        $posts = \Cookie::get('wishlist', []);
        return isset($posts[$this->id]) ? 'active' : '';
    }

    public function loadDetailProperties() {
        $data = [];

        foreach ($this->properties as $key => $value) {
            $label = $value->value;

            if (isset($value->property->values)) {
                $firstVal = $value->property->values()->where('value', $value->value)->first();
                if ($firstVal) {
                    $label = $firstVal->label;
                }
            }

            $data[] = [
                'label' => $value->property->label,
                'value' => $label,
            ];
        }

        return $data;
    }

    //
    // Scopes
    //

    public function scopeIsPublished($query)
    {
        return $query
            ->whereNotNull('published')
            ->where('published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<', Carbon::now())
        ;
    }

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
            'sort'             => 'published_at desc',
            'categories'       => null,
            'category'         => null,
            'search'           => '',
            'published'        => true,
            'location'         => null,
            'properties'       => [],
            'user_id'          => null,
        ], $options));

        $properties = array_filter($properties);
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
                if (empty($value) || (is_array($value) && empty(array_filter($value)))) {
                    continue;
                }

                if (is_array($value)) {
                    $postProperty->where('property_id', $key)->whereBetween('value', $value);
                } else {
                    $postProperty->where('property_id', $key)->where('value', $value);
                }
            }

            $ids = $postProperty->lists('post_id');

            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        if ($published) {
            $query->isPublished();
        }

        /*
         * Properties filter
         */
        if ($user_id) {
            $query->where('user_id', $user_id);
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
