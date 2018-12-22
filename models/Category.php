<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class Category extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\NestedTree;
    use \October\Rain\Database\Traits\Sluggable;
    
    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = [
        'name', 'seo_title',
        'seo_desc', 'seo_keys',
        'slug'
    ];

    protected $slugs = ['slug' => 'name'];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    public $timestamps = false;


    /**
     * @var string The database table used by the model.
     */
    public $table = 'shohabbos_board_categories';

    /**
     * @var array Validation rules
     */
    public $rules = [
    ];

    public $attachOne = [
        'thumb' => 'System\Models\File',
        'photo' => 'System\Models\File'
    ];

    public $hasMany = [
        'posts' => Post::class
    ];


    public $belongsToMany = [
        'properties' => [Property::class, 'table' => 'shohabbos_board_category_property']
    ];
    

    /**
     * Sets the "url" attribute with a URL to this object
     * @param string $pageName
     * @param Cms\Classes\Controller $controller
     */
    public function setUrl($pageName, $controller)
    {
        $params = [
            'id' => $this->id,
            'slug' => $this->slug,
            'category' => $this->slug,
            'categories' => implode("/", $this->getParentsAndSelf()->lists('slug')),
        ];
        
        return $this->url = $controller->pageUrl($pageName, $params, false);
    }
}
