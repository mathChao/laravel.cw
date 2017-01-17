<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Tools\Ajax;
use Illuminate\Http\Request;
use App\Services\SyncDuoShuoComments;

class CommentController extends Controller
{

	function syncCallback(Request $request)
	{
		$handler  = new SyncDuoShuoComments();

		if ($this->check_signature($request->input(), $handler->app_key) == false) {
			return false;
		}

		$result = $handler->syncComment(20);

		if($result === true){
			return Ajax::success(['message' => 'success']);
		}else{
			return Ajax::serverError($result);
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
