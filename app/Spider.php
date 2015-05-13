<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Spider extends Model {

	protected $fillable = ['site', 'last_update'];
	protected $dates = array('last_update');

}
