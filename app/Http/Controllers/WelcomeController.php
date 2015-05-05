<?php namespace App\Http\Controllers;

use PHPHtmlParser\Dom;
use App;
class WelcomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		//$fb = App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');
		//Alchemy API key: 45cc3141676b2ecb9466fe028749d685cfe6e776
		//http://access.alchemyapi.com/calls/html/HTMLGetTextSentiment
		$dom = new Dom;
		$html = $this->gather('http://forums.whirlpool.net.au/forum-replies.cfm?t=2402456');
		$dom->load($html);
		$replies = $dom->find('.replytext');
		$domtext = new Dom;
		$domtext->load($replies[0]->innerHtml);
		$content = $domtext->find('.op')->innerHtml;
		dd($replies[0]->innerHtml);
		//return view('welcome');
	}
	public function gather($url) 
	{
		$ch = curl_init();
		$proxy = "https://localhost:8080";
		curl_setopt($ch, CURLOPT_URL, $url);//return the transfer as a string
        curl_setopt($ch, CURLOPT_PROXY, 'wwwproxy.vodafone.com.au');
	    curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'DwyerS:tBa5tdgag4');
	    curl_setopt($ch, CURLOPT_PROXYPORT, 8080);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, 'http://vodafone.com.au');
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $output = curl_exec($ch);
        curl_close($ch); 
        return $output;
	}

}
