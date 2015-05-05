<?php namespace App\Http\Controllers;


use App\Statement;
class SentimentController extends Controller {


	public function index()
	{
		$statements = Statement::all();
		//dd($statements);
		return view('voices');
	}


}
