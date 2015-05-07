<?php namespace App\Http\Controllers;


use App\Statement;
use DB;
class SentimentController extends Controller {


	public function index()
	{
		$statements = Statement::whereNotNull('sentiment')->orderBy('published', 'DESC')->get();
		$highest = Statement::whereNotNull('sentiment')->orderBy('sentiment', 'DESC')->first();
		$lowest = Statement::whereNotNull('sentiment')->orderBy('sentiment', 'ASC')->first();
		$average = DB::table('statements')->avg('sentiment');
		if(!$statements || !$highest || !$lowest || !$average) {
			return 'Empty DB';
		}
		return view('voices', compact('statements', 'highest', 'lowest','average'));
	}
}
