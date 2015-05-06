<?php namespace App\Http\Controllers;


use App\Statement;
use DB;
class SentimentController extends Controller {


	public function index()
	{
		$statements = Statement::orderBy('published', 'ASC')->get();
		$highest = Statement::orderBy('sentiment', 'DESC')->first();
		$lowest = Statement::orderBy('sentiment', 'ASC')->first();
		$average = DB::table('statements')->avg('sentiment');
		if(!$statements || !$highest || !$lowest || !$average) {
			return 'Empty DB';
		}
		return view('voices', compact('statements', 'highest', 'lowest','average'));
	}
}
