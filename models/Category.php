<?php namespace Shohabbos\Board\Models;

use Model;

/**
 * Model
 */
class Category extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SimpleTree;
    use \October\Rain\Database\Traits\Sortable;
    use \October\Rain\Database\Traits\Nullable;
    
    const SORT_ORDER = 'sort';
    const PARENT_ID = 'parent_id';
    
    public $implement = ['@RainLab.Translate.Behaviors.TranslatableModel'];

    public $translatable = [
        'name', 'seo_title',
        'seo_desc', 'seo_keys',
        'slug'
    ];

    public $nullable = ['parent_id'];

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
        'thumb' => 'System\Models\File'
    ];

    public $hasMany = [
        'children'    => [self::class, 'key' => 'parent_id'],
        'posts' => Post::class
    ];

    public $belongsTo = [
        'parent'    => [self::class, 'key' => 'parent_id'],
    ];

    public $belongsToMany = [
        'properties' => [Property::class, 'table' => 'shohabbos_board_category_property']
    ];
    

    public function getParentIdOptions() {
        return self::where('parent_id', null)
            ->where('id', '!=', $this->id)
            ->lists('name', 'id');
    }

    /**
     * Sets the "url" attribute with a URL to this object
     * @param string $pageName
     * @param Cms\Classes\Controller $controller
     */
    public function setUrl($pageName, $controller)
    {
        $params = [
            'category' => $this->slug,
        ];
        
        return $this->url = $controller->pageUrl($pageName, $params, false);
    }
}
