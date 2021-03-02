<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Searchable\Searchable;
use Spatie\Searchable\SearchResult;

class Product extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name', 'description', 'gender', 'category_id'
    ];
    
    public function details() {
        return $this->hasMany(ProductDetails::class);
    }

    public function category() {
        return $this->belongsTo(Category::class, 'category_id');
    }

    /*public function getSearchResult(): SearchResult {
       $url = route('product.show', $this->id);
         
       return new SearchResult($this, $this->name, $url);
    }*/
}
