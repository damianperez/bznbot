<?php
namespace Longman\TelegramBot;
use Longman\TelegramBot\DB;
use Longman\TelegramBot\Entities\InlineKeyboard;
use PDO;
error_reporting(E_ALL);
$config = require __DIR__ . '/config.php';
class Funciones {
	public static function usuario($id)
	{
	$u[662767623]= 'Damián';
	$u[5634398051]='Marcos';
	$u[928836801]=  'Pablo';
	$u[1103479769]=  'Cecilia                    ';
	$u[5463687641]=   'Bot';
	$u[6839284307]= 'Dani';
	$u[5624988914]= 'Pipo';
	$u[7382245233]=  'MArtin';
	$u[6742865545]=  'Valentin';
	$u[7552023525]=   'Celu galpon';
	$u[8198796839]=   'Valen Arignon';
	if (isset($u[$id])) return $u[$id];
	return "nn $id";
	}
	
	function UltimosMovs($id=null,$cli=null)
	{	
		if ( $cli==null || $cli==0 ) return ([]);		
		
		$URL_BASE = 'https://'.$_SERVER['SERVER_NAME'].'/back/api' ;		
		$URL_BASE.="/movimientos?id_cliente=$cli";
		
		//Funciones::debug_a_admins('Cli',$URL_BASE);
		$json = Funciones::SendGet($URL_BASE);	
		return json_decode($json);
		
	}
	function Dolar($tipo='')
	{				
		$URL_BASE = 'https://dolarapi.com/v1/dolares/oficial?' . time();
		if ($tipo=='blue')
			$URL_BASE = 'https://dolarapi.com/v1/dolares/blue?' . time();
		//Funciones::debug_a_admins('Cli',$URL_BASE);
		$json = Funciones::SendGet($URL_BASE);	
		return json_decode($json);
		
	}
	function  Puede_usar_el_bot($user_id, $lista)
	{			
		return in_array($user_id, $lista, true);
	}
	
	function Art($p1=null,$p2=null,$p3=null,$p4=null)
	{	
		$params=array();		
		//$basico = Funciones::Basicos("$p1 $p2 $p3 $p4");
        //if ($basico) $p1=$basico;   


		if ($p1 <> null && $p1 <> 'A' && $p1 <> 'C' ) $p1= "/$p1";
		if ($p2 <> null ) $p2= "/$p2";
		if ($p3 <> null ) $p3= "/$p3";
		if ($p4 <> null ) $p4= "/$p4";     
        $URL_BASE = 'https://'.$_SERVER['SERVER_NAME'].'/back/api' ;
		//$URL_BASE = 'https://www.arignon.com.ar/back/api/buscart' ;
		$URL_BASE.='/buscart';
		$json = Funciones::SendGet($URL_BASE.$p1.$p2.$p3.$p4);	
		//Funciones::debug_a_admins('Art fn',$URL_BASE.$p1.$p2);//
		return json_decode($json);
	}

	
	public static function debug_a_admins(   $quien, $msg )
    {
		$bot_api_key  = "676438755:AAG3QBJ5owYiwMjV2wiluXIJB5DGxFyjKbY";
		$bot_username = '@Buchonbot';
		$chatIds = array("662767623"); // Los destinatarios 
    
    	foreach ($chatIds as $chatId) {
        $data = array(   'chat_id' => $chatId,
        'text' => 'Debug '.$quien. '  '.var_export($msg,true) ,
        'parse_mode' => 'HTML' );
         $response = file_get_contents("https://api.telegram.org/bot$bot_api_key/sendMessage?" . http_build_query($data) );
    	}
    	return ; 
    }
	public static function msj_a_admins(   $quien, $msg )
    {
		$bot_api_key  = "676438755:AAG3QBJ5owYiwMjV2wiluXIJB5DGxFyjKbY";
		$bot_username = '@Buchonbot';
		$chatIds = array("662767623"); // Los destinatarios     
    	foreach ($chatIds as $chatId) {
        $data = array(   'chat_id' => $chatId,
        'text' => 'MSJ: '.$quien. '  '.$msg ,
        'parse_mode' => 'HTML' );
         $response = file_get_contents("https://api.telegram.org/bot$bot_api_key/sendMessage?" . http_build_query($data) );
    	}
    	return ; 
    }
	public static Function SendPostFile($target_url,$params)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $target_url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)");   
		curl_setopt($ch, CURLOPT_HTTPHEADER,array('Content-Type: multipart/form-data'));
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);   
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);  
		curl_setopt($ch, CURLOPT_TIMEOUT, 1000);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION ,true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	
		$result = curl_exec ($ch);
	
		if ($result === FALSE) 
			echo "Error sending   " . curl_error($ch);
	
		curl_close ($ch);
		return $result;
		return json_decode($result);
	}
	
function SendPost($url,$params )
{
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);
return $result;
}
public function SendGet($url )
    {
        $ch = curl_init($url);
   //     curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Cache-Control: no-cache, no-store, must-revalidate",
    "Pragma: no-cache",
    "Expires: 0"
));
		curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 0); // Disables DNS caching
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);        
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

public function comi( $str )
{
	return "'".$str."' ,";
}

}
?>
