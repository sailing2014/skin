<?php
namespace Helper;

use Httpful\Request;
use Httpful\Exception\ConnectionErrorException;
use App\Exception\GatewayException;

/**
 * Some Util
 *
 * @author changyu.wang
 *        
 */
class Util
{

    /**
     * 发送http请求 default post
     *
     * @param string $url            
     * @param array $data
     *            NOTES: array values don't json_encode!!!!
     * @param string $verb
     *            [optional]
     * @param array $headers
     *            custom headers
     * @param int $timeout 
     *            timeout
     * @return array contain `status`(int http status) and `body`(string body).
     */
    public static function httpSender($url, $data = array(), $verb = 'POST', $headers = array(), $format = 'json',$timeout=3)
    {
        $response = null;
        
        try {
            
            switch (strtoupper($verb)) {
                case 'GET':
                    $response = Request::get($url, $format)->addHeaders($headers)
                        ->timeout($timeout)
                        ->send();
                    break;
                
                case 'PUT':
                    $response = Request::put($url, json_encode($data), $format)->addHeaders($headers)
                        ->timeout($timeout)
                        ->send();
                    break;
                
                case 'DELETE':
                    $response = Request::delete($url, $format)->addHeaders($headers)
                        ->timeout($timeout)
                        ->send();
                    break;
                
                default:
                    $response = Request::post($url, json_encode($data), $format)->addHeaders($headers)
                        ->timeout($timeout)
                        ->send();
                    break;
            }
            
            return array(
                'httpStatus' => $response->code,
                'body' => $response->raw_body
            );
            
        } catch (ConnectionErrorException $e) {
            throw new GatewayException($e); 
        }catch (GatewayException $e) {
            throw new GatewayException($e); 
        }catch (Exception $e) {
            throw new GatewayException($e); 
        } 

    }
}

