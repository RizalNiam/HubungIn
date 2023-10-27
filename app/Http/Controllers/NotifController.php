<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;



class NotifController extends Controller
{
    function send_notif (Request $request) {
  
        $id = $request['user_id'];
   
        // get a user to get the fcm_token that already sent from mobile apps 
        $user = User::findOrFail($id);

        $user = User::where('id', $id)->first();

        $deviceToken = $user->device_token;

          $data = [
               'title' => $request->input('title'),
               'body' => $request->input('body'),
          ];
         
      $headers = [
       'Authorization: key=AAAARvgGbJU:APA91bF86WdF2Dl23jLN_pZSSffrz1gogwAP5aM1t-6gprrUyU_H6jeTWVo3KR4o-i39JZYvnrVvEcsO-5s8F2dVG5wU12WBLtFr8W4BVpwk4cv_yDW3ztjPb2Y1U4f_v2JHk4TexC59',
       'Content-Type: application/json',
   ];
   
      $options = [
          CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
          CURLOPT_POST => true,
          CURLOPT_HTTPHEADER => $headers,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_POSTFIELDS => json_encode([
              'to' => $deviceToken,
              'data' => $data,
          ]),
      ];
   
      $ch = curl_init();
      curl_setopt_array($ch, $options);
   
      $response = curl_exec($ch);
   
      curl_close($ch);
      if ($response === false) {
          die('cURL error: ' . curl_error($ch));
      }
   
      $responseData = json_decode($response, true);
   
      if (isset($responseData['error'])) {
          die('FCM API error: ' . $responseData['error']);
      }
      return $this->requestSuccessData('Successfully sent', $responseData );     
        }
}
