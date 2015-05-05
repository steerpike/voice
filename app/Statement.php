<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Statement extends Model {

	protected $fillable = ['author', 'content', 'url', 'sentiment_label', 'sentiment', 'published'];

}