<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Tools\Ajax;
use DB;

class CommentController extends Controller
{


	function syncCallback(Request $request)
	{
		$key = '2093c10c3fe2847c8d4e178b8f748a51';
		$last_log_id = 0;

		if ($this->check_signature($request->input(), $key) == false) {
			return false;
		}
		$limit = 20;

		$params = array(
			'limit' => $limit,
			'order' => 'asc',
		);


		if (!$last_log_id)
			$last_log_id = 0;

		$params['since_id'] = $last_log_id;
		//自己找一个php的 http 库
		$http_client = new Client();
		$response = $http_client->request('GET', 'http://api.duoshuo.com/log/list.json', $params);

		if (!isset($response['response'])) {
			//处理错误,错误消息$response['message'], $response['code']
			//...

		} else {
			//遍历返回的response，你可以根据action决定对这条评论的处理方式。
			foreach ($response['response'] as $log) {

				dd($log);
				switch ($log['action']) {
					case 'create':
						//这条评论是刚创建的
						break;
					case 'approve':
						//这条评论是通过的评论
						break;
					case 'spam':
						//这条评论是标记垃圾的评论
						break;
					case 'delete':
						//这条评论是删除的评论
						break;
					case 'delete-forever':
						//彻底删除的评论
						break;
					default:
						break;
				}

				//更新last_log_id，记得维护last_log_id。（如update你的数据库）
				if (strlen($log['log_id']) > strlen($last_log_id) || strcmp($log['log_id'], $last_log_id) > 0) {
					$last_log_id = $log['log_id'];
				}

			}

		}

	}


	/**
	 *
	 * check signature
	 *
	 */
	protected function check_signature($input, $secret)
	{
		$signature = $input['signature'];
		unset($input['signature']);
		ksort($input);
		$baseString = http_build_query($input, null, '&');
		$expectSignature = base64_encode(hash_hmac('sha1', $baseString, $secret, true));
		if ($signature !== $expectSignature) {
			return false;
		}
		return true;
	}
}
