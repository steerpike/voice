<?php namespace App\Http\Controllers;

use PHPHtmlParser\Dom;
use App;
use App\Statement;
class GatherController extends Controller {


	public function whirlpool()
	{
		//$fb = App::make('SammyK\LaravelFacebookSdk\LaravelFacebookSdk');
		//Alchemy API key: 45cc3141676b2ecb9466fe028749d685cfe6e776
		//http://access.alchemyapi.com/calls/html/HTMLGetTextSentiment
		$dom = new Dom;

		$forum = 'http://forums.whirlpool.net.au/forum-replies.cfm?t=2402456';
		//$forum = 'http://forums.whirlpool.net.au/forum-replies.cfm?t=2383866&p=18';
		$html = $this->gather($forum);
		$dom->load($html);
		$replies = $dom->find('.reply');
		foreach($replies as $reply) 
		{
			$domtext = new Dom;
			$domtext->load($reply->innerHtml);
			$author = $domtext->find('.username .bu_name')->text;
			$date = $domtext->find('.date')->text;
			$date = $this->parsedate($date);
			$url = $domtext->find('a')[2]->getAttribute('href');
			$content = strip_tags($domtext->find('.replytext')->innerHtml);
			$statement = new Statement;
			$statement->author = $author;
			$statement->content = $content;
			$statement->url = $url;
			$statement->published = $date;
			$statement->save();
		}
	}
	public function sentiment() 
	{
		$statements = Statement::all();
		foreach($statements as $statement)
		{
			$url = 'http://access.alchemyapi.com/calls/html/HTMLGetTextSentiment?'.
			'apikey=f76a71f030cc99780f960dab560d9ddc8aa8eaa2&outputMode=json&'.
			'html='.urlencode($statement->content);
			$response = json_decode($this->gather($url));
			$statement->sentiment = $response->docSentiment->score;
			$statement->sentiment_label = $response->docSentiment->type;
			$statement->save();
		}
	}
	public function gather($url) 
	{
		$ch = curl_init();
		$proxy = "https://localhost:8080";
		curl_setopt($ch, CURLOPT_URL, $url);//return the transfer as a string
        //curl_setopt($ch, CURLOPT_PROXY, 'wwwproxy.vodafone.com.au');
	    //curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'DwyerS:tBa5tdgag4');
	    curl_setopt($ch, CURLOPT_PROXYPORT, 8080);
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, 'http://vodafone.com.au');
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        $output = curl_exec($ch);
        curl_close($ch); 
        return $output;
	}
	public function parsedate($date) 
	{
		if(strpos($date, 'ago')) {
				$date = str_replace('ago', '', trim($date));
				$date = str_replace('Today', '', trim($date));
				$date = date('d-m-Y H:i:s', strtotime('-'.$date));
			} else {
				$date = str_replace('at', '', trim($date));
				$date = str_replace(' pm', 'pm', $date);
				$date = str_replace(' am', 'am', $date);
				$date = strtotime($date);
				$date = date('d-m-Y H:i:s', $date);
			}
		return $date;
	}

}
