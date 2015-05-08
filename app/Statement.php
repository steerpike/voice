<?php namespace App;

use Carbon;
use Illuminate\Database\Eloquent\Model;

class Statement extends Model {

	protected $fillable = ['author', 'content', 'url', 
	'site', 'thread',
	'sentiment_label', 'sentiment', 'published'];
	protected $dates = array('published');

	public function scopeNotReviewed($query)
    {
        return $query->where('sentiment', '=', null);
    }
    public function setPublishedAttribute($value)
{
    $this->attributes['published'] = Carbon\Carbon::createFromFormat('d-m-Y H:i:s', $value);
}
}
