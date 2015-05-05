<?php namespace App\Http\Controllers;


use App\Statement;
class SentimentController extends Controller {


	public function index()
	{
		return Statement::all();
	}


}
