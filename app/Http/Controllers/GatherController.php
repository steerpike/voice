<?php namespace App\Http\Controllers;

use PHPHtmlParser\Dom;
use App;
use Illuminate\Http\Request;
use App\Statement;
class GatherController extends Controller {


	public function whirlpool(Request $request)
	{
		//Alchemy API key: 45cc3141676b2ecb9466fe028749d685cfe6e776
		//http://access.alchemyapi.com/calls/html/HTMLGetTextSentiment
		if ($request->route('id'))
		{
			$dom = new Dom;
			$thread = 'http://forums.whirlpool.net.au/forum-replies.cfm?t='.$request->route('id');
			$html = $this->gather($thread);
			$dom->load($html);
			$replies = $dom->find('.reply');
			foreach($replies as $reply) 
			{
				$domtext = new Dom;
				$domtext->load($reply->innerHtml);
				$author = $domtext->find('.username .bu_name')->text;
				$date = $domtext->find('.date')->text;
				$date = $this->parsedate($date);
				$hash = $domtext->find('a')[2]->getAttribute('href');
				$content = strip_tags($domtext->find('.replytext')->innerHtml);
				$url = 'http://forums.whirlpool.net.au/'.$hash;
				$site = 'whirlpool';
				$statement = new Statement;
				$model = $statement::firstOrCreate(['author'=>$author, 
					'content'=>$content,
					'url'=>$url,
					'thread'=>$thread,
					'site'=>$site, 
					'published'=>$date]);
			}
			return 'Inserted '.count($replies).' records';
		}
	}
	public function facebook(Request $request) {
		//https://graph.facebook.com/{post_id}/comments?access_token={access_token}
		//https://graph.facebook.com/{post_id}/comments
		$token = $this->gather("https://graph.facebook.com/oauth/".
			"access_token?grant_type=client_credentials&client_id=".getenv('FACEBOOK_APP_ID')."&".
			"client_secret=".getenv('FACEBOOK_APP_SECRET'));
		if ($request->route('id'))
		{
			$id = $request->route('id');
			$thread = 'https://www.facebook.com/vodafoneau/posts/'.$id;
			$comments = json_decode($this->gather('https://graph.facebook.com/'.$id.'/comments?'.$token));		
			foreach($comments->data as $comment) 
			{
				$statement = new Statement;
				if($comment->from->name=="Vodafone Australia")
				{
					continue; //skip out here and gte the next one
				}
				$statement->author = $comment->from->name;
				$statement->thread = $thread;
				$statement->site = 'facebook';
				$statement->content = $comment->message;
				$split = explode('_',$comment->id);
				$url = $thread.'?comment_id='.$split[1];
				$statement->url = $url;
				$statement->published = $comment->created_time;
				$statement->save();
			}
			return 'Inserted '.count($comments->data).' records';
		}

	}
	public function sentiment() 
	{
		$statements = Statement::where('sentiment','=', null)->get();
		foreach($statements as $statement)
		{
			$url = 'http://access.alchemyapi.com/calls/html/HTMLGetTextSentiment?'.
			'apikey=f76a71f030cc99780f960dab560d9ddc8aa8eaa2&outputMode=json&'.
			'html='.urlencode($statement->content);
			$response = json_decode($this->gather($url));
			if($response->status !="ERROR") 
			{
				if(property_exists($response->docSentiment, 'score')) 
				{
					$statement->sentiment = $response->docSentiment->score;
				} else {
					$statement->sentiment = 0;
				}
				$statement->sentiment_label = $response->docSentiment->type;
				$statement->save();
			}
		}
		return 'Updated '.count($statements).' records';
	}

	public function spider() 
	{
		
		$url = 'http://forums.whirlpool.net.au/forum/114?g=141';
		$dom = new Dom;
		$html = $this->gather($url);
		$dom->load($html);

		$threads = $dom->find('#threads');
		$rows = $threads->find('table tr');
		foreach($rows as $row) 
		{
			$thread = $row->find('td a.title')->getAttribute('href');
			$updated_string = $row->find('td.newest span')->text;
			$last_page = $row->find('td.goend a')->getAttribute('href');
			print_r($thread.' '.$updated_string.' '.$last_page.'<br />');
		}
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
				if(strpos($date, 'Monday')===0 ||  strpos($date, 'Tuesday')===0 || 
					strpos($date, 'Wednesday')===0 || strpos($date, 'Thursday')===0 ||
					strpos($date, 'Friday')===0)
				{
					$date = 'last '.$date;
				}
				//echo($date.'<br />');
				$date = strtotime($date);
				$date = date('d-m-Y H:i:s', $date);
			}
		return $date;
	}

}
