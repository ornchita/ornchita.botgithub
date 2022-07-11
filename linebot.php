<?php

//---------------bot--------------------///
$access_token = 'DdpSTv6Uqr27sP+tFqF1poQGxMxRuERJjt4WF6zHUgjXkgY295RrXWrDlCyBifwXAzApCVcYLjgZ7dfDiKQ0kAEasY+OBT03FRRFIm0ImIEixIipLcz7M78Qa1HneZ9CzE+Tj2276n0RK+rKiyuRjQdB04t89/1O/w1cDnyilFU=';

$channelAccessToken = 'DdpSTv6Uqr27sP+tFqF1poQGxMxRuERJjt4WF6zHUgjXkgY295RrXWrDlCyBifwXAzApCVcYLjgZ7dfDiKQ0kAEasY+OBT03FRRFIm0ImIEixIipLcz7M78Qa1HneZ9CzE+Tj2276n0RK+rKiyuRjQdB04t89/1O/w1cDnyilFU=';

$channelSecret = '029761f917e3395928f28a0c1c599165';

//---------------bot--------------------///


date_default_timezone_set('Asia/Bangkok');
header('Content-Type: text/html; charset=utf-8');

@ini_set('display_errors', '0'); //ไม่แสดงerror



// for test debug file
require_once('LINEBotTiny.php');


$client = new LINEBotTiny($channelAccessToken, $channelSecret);
$botName = "BOT";


function hbd(){
  $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, "https://gisweb2.pwa.co.th/line_service/bot/gis-pwa.php?send=hbd");
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 0);
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($curl);
    curl_close($curl);
}


function get_profile($fullurl) 
{
        $channelAccessToken2 = 'DdpSTv6Uqr27sP+tFqF1poQGxMxRuERJjt4WF6zHUgjXkgY295RrXWrDlCyBifwXAzApCVcYLjgZ7dfDiKQ0kAEasY+OBT03FRRFIm0ImIEixIipLcz7M78Qa1HneZ9CzE+Tj2276n0RK+rKiyuRjQdB04t89/1O/w1cDnyilFU=';
 
        $header = array(
            "Content-Type: application/json",
            'Authorization: Bearer '.$channelAccessToken2,
        );
 
         
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);      
        curl_setopt($ch, CURLOPT_FAILONERROR, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $fullurl);
         
        $returned =  curl_exec($ch);
        curl_close($ch);
        return($returned);
}


//-----------auto send----push message------------------//

if ( $_GET['send'] == 'noti' )
{
	//send_noti();
}
//----

if ( $_GET['send'] == 'text' )
{
	$text = array(
			'type' => 'text',
			'text' => $_GET['text']
		);
	$uid = $_GET['id'];
	$client->pushMessage($uid, $text);
}
//-----------auto send----push message------------------//


// Get POST body content
$content = file_get_contents('php://input');
// Parse JSON
$events = json_decode($content, true);
// Validate parsed JSON data
if (!is_null($events['events'])) {
	// Loop through each event
	foreach ($events['events'] as $event) {
		$uid = $event['source']['userId'];
		$gid = $event['source']['groupId'];


		// Reply only when message sent is in 'text' format
		if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
			// Get text sent

			$text = $event['message']['text'];
			$uid = $event['source']['userId'];
			$gid = $event['source']['groupId'];

			$timestamp = $event['timestamp'];

			if (preg_match('(#สถานการณ์โควิด|#สรุปโควิด)', $text) === 1) {

				$handle1 = curl_init();
				 
				$url1 = "https://covid19.th-stat.com/api/open/today";
				 
				// Set the url
				curl_setopt($handle1, CURLOPT_URL, $url1);
				// Set the result output to be a string.
				curl_setopt($handle1, CURLOPT_RETURNTRANSFER, true);
				 
				$output1 = curl_exec($handle1);
				 
				curl_close($handle1);
				$obj = json_decode($output1); 

				$text_reply = "สถานการณ์โควิด-19
				\n $obj->UpdateDate 
				\n พบผู้ป่วยรายใหม่ จำนวน $obj->NewConfirmed ราย
				\n มีผู้ติดเชื้อสะสม $obj->Confirmed ราย
				\n เสียชีวิตเพิ่มขึ้น $obj->NewDeaths ราย (สะสม $obj->Deaths ราย)
				\n รักษาหาย $obj->NewRecovered ราย (รวม $obj->Recovered ราย)
				\n กำลังรักษาอยู่ใน รพ. $obj->Hospitalized ราย
				\n แหล่งที่มา: $obj->Source
				";


				$messages = [
				'type' => 'text',
				'text' => $text_reply 
				];
			
			}

			else if (preg_match('(ทดสอบ|เพื่อน|เพื่อนกัน)', $text) === 1) {
				//$gid = $event['source']['groupId'];
				$uid = $event['source']['userId'];

				$url = 'https://api.line.me/v2/bot/profile/'.$uid;
				//$url ='https://api.line.me/v2/bot/group/'.$gid.'/member/'.$uid;
				$profile = get_profile($url);
				$obj = json_decode($profile);

				$nameid = $obj->displayName;
				$status = $obj->statusMessage;
				$pathpic = explode("cdn.net/", $obj->pictureUrl);

				if ($nameid){

					$messages = [
					'type' => 'text',
					'text' => 'เพื่อนกันตลอดไป',
					'sender' => [
						'name' => $nameid,
						'iconUrl' => 'https://obs.line-apps.com/'.$pathpic[1]
					]
					];

				}
				else{
					$messages = [
					'type' => 'text',
					'text' => 'ใครอ่ะๆ ไม่เห็นรู้จัก'
					];

				}


			}

			else if(preg_match('(สวัสดี|สวัสดีครับ|สวัสดีค่ะ)', $text) === 1) {	

					$gid = $event['source']['groupId'];
					$uid = $event['source']['userId'];


					//$url = 'https://api.line.me/v2/bot/group/'.$gid.'/member/'.$uid; //กลุ่ม
					$url = 'https://api.line.me/v2/bot/profile/'.$uid;			//user
					$channelAccessToken2 = 'DdpSTv6Uqr27sP+tFqF1poQGxMxRuERJjt4WF6zHUgjXkgY295RrXWrDlCyBifwXAzApCVcYLjgZ7dfDiKQ0kAEasY+OBT03FRRFIm0ImIEixIipLcz7M78Qa1HneZ9CzE+Tj2276n0RK+rKiyuRjQdB04t89/1O/w1cDnyilFU=';

					$header = array(
						"Content-Type: application/json",
						'Authorization: Bearer '.$channelAccessToken2,
					);
					$ch = curl_init();
					//curl_setopt($ch, CURLOPT_HTTP_VERSION, 'CURL_HTTP_VERSION_1_1');
					//curl_setopt($ch, CURLOPT_VERBOSE, 1);
					//curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($ch, CURLOPT_FAILONERROR, 0);		;
					//curl_setopt($ch, CURLOPT_HTTPGET, 1);
					//curl_setopt($ch, CURLOPT_USERAGENT, $agent);
					//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
					curl_setopt($ch, CURLOPT_URL, $url);
					
					$profile =  curl_exec($ch);
					curl_close($ch);
					$obj = json_decode($profile);

					$pathpic = explode("cdn.net/", $obj->pictureUrl);


					$messages = [
							"type" => "text",
							//"text" =>  "สวัสดี คุณ ".$obj->displayName
							"text" =>  "สวัสดี คุณ ".$obj->displayName."(".$obj->statusMessage.")"
					];

			}
	

			else if ($text == '#id') {
				$gid = $event['source']['groupId'];
				$uid = $event['source']['userId'];
				// Build message to reply back
				$messages = [
				'type' => 'text',
				"text" => 'uid:'.$uid.'\n'.'gid'.$gid
				];
			}


			else {
				
				/*
				$text_reply = "ยังไม่มีคำตอบ";

				// Build message to reply back
				$messages = [
				'type' => 'text',
				//'text' => $text
				"text" => $text_reply." ".$uid
				//"text" => $text_reply

				];

				*/

			}



			// Get replyToken
			$replyToken = $event['replyToken'];


			// Make a POST Request to Messaging API to reply to sender
			$url = 'https://api.line.me/v2/bot/message/reply';
			$data = [
				'replyToken' => $replyToken,
				'messages' => [$messages],

			];
			$post = json_encode($data);
			$headers = array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token);

			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
			$result = curl_exec($ch);
			curl_close($ch);

			echo $result . "\r\n";
		}


	}
}


echo "OK";


//----------------------------จบฟังก์ชั่น ReplyMessage----------------------------------//
function replyMsg1($event, $client)
{
    $uid;
    $gid;
    $ty  = $event['source']['type'];    //user,group
    //$uid = $event['source']['userId'];
    //$gid = $event['source']['groupId'];
    if($event['source']['userId']){
        $uid = $event['source']['userId'];
    }
    if($event['source']['groupId']){
        $uid = $event['source']['groupId'];
    }
 
    $id = $event['message']['id'];
 

	if ($event['type'] == 'follow') {

		$id = $event['source']['userId'];
		$urlp = 'https://api.line.me/v2/bot/profile/'.$id;
		$channelAccessToken2 = 'DdpSTv6Uqr27sP+tFqF1poQGxMxRuERJjt4WF6zHUgjXkgY295RrXWrDlCyBifwXAzApCVcYLjgZ7dfDiKQ0kAEasY+OBT03FRRFIm0ImIEixIipLcz7M78Qa1HneZ9CzE+Tj2276n0RK+rKiyuRjQdB04t89/1O/w1cDnyilFU=';

		$header = array(
			"Content-Type: application/json",
			'Authorization: Bearer '.$channelAccessToken2,
		);

		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_HTTP_VERSION, 'CURL_HTTP_VERSION_1_1');
		//curl_setopt($ch, CURLOPT_VERBOSE, 1);
		//curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);       ;
		//curl_setopt($ch, CURLOPT_HTTPGET, 1);
		//curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_URL, $urlp);
		 
		$profile =  curl_exec($ch);
		curl_close($ch);
		$obj = json_decode($profile);


		$pathpic = explode("cdn.net/", $obj->pictureUrl);
	 
			$t=array("ขอบคุณที่แอดเราเป็นเพื่อนนะ","ขอบคุณที่แอดเราเป็นเพื่อนนะ","ขอบคุณที่แอดเราเป็นเพื่อนนะ","ขอบคุณที่แอดเราเป็นเพื่อนนะ");
			$random_keys=array_rand($t,1);
			$txt = $t[$random_keys];
			$a = array(
						array(
							'type' => 'text',
							//'text' => $txt." เพิ่ม id:".$sec_id[0]." count:".$count
							'text' => $txt
						)
					);
			$client->replyMessage1($event['replyToken'],$a);


			/*
			$t=array("ยินดีต้อนรับกลับมาเป็นเพื่อนครับ","อย่าบล็อคผมอีกนะครับ");
			$random_keys=array_rand($t,1);
			$txt = $t[$random_keys];
			//$txt = 'มีคำถามนี้แล้ว-อัพเดท $oid:';
			$a = array(
						array(
							'type' => 'text',
							//'text' => $txt." อัพเดท id:".$q_json_oid." count:".$count
							'text' => $txt
						)
					);
			$client->replyMessage1($event['replyToken'],$a);
			*/

	}
	else if ($event['type'] == 'unfollow') {

		$id = $event['source']['userId'];
		$urlp = 'https://api.line.me/v2/bot/profile/'.$id;
		$channelAccessToken2 = 'DdpSTv6Uqr27sP+tFqF1poQGxMxRuERJjt4WF6zHUgjXkgY295RrXWrDlCyBifwXAzApCVcYLjgZ7dfDiKQ0kAEasY+OBT03FRRFIm0ImIEixIipLcz7M78Qa1HneZ9CzE+Tj2276n0RK+rKiyuRjQdB04t89/1O/w1cDnyilFU=';

		$header = array(
			"Content-Type: application/json",
			'Authorization: Bearer '.$channelAccessToken2,
		);

		$ch = curl_init();
		//curl_setopt($ch, CURLOPT_HTTP_VERSION, 'CURL_HTTP_VERSION_1_1');
		//curl_setopt($ch, CURLOPT_VERBOSE, 1);
		//curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_FAILONERROR, 0);       ;
		//curl_setopt($ch, CURLOPT_HTTPGET, 1);
		//curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_URL, $urlp);
		 
		$profile =  curl_exec($ch);
		curl_close($ch);
		$obj = json_decode($profile);


		$pathpic = explode("cdn.net/", $obj->pictureUrl);
	 

			$t=array("Unfriend เราไปแล้ว","เสียใจ");
			$random_keys=array_rand($t,1);
			$txt = $t[$random_keys];
			$a = array(
						array(
							'type' => 'text',
							//'text' => $txt." เพิ่ม id:".$sec_id[0]." count:".$count
							'text' => $txt
						)
					);
			$client->replyMessage1($event['replyToken'],$a);


	}
	else if ($event['type'] == 'join') {

		$a = array(
					array(
						'type' => 'text',
						'text' => 'ขอบคุณที่เชิญเข้ากลุ่มจ้า'            
					)
				);
		$client->replyMessage1($event['replyToken'],$a);
	}
	else if ($event['type'] == 'leave') {

		$a = array(
					array(
						'type' => 'text',
						'text' => 'ถูกออกจากกลุ่ม'            
					)
				);
		$client->replyMessage1($event['replyToken'],$a);
	}
	else if ($event['type'] == 'memberJoined') {

		$a = array(
					array(
						'type' => 'text',
						'text' => 'ฮัลโหลๆ แนะนำตัวหน่อย สมาชิกใหม่'            
					)
				);
		$client->replyMessage1($event['replyToken'],$a);
	}
	else if ($event['type'] == 'memberLeft') {

		$a = array(
					array(
						'type' => 'text',
						'text' => 'RIP. เสียใจด้วย คุณไม่ได้ไปต่อ'            
					)
				);
		$client->replyMessage1($event['replyToken'],$a);
	}
	else if ($event['type'] == 'postback') {

		$a = array(
					array(
						'type' => 'text',
						'text' => $event['postback']['data']           
					)
				);
		$client->replyMessage1($event['replyToken'],$a);
	}

	elseif ($event['type'] == 'beacon') {	
		$txt;
		if($event['beacon']['type'] == 'enter'){
			$txt = 'in '.'hwid='.$event['beacon']['hwid'].'   '.'type='.$event['beacon']['type'];	
		}
		elseif($event['beacon']['type'] == 'leave'){
			$txt = 'out '.'hwid='.$event['beacon']['hwid'].'   '.'type='.$event['beacon']['type'];	
		}
		
		$a = array(
					array(
						'type' => 'text',
						'text' => $txt           
					)
				);
		$client->replyMessage1($event['replyToken'],$a);

	}
}
function replyMsg($event, $client)
{

    $uid;
    $gid;
    $ty  = $event['source']['type'];    //user,group
    //$uid = $event['source']['userId'];
    //$gid = $event['source']['groupId'];
    if($event['source']['userId']){
        $uid = $event['source']['userId'];
    }
    if($event['source']['groupId']){
        $uid = $event['source']['groupId'];
    }
 
    $id = $event['message']['id'];
 

    //-----ถ้ามีการส่งข้อความText------------------------------------------------------------//
    if ($event['type'] == 'message' && $event['message']['type'] == 'text') {
        //ข้อความtext ที่ได้รับ
        $msg = $event['message']['text'];
 

            $ty = $event['source']['type']; //user,group
 

                if (preg_match('(เสียใจ|ร้องไห้|ไม่ต้องร้อง|ผิดหวัง)', $msg) === 1) {
                    $a = array(
                                array(
                                    'type' => 'sticker',
                                    'packageId' => 2,
                                    'stickerId' => 152
                                )
                            );
                    $client->replyMessage1($event['replyToken'],$a);
                }
 	
 			
			
                else if (preg_match('(น่ารัก|น่ารักนะ|น่ารักจัง)', $msg) === 1) {
 
                    $t=array("ขอบคุณสำหรับคำชม","เขินจุง","ลอยแล้วๆ");
                    $random_keys=array_rand($t,1);
                    $txt = $t[$random_keys];
                    $a = array(
                                array(
                                    'type' => 'text',
                                    'text' => $txt          
                                )
                            );
                    $client->replyMessage1($event['replyToken'],$a);
                }
 
 
				else if (preg_match('(#ดุ๊กดิ๊ก|#ดุ้กดิ้ก|#ดุกดิก|#ดุ๊กดิ้ก|#ดุ้กดิ๊ก)', $msg) === 1) {

					$sticker=array("11537,52002734","11537,52002735","11537,52002736","11537,52002737","11537,52002738","11537,52002739","11537,52002740","11537,52002741","11537,52002742","11537,52002743","11537,52002744","11537,52002745","11537,52002746","11537,52002747","11537,52002748","11537,52002749","11537,52002750","11537,52002751","11537,52002752","11537,52002753","11537,52002754","11537,52002755","11537,52002756","11537,52002757","11537,52002758","11537,52002759","11537,52002760","11537,52002761","11537,52002762","11537,52002763","11537,52002764","11537,52002765","11537,52002766","11537,52002767","11537,52002768","11537,52002769","11537,52002770","11537,52002771","11537,52002772","11537,52002773","11538,51626494","11538,51626495","11538,51626496","11538,51626497","11538,51626498","11538,51626499","11538,51626500","11538,51626501","11538,51626502","11538,51626503","11538,51626504","11538,51626505","11538,51626506","11538,51626507","11538,51626508","11538,51626509","11538,51626510","11538,51626511","11538,51626512","11538,51626513","11538,51626514","11538,51626515","11538,51626516","11538,51626517","11538,51626518","11538,51626519","11538,51626520","11538,51626521","11538,51626522","11538,51626523","11538,51626524","11538,51626525","11538,51626526","11538,51626527","11538,51626528","11538,51626529","11538,51626530","11538,51626531","11538,51626532","11538,51626533","11539,52114110","11539,52114111","11539,52114112","11539,52114113","11539,52114114","11539,52114115","11539,52114116","11539,52114117","11539,52114118","11539,52114119","11539,52114120","11539,52114121","11539,52114122","11539,52114123","11539,52114124","11539,52114125","11539,52114126","11539,52114127","11539,52114128","11539,52114129","11539,52114130","11539,52114131","11539,52114132","11539,52114133","11539,52114134","11539,52114135","11539,52114136","11539,52114137","11539,52114138","11539,52114139","11539,52114140","11539,52114141","11539,52114142","11539,52114143","11539,52114144","11539,52114145","11539,52114146","11539,52114147","11539,52114148","11539,52114149");
	

					$random_keys=array_rand($sticker,1);
					$txt = $sticker[$random_keys];

					$split = explode(",", $txt);
					$p = $split[0];
					$s = $split[1];
					//echo $split[0];
					
					$client->replyMessage1($event['replyToken'],array(
							array(
								'type' => 'sticker',
								'packageId' => $p,
								'stickerId' => $s,
								'stickerResourceType'=> 'ANIMATION'
							)
							/*
							array(
								'type' => 'sticker',
								'packageId' => $p,
								'stickerId' => $s,
								//'stickerResourceType'=> 'STATIC'
							),
							array(
								'type' => 'sticker',
								'packageId' => 3,
								'stickerId' => 232
							),
								array(
								'type' => 'sticker',
								'packageId' => 3,
								'stickerId' => 233
							)       
							*/
					 
					)
						);
				}


                else if (preg_match('(วันนี้|วันอะไร)', $msg) === 1) {

					$today = date("Y-m-d");
					//$today = "2018-07-01";
					$txt = "";
					$DayOfWeek = date("w", strtotime($today));
					if($DayOfWeek == 0 )  // 0 = Sunday, 6 = Saturday;
					{
						$txt = "วันนี้เป็นวันหยุด(วันอาทิตย์)";
						//echo "$today = <font color=red>Holiday(Sunday)</font><br>";
					}

					else if($DayOfWeek ==6)  // 0 = Sunday, 6 = Saturday;
					{
						$txt = "วันนี้เป็นวันหยุด(วันเสาร์)";
						echo "$today = <font color=red>Holiday(Saturday)</font><br>";
					}


					else{
						$txt = "วันนี้ก็คือวันนี้";
						//echo "$today = <font color=blue>No Holiday</font><br>";

					}


                    $a = array(
                                array(
                                    'type' => 'text',
                                    'text' => $txt          
                                )
                            );
                    $client->replyMessage1($event['replyToken'],$a);
                }


                else if (preg_match('(บอทครับ|บอทคะ|บอทคับ|ดีบอท|สวัสดีครับบอท|สวัสดีบอท|หวัดดีบอท)', $msg) === 1) {
 
                    if ($ty == 'user'){
 
                        $url = 'https://api.line.me/v2/bot/profile/'.$uid;
                        $channelAccessToken2 = 'DdpSTv6Uqr27sP+tFqF1poQGxMxRuERJjt4WF6zHUgjXkgY295RrXWrDlCyBifwXAzApCVcYLjgZ7dfDiKQ0kAEasY+OBT03FRRFIm0ImIEixIipLcz7M78Qa1HneZ9CzE+Tj2276n0RK+rKiyuRjQdB04t89/1O/w1cDnyilFU=';
 
                        $header = array(
                            "Content-Type: application/json",
                            'Authorization: Bearer '.$channelAccessToken2,
                        );
 
                        $ch = curl_init();
                        //curl_setopt($ch, CURLOPT_HTTP_VERSION, 'CURL_HTTP_VERSION_1_1');
                        //curl_setopt($ch, CURLOPT_VERBOSE, 1);
                        //curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($ch, CURLOPT_FAILONERROR, 0);       ;
                        //curl_setopt($ch, CURLOPT_HTTPGET, 1);
                        //curl_setopt($ch, CURLOPT_USERAGENT, $agent);
                        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                        curl_setopt($ch, CURLOPT_URL, $url);
                         
                        $profile =  curl_exec($ch);
                        curl_close($ch);
                        $obj = json_decode($profile);
 
 
                        $pathpic = explode("cdn.net/", $obj->pictureUrl);
 
 
                        $a = array(
 
                                array(
                                    'type' => 'text',
                                    'text' => 'ดีจ้า '.$obj->displayName
                                ),
                                array(
                                    'type' => 'image',
                                    'originalContentUrl' => 'https://obs.line-apps.com/'.$pathpic[1],
                                    'previewImageUrl' => 'https://obs.line-apps.com/'.$pathpic[1].'/large'
                                )
                          //,
                          //    array(
                          //        'type' => 'text',
                          //        'text' => $ty. ' '.$uid. ' '. $gid. ' '.$profile
                          //    )
                          //,
                          //    array(
                          //        'type' => 'text',
                          //        'text' => 'สวัสดีคุณ '.$obj->displayName.' type='.$ty.' uid='.$uid.' gid='.$gid
                          //    )
                          //,
                          //    array(
                          //        'type' => 'text',
                          //        'text' => $obj->statusMessage
                          //    ),
                          //    array(
                          //        'type' => 'text',
                          //        'text' => $obj->pictureUrl
                          //    )
                            );
                        $client->replyMessage1($event['replyToken'],$a);
 
                    }
 
                    else if ($ty == 'group'){
 
 
                        $gid = $event['source']['groupId'];
                        $uid = $event['source']['userId'];
 
                        $url = 'https://api.line.me/v2/bot/group/'.$gid.'/member/'.$uid;
                        //$url = 'https://api.line.me/v2/bot/profile/'.$uid;
                        $channelAccessToken2 = 'DdpSTv6Uqr27sP+tFqF1poQGxMxRuERJjt4WF6zHUgjXkgY295RrXWrDlCyBifwXAzApCVcYLjgZ7dfDiKQ0kAEasY+OBT03FRRFIm0ImIEixIipLcz7M78Qa1HneZ9CzE+Tj2276n0RK+rKiyuRjQdB04t89/1O/w1cDnyilFU=';
 
                        $header = array(
                            "Content-Type: application/json",
                            'Authorization: Bearer '.$channelAccessToken2,
                        );
                        $ch = curl_init();
                        //curl_setopt($ch, CURLOPT_HTTP_VERSION, 'CURL_HTTP_VERSION_1_1');
                        //curl_setopt($ch, CURLOPT_VERBOSE, 1);
                        //curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                        //curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($ch, CURLOPT_FAILONERROR, 0);       ;
                        //curl_setopt($ch, CURLOPT_HTTPGET, 1);
                        //curl_setopt($ch, CURLOPT_USERAGENT, $agent);
                        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
                        curl_setopt($ch, CURLOPT_HEADER, 0);
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                        curl_setopt($ch, CURLOPT_URL, $url);
                         
                        $profile =  curl_exec($ch);
                        curl_close($ch);
                        $obj = json_decode($profile);
 
                        $pathpic = explode("cdn.net/", $obj->pictureUrl);
 
                        $a = array(
 
                                array(
                                    'type' => 'text',
                                    'text' => 'ดีจ้า '.$obj->displayName
                                ),
                                array(
                                    'type' => 'image',
                                    'originalContentUrl' => 'https://obs.line-apps.com/'.$pathpic[1],
                                    'previewImageUrl' => 'https://obs.line-apps.com/'.$pathpic[1].'/large'
                                )
                          //,
                          //    array(
                          //        'type' => 'text',
                          //        'text' => $ty. ' '.$uid. ' '. $gid. ' '.$profile
                          //    )
                          //,
                          //    array(
                          //        'type' => 'text',
                          //        'text' => 'สวัสดีคุณ '.$obj->displayName.' type='.$ty.' uid='.$uid.' gid='.$gid
                          //    )
                          //,
                          //    array(
                          //        'type' => 'text',
                          //        'text' => $obj->statusMessage
                          //    ),
                          //    array(
                          //        'type' => 'text',
                          //        'text' => $obj->pictureUrl
                          //    )
                            );
                        $client->replyMessage1($event['replyToken'],$a);
 
                    }
 
                }
 

				// ยังไม่เสร็จ-----------------------------//
				else if (preg_match('(#เตือน|#เตือน)', $msg) === 1) {
					$ugid;
					if($event['source']['groupId']){
									   $ugid = $event['source']['groupId'];
					}
					else{
									   $ugid = $event['source']['userId'];
					}
					
					$message = '';
					$memo_=array(
						"13-07-2022"=>"ได้หยุดเสียที (13 ก.ค. 65) ",
						"14-07-2022"=>"นี่ก็หยุด (14 ก.ค. 65) ",
						"15-07-2022"=>"สุขไหนจะสุขเท่าศุกร์นี้ (15 ก.ค. 65) ",
					);				

					$today_ = date("d-m-Y");

					$s7d = date("d-m-Y",strtotime("+7 days",strtotime($today_)));
					$s3d = date("d-m-Y",strtotime("+3 days",strtotime($today_)));
					$s2d = date("d-m-Y",strtotime("+2 days",strtotime($today_)));
					$s1d = date("d-m-Y",strtotime("+1 days",strtotime($today_)));


					if(array_key_exists($s7d, $memo_))  // holiday;
					//else if(in_array($today, $holiday))  // holiday;
					{
						$message .= "เหลือเวลาอีก 7 วัน: ".$memo_[$s7d]." ";
					}
					if(array_key_exists($s3d, $memo_))  // holiday;
					//else if(in_array($today, $holiday))  // holiday;
					{
						$message .= "เหลือเวลาอีก 3 วัน: ".$memo_[$s3d]." ";
					}
					if(array_key_exists($s2d, $memo_))  // holiday;
					//else if(in_array($today, $holiday))  // holiday;
					{
						$message .= "เหลือเวลาอีก 2 วัน: ".$memo_[$s2d]." ";
					}
					if(array_key_exists($s1d, $memo_))  // holiday;
					//else if(in_array($today, $holiday))  // holiday;
					{
						$message .= "เหลือเวลาอีก 1 วัน: ".$memo_[$s1d]." ";
					}

					if(array_key_exists($today_, $memo_))
					{
						$message .= "อย่าลืมวันนี้นะ : ".$memo_[$today_]." ";
					}				

                    $a = array(
                                array(
                                    'type' => 'text',
                                    'text' => $message          
                                )
                            );
                    $client->replyMessage1($event['replyToken'],$a);
				}



				else if (preg_match('(#ติดตาม|#ติดตาม)', $msg) === 1) {

						$a = array(
									/*array(
										'type' => 'text',
										'text' => $city.$temp.$aqi.$icon.$level.$face			
									),*/

										/*
										array(
											'type' => 'flex',
											'altText' => 'monitor',
											'contents'=> array(
														  "type"=> "bubble",
														  "size"=> "mega",
														  "header"=> array(
														    "type"=> "box",
														    "layout"=> "vertical",
														    "contents"=> array(
														      array(
														        "type"=> "box",
														        "layout"=> "vertical",
														        "contents"=> array(
														          array(
														            "type"=> "text",
														            "text"=> "เลขคำขอ",
														            "color"=> "#ffffff99",
														            "size"=> "sm"
														          ),
														          array(
														            "type"=> "text",
														            "text"=> "Q65070800001",
														            "color"=> "#ffffff",
														            "size"=> "xl",
														            "flex"=> 4,
														            "weight"=> "bold"
														          )
														        )
														      )
														    ),
														    "paddingAll"=> "20px",
														    "backgroundColor"=> "#0367D3",
														    "spacing"=> "md",
														    "height"=> "80px",
														    "paddingTop"=> "22px"
														  ),
														  /*
														  "body"=> array(
														    "type"=> "box",
														    "layout"=> "vertical",
														    "contents"=> array(
														      array(
														        "type"=> "text",
														        "text"=> "สถานะการให้บริการข้อมูล GIS",
														        "color"=> "#0367D3",
														        "size"=> "md",
														        "style"=> "italic",
														        "weight"=> "bold"
														      ),
														      array(
														        "type"=> "box",
														        "layout"=> "horizontal",
														        "contents"=> array(
														          array(
														            "type"=> "text",
														            "text"=> "08 ก.ค. 65",
														            "size"=> "xs",
														            "gravity"=> "center",
														            "flex"=> 1
														          ),
														          array(
														            "type"=> "box",
														            "layout"=> "vertical",
														            "contents"=> array(
														              array(
														                "type"=> "filler"
														              ),
														              array(
														                "type"=> "box",
														                "layout"=> "vertical",
														                "contents"=> array(),
														                "cornerRadius"=> "30px",
														                "height"=> "12px",
														                "width"=> "12px",
														                "borderColor"=> "#EF454D",
														                "borderWidth"=> "2px"
														              ),
														              array(
														                "type"=> "filler"
														              )
														            ),
														            "flex"=> 0
														          ),
														          array(
														            "type"=> "text",
														            "text"=> "รับคำขอ",
														            "gravity"=> "center",
														            "flex"=> 2,
														            "size"=> "sm",
														            "align"=> "start"
														          )
														        ),
														        "spacing"=> "lg",
														        "cornerRadius"=> "30px",
														        "margin"=> "xl"
														      ),
														      array(
														        "type"=> "box",
														        "layout"=> "horizontal",
														        "contents"=> array(
														          array(
														            "type"=> "box",
														            "layout"=> "baseline",
														            "contents"=> array(
														              array(
														                "type"=> "filler"
														              )
														            ),
														            "flex"=> 2
														          ),
														          array(
														            "type"=> "box",
														            "layout"=> "vertical",
														            "contents"=> array(
														              array(
														                "type"=> "box",
														                "layout"=> "horizontal",
														                "contents"=> array(
														                  array(
														                    "type"=> "filler"
														                  ),
														                  array(
														                    "type"=> "box",
														                    "layout"=> "vertical",
														                    "contents"=> array(),
														                    "width"=> "2px",
														                    "backgroundColor"=> "#B7B7B7"
														                  ),
														                  array(
														                    "type"=> "filler"
														                  )
														                ),
														                "flex"=> 1
														              )
														            ),
														            "width"=> "12px"
														          ),
														          array(
														            "type"=> "text",
														            "text"=> "กนกวรรณ",
														            "gravity"=> "center",
														            "flex"=> 4,
														            "size"=> "xs",
														            "color"=> "#8c8c8c"
														          )
														        ),
														        "spacing"=> "lg",
														        "height"=> "30px"
														      ),
														      array(
														        "type"=> "box",
														        "layout"=> "horizontal",
														        "contents"=> array(
														          array(
														            "type"=> "box",
														            "layout"=> "horizontal",
														            "contents"=> array(
														              array(
														                "type"=> "text",
														                "text"=> "09 ก.ค. 65",
														                "gravity"=> "center",
														                "size"=> "xs",
														                "flex"=> 1
														              )
														            ),
														            "flex"=> 1
														          ),
														          array(
														            "type"=> "box",
														            "layout"=> "vertical",
														            "contents"=> array(
														              array(
														                "type"=> "filler"
														              ),
														              array(
														                "type"=> "box",
														                "layout"=> "vertical",
														                "contents"=> array(),
														                "cornerRadius"=> "30px",
														                "width"=> "12px",
														                "height"=> "12px",
														                "borderWidth"=> "2px",
														                "borderColor"=> "#6486E3"
														              ),
														              array(
														                "type"=> "filler"
														              )
														            ),
														            "flex"=> 0
														          ),
														          array(
														            "type"=> "text",
														            "text"=> "อยู่ระหว่างดำเนินการ",
														            "gravity"=> "center",
														            "flex"=> 2,
														            "size"=> "sm",
														            "align"=> "start"
														          )
														        ),
														        "spacing"=> "lg",
														        "cornerRadius"=> "30px",
														        "margin"=> "xl"
														      ),
														      array(
														        "type"=> "box",
														        "layout"=> "horizontal",
														        "contents"=> array(
														          array(
														            "type"=> "box",
														            "layout"=> "baseline",
														            "contents"=> array(
														              array(
														                "type"=> "filler"
														              )
														            ),
														            "flex"=> 2
														          ),
														          array(
														            "type"=> "box",
														            "layout"=> "vertical",
														            "contents"=> array(
														              array(
														                "type"=> "box",
														                "layout"=> "horizontal",
														                "contents"=> array(
														                  array(
														                    "type"=> "filler"
														                  ),
														                  array(
														                    "type"=> "box",
														                    "layout"=> "vertical",
														                    "contents"=> array(),
														                    "width"=> "2px",
														                    "backgroundColor"=> "#6486E3"
														                  ),
														                  array(
														                    "type"=> "filler"
														                  )
														                ),
														                "flex"=> 1
														              )
														            ),
														            "width"=> "12px"
														          ),
														          array(
														            "type"=> "text",
														            "text"=> "พิมาน",
														            "gravity"=> "center",
														            "flex"=> 4,
														            "size"=> "xs",
														            "color"=> "#8c8c8c"
														          )
														        ),
														        "spacing"=> "lg",
														        "height"=> "30px"
														      ),
														      array(
														        "type"=> "box",
														        "layout"=> "horizontal",
														        "contents"=> array(
														          array(
														            "type"=> "text",
														            "text"=> "11 ก.ค. 65",
														            "gravity"=> "center",
														            "size"=> "xs"
														          ),
														          array(
														            "type"=> "box",
														            "layout"=> "vertical",
														            "contents"=> array(
														              array(
														                "type"=> "filler"
														              ),
														              array(
														                "type"=> "box",
														                "layout"=> "vertical",
														                "contents"=> array(),
														                "cornerRadius"=> "30px",
														                "width"=> "12px",
														                "height"=> "12px",
														                "borderColor"=> "#008000",
														                "borderWidth"=> "2px"
														              ),
														              array(
														                "type"=> "filler"
														              )
														            ),
														            "flex"=> 0
														          ),
														          array(
														            "type"=> "text",
														            "text"=> "ดำเนินการแล้วเสร็จ",
														            "gravity"=> "center",
														            "flex"=> 2,
														            "size"=> "sm"
														          )
														        ),
														        "spacing"=> "lg",
														        "cornerRadius"=> "30px",
														        "margin"=> "xl",
														        "flex"=> 1
														      ),
														      array(
														        "type"=> "box",
														        "layout"=> "horizontal",
														        "contents"=> array(
														          array(
														            "type"=> "box",
														            "layout"=> "baseline",
														            "contents"=> array(
														              array(
														                "type"=> "filler"
														              )
														            ),
														            "flex"=> 2
														          ),
														          array(
														            "type"=> "box",
														            "layout"=> "vertical",
														            "contents"=> array(
														              array(
														                "type"=> "box",
														                "layout"=> "horizontal",
														                "contents"=> array(
														                  array(
														                    "type"=> "filler"
														                  ),
														                  array(
														                    "type"=> "box",
														                    "layout"=> "vertical",
														                    "contents"=> array(),
														                    "width"=> "2px",
														                    "backgroundColor"=> "#008000"
														                  ),
														                  array(
														                    "type"=> "filler"
														                  )
														                ),
														                "flex"=> 1
														              )
														            ),
														            "width"=> "12px"
														          ),
														          array(
														            "type"=> "text",
														            "text"=> "กนกวรรณ",
														            "gravity"=> "center",
														            "flex"=> 4,
														            "size"=> "xs",
														            "color"=> "#8c8c8c"
														          )
														        ),
														        "spacing"=> "lg",
														        "height"=> "30px"
														      )
														    )
														  )
														  */
														)
											
										
											)
										)
										/*
										array(
											'type' => 'flex',
											'altText' => 'monitor',
											'contents'=> array(
														  "type"=> "bubble",
														  "size"=> "mega",
														  "header"=> array(
														    "type"=> "box",
														    "layout"=> "vertical",
														    "contents"=> array(
														      array(
														        "type"=> "box",
														        "layout"=> "vertical",
														        "contents"=> array(
														          array(
														            "type"=> "text",
														            "text"=> "เลขคำขอ",
														            "color"=> "#ffffff99",
														            "size"=> "sm"
														          ),
														          array(
														            "type"=> "text",
														            "text"=> "Q65070800001",
														            "color"=> "#ffffff",
														            "size"=> "xl",
														            "flex"=> 4,
														            "weight"=> "bold"
														          )
														        )
														      )
														    ),
														    "paddingAll"=> "20px",
														    "backgroundColor"=> "#0367D3",
														    "spacing"=> "md",
														    "height"=> "80px",
														    "paddingTop"=> "22px"
														  ),
														  "body"=> array(
														    "type"=> "box",
														    "layout"=> "vertical",
														    "contents"=> array(
														      array(
														        "type"=> "text",
														        "text"=> "สถานะการให้บริการข้อมูล GIS",
														        "color"=> "#0367D3",
														        "size"=> "md",
														        "style"=> "italic",
														        "weight"=> "bold"
														      ),
														      array(
														        "type"=> "box",
														        "layout"=> "horizontal",
														        "contents"=> array(
														          array(
														            "type"=> "text",
														            "text"=> "08 ก.ค. 65",
														            "size"=> "xs",
														            "gravity"=> "center",
														            "flex"=> 1
														          ),
														          array(
														            "type"=> "box",
														            "layout"=> "vertical",
														            "contents"=> array(
														              array(
														                "type"=> "filler"
														              ),
														              array(
														                "type"=> "box",
														                "layout"=> "vertical",
														                "contents"=> array(),
														                "cornerRadius"=> "30px",
														                "height"=> "12px",
														                "width"=> "12px",
														                "borderColor"=> "#EF454D",
														                "borderWidth"=> "2px"
														              ),
														              array(
														                "type"=> "filler"
														              )
														            ),
														            "flex"=> 0
														          ),
														          array(
														            "type"=> "text",
														            "text"=> "รับคำขอ",
														            "gravity"=> "center",
														            "flex"=> 2,
														            "size"=> "sm",
														            "align"=> "start"
														          )
														        ),
														        "spacing"=> "lg",
														        "cornerRadius"=> "30px",
														        "margin"=> "xl"
														      ),
														      array(
														        "type"=> "box",
														        "layout"=> "horizontal",
														        "contents"=> array(
														          array(
														            "type"=> "box",
														            "layout"=> "baseline",
														            "contents"=> array(
														              array(
														                "type"=> "filler"
														              )
														            ),
														            "flex"=> 2
														          ),
														          array(
														            "type"=> "box",
														            "layout"=> "vertical",
														            "contents"=> array(
														              array(
														                "type"=> "box",
														                "layout"=> "horizontal",
														                "contents"=> array(
														                  array(
														                    "type"=> "filler"
														                  ),
														                  array(
														                    "type"=> "box",
														                    "layout"=> "vertical",
														                    "contents"=> array(),
														                    "width"=> "2px",
														                    "backgroundColor"=> "#B7B7B7"
														                  ),
														                  array(
														                    "type"=> "filler"
														                  )
														                ),
														                "flex"=> 1
														              )
														            ),
														            "width"=> "12px"
														          ),
														          array(
														            "type"=> "text",
														            "text"=> "กนกวรรณ",
														            "gravity"=> "center",
														            "flex"=> 4,
														            "size"=> "xs",
														            "color"=> "#8c8c8c"
														          )
														        ),
														        "spacing"=> "lg",
														        "height"=> "30px"
														      ),
														      array(
														        "type"=> "box",
														        "layout"=> "horizontal",
														        "contents"=> array(
														          array(
														            "type"=> "box",
														            "layout"=> "horizontal",
														            "contents"=> array(
														              array(
														                "type"=> "text",
														                "text"=> "09 ก.ค. 65",
														                "gravity"=> "center",
														                "size"=> "xs",
														                "flex"=> 1
														              )
														            ),
														            "flex"=> 1
														          ),
														          array(
														            "type"=> "box",
														            "layout"=> "vertical",
														            "contents"=> array(
														              array(
														                "type"=> "filler"
														              ),
														              array(
														                "type"=> "box",
														                "layout"=> "vertical",
														                "contents"=> array(),
														                "cornerRadius"=> "30px",
														                "width"=> "12px",
														                "height"=> "12px",
														                "borderWidth"=> "2px",
														                "borderColor"=> "#6486E3"
														              ),
														              array(
														                "type"=> "filler"
														              )
														            ),
														            "flex"=> 0
														          ),
														          array(
														            "type"=> "text",
														            "text"=> "อยู่ระหว่างดำเนินการ",
														            "gravity"=> "center",
														            "flex"=> 2,
														            "size"=> "sm",
														            "align"=> "start"
														          )
														        ),
														        "spacing"=> "lg",
														        "cornerRadius"=> "30px",
														        "margin"=> "xl"
														      ),
														      array(
														        "type"=> "box",
														        "layout"=> "horizontal",
														        "contents"=> array(
														          array(
														            "type"=> "box",
														            "layout"=> "baseline",
														            "contents"=> array(
														              array(
														                "type"=> "filler"
														              )
														            ),
														            "flex"=> 2
														          ),
														          array(
														            "type"=> "box",
														            "layout"=> "vertical",
														            "contents"=> array(
														              array(
														                "type"=> "box",
														                "layout"=> "horizontal",
														                "contents"=> array(
														                  array(
														                    "type"=> "filler"
														                  ),
														                  array(
														                    "type"=> "box",
														                    "layout"=> "vertical",
														                    "contents"=> array(),
														                    "width"=> "2px",
														                    "backgroundColor"=> "#6486E3"
														                  ),
														                  array(
														                    "type"=> "filler"
														                  )
														                ),
														                "flex"=> 1
														              )
														            ),
														            "width"=> "12px"
														          ),
														          array(
														            "type"=> "text",
														            "text"=> "พิมาน",
														            "gravity"=> "center",
														            "flex"=> 4,
														            "size"=> "xs",
														            "color"=> "#8c8c8c"
														          )
														        ),
														        "spacing"=> "lg",
														        "height"=> "30px"
														      ),
														      array(
														        "type"=> "box",
														        "layout"=> "horizontal",
														        "contents"=> array(
														          array(
														            "type"=> "text",
														            "text"=> "11 ก.ค. 65",
														            "gravity"=> "center",
														            "size"=> "xs"
														          ),
														          array(
														            "type"=> "box",
														            "layout"=> "vertical",
														            "contents"=> array(
														              array(
														                "type"=> "filler"
														              ),
														              array(
														                "type"=> "box",
														                "layout"=> "vertical",
														                "contents"=> array(),
														                "cornerRadius"=> "30px",
														                "width"=> "12px",
														                "height"=> "12px",
														                "borderColor"=> "#008000",
														                "borderWidth"=> "2px"
														              ),
														              array(
														                "type"=> "filler"
														              )
														            ),
														            "flex"=> 0
														          ),
														          array(
														            "type"=> "text",
														            "text"=> "ดำเนินการแล้วเสร็จ",
														            "gravity"=> "center",
														            "flex"=> 2,
														            "size"=> "sm"
														          )
														        ),
														        "spacing"=> "lg",
														        "cornerRadius"=> "30px",
														        "margin"=> "xl",
														        "flex"=> 1
														      ),
														      array(
														        "type"=> "box",
														        "layout"=> "horizontal",
														        "contents"=> array(
														          array(
														            "type"=> "box",
														            "layout"=> "baseline",
														            "contents"=> array(
														              array(
														                "type"=> "filler"
														              )
														            ),
														            "flex"=> 2
														          ),
														          array(
														            "type"=> "box",
														            "layout"=> "vertical",
														            "contents"=> array(
														              array(
														                "type"=> "box",
														                "layout"=> "horizontal",
														                "contents"=> array(
														                  array(
														                    "type"=> "filler"
														                  ),
														                  array(
														                    "type"=> "box",
														                    "layout"=> "vertical",
														                    "contents"=> array(),
														                    "width"=> "2px",
														                    "backgroundColor"=> "#008000"
														                  ),
														                  array(
														                    "type"=> "filler"
														                  )
														                ),
														                "flex"=> 1
														              )
														            ),
														            "width"=> "12px"
														          ),
														          array(
														            "type"=> "text",
														            "text"=> "กนกวรรณ",
														            "gravity"=> "center",
														            "flex"=> 4,
														            "size"=> "xs",
														            "color"=> "#8c8c8c"
														          )
														        ),
														        "spacing"=> "lg",
														        "height"=> "30px"
														      )
														    )
														  )
														)
											
										
											)
										)
										*/
												
						);
						$client->replyMessage1($event['replyToken'],$a);
				}

                else{
					/*
				    $a = array(
					array(
					    'type' => 'text',
					    'text' => "---"         
					)
				    );
				    $client->replyMessage1($event['replyToken'],$a);
					*/
				}

			
    }
    //----------------------------จบเงื่อนไขข้อความtext-----------------------------------//
     //-----ถ้ามีการส่งimage------------------------------------------------------------//
    elseif ($event['type'] == 'message' && $event['message']['type'] == 'image') {

		$message = $event['message'];
		//$imagepath = 'img/';  
		$imagename = 'img_'.date('Ymdhis').'.jpg';
		$imageData = $client->getImage($event['message']['id']);
		//$save_result = file_put_contents($imagepath.$imagename, $imageData);
		
		
        $client->replyMessage1($event['replyToken'],array(
                array(
                    'type' => 'text',
                    'text' => $event['message']['type']
                ),
                array(
                    'type' => 'text',
                    'text' => $event['message']['id']
                ),      
                array(
                    'type' => 'sticker',
                    'packageId' => 3,
                    'stickerId' => 232
                )
        ));
		
    }
    //----------------------------จบเงื่อนไขimage------------------------------------//
 
     //-----ถ้ามีการส่งvideo------------------------------------------------------------//
    elseif ($event['type'] == 'message' && $event['message']['type'] == 'video') {
		/*
		  "message": {
			"id": "325708",
			"type": "video",
			"duration": 60000,
			"contentProvider": {
			  "type": "external",
			  "originalContentUrl": "https://example.com/original.mp4",
			  "previewImageUrl": "https://example.com/preview.jpg"
			}
		*/

	    $text_=implode(" ",$event['message']['contentProvider']);
	    
		$sticker=array("2,149","2,23","3,239","2,154","2,161","3,232","2,24","1,115","2,152","4,616","4,296","2,165","4,279","2,525","2,19","2,527");
		$random_keys=array_rand($sticker,1);
		$txt = $sticker[$random_keys];

		$split = explode(",", $txt);
		$p = $split[0];
		$s = $split[1];
		//echo $split[0];

		
        $client->replyMessage1($event['replyToken'],array(
                array(
                    'type' => 'text',
                    'text' => $event['message']['type']
                ),
                array(
                    'type' => 'text',
                    'text' => $text_
                ),

                array(
                    'type' => 'sticker',
                    'packageId' => $p,
                    'stickerId' => $s
                )

        ));
		
    }
    //----------------------------จบเงื่อนไขvideo------------------------------------//
 
 
    //-----ถ้ามีการส่งสติ๊กเกอร์------------------------------------------------------------//
    elseif ($event['type'] == 'message' && $event['message']['type'] == 'sticker') {

		$sticker=array("2,149","2,23","3,239","2,154","2,161","3,232","2,24","1,115","2,152","4,616","4,296","2,165","4,279","2,525","2,19","2,527","11538,51626498","11538,51626525","11537,52002771");
		$random_keys=array_rand($sticker,1);
		$txt = $sticker[$random_keys];

		$split = explode(",", $txt);
		$p = $split[0];
		$s = $split[1];
		//echo $split[0];

        $client->replyMessage1($event['replyToken'],array(
                array(
                    'type' => 'sticker',
                    'packageId' => $p,
                    'stickerId' => $s
                )
             
                /*,
                array(
                    'type' => 'sticker',
                    'packageId' => 3,
                    'stickerId' => 232
                ),
                    array(
                    'type' => 'sticker',
                    'packageId' => 3,
                    'stickerId' => 233
                )       
                */
         
        )
            );
    }
    //----------------------------จบเงื่อนไขสติ๊กเกอร์------------------------------------//
 
	
   //-----ถ้ามีการแชร์ location-------------------------------------------------------//
   elseif ($event['type'] == 'message' && $event['message']['type'] == 'location') {
        $latitude = $event['message']['latitude'];
        $longitude = $event['message']['longitude'];
        $title = $event['message']['title'];
        $address = $event['message']['address'];
 
               $client->replyMessage1($event['replyToken'],array(
 
                        array(
                                'type' => 'text',
                                'text' => 'มีการแชร์ตำแหน่ง'
                        ),
 
                        array(
                                "type"=> "location",
                                "title"=> "ตำแหน่งของท่าน",
                                "address"=> $address,
                                "latitude"=> $latitude,
                                "longitude"=> $longitude
                        )
                   )
                );
    }
    //----------------------------จบเงื่อนไขแชร์ location------------------------------------//
	


  //}//endif group
}
//----------------------------จบฟังก์ชั่น ReplyMessage----------------------------------//
 
//------listen--$client->parseEvents()----และเข้าฟังก์ชั่น replyMsg--------//
foreach ($client->parseEvents() as $event) {
    switch ($event['type']) {
        case 'message':
            $message = $event['message'];
            switch ($message['type']) {
                case 'text':
                    replyMsg($event, $client);                  
                    break;
                case 'image':
                    replyMsg($event, $client);                  
                    break;
                case 'sticker':
                    replyMsg($event, $client);                  
                    break;
                case 'location':
                    replyMsg($event, $client);                  
                    break;
                case 'video':
                    replyMsg($event, $client);                  
                    break;
                case 'audio':
                    replyMsg($event, $client);                  
                    break;
                case 'file':
                    replyMsg($event, $client);                  
                    break;

                default:
                    //error_log("Unsupporeted message type: " . $message['type']);
                    break;
            }
            break;
		case 'follow':
			replyMsg1($event, $client);                  
			break;
		case 'unfollow':
			replyMsg1($event, $client);                  
			break;
		case 'join':
			replyMsg1($event, $client);                  
			break;
		case 'leave':
			replyMsg1($event, $client);                  
			break;
		case 'memberJoined':
			replyMsg1($event, $client);                  
			break;
		case 'memberLeft':
			replyMsg1($event, $client);                  
			break;
		case 'postback':
			replyMsg1($event, $client);                  
			break;
		case 'beacon':
			replyMsg1($event, $client);                  
			break;
        default:
            error_log("Unsupporeted event type: " . $event['type']);
            break;
    }
};
//------listen--$client->parseEvents()----และเข้าฟังก์ชั่น replyMsg--------//
 

?>




