<?php
namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\Command;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Menu;
use Longman\TelegramBot\Funciones;
use Longman\TelegramBot\Request;

use Longman\TelegramBot\Entities\InputMedia\InputMediaPhoto;


class ArtCommand extends UserCommand
{
    protected $name = 'Art';
    protected $description = 'Art command';
    protected $usage = '/Art';
    protected $version = '1.2.0';
    protected $private_only = true;
    public function valores($LISTA,$m1,$m2,$m3,$item,$medidas)    
    {
        $PRECIO= $item->Precio_final_mayorista;
                if ($LISTA == 'A') $PRECIO= $item->Precio_final;
                if ($LISTA == 'C') $PRECIO= $item->Precio_reventa;

        $medida=$medidas[$m1][$m2][$m3];
        $valores = " P$LISTA <b>$".round( $medida *  $PRECIO,2 ) .'</b>';                    ;
        return $m1."“ x ".$m2."“ x  $m3 pies ".$valores;

    }
    public function valores2($m1,$m2,$item,$medidas)    
    {

        for ($p = 8; $p <= 16; $p++) 
            {
            $medida=$medidas[$m1][$m2][$p];

            
            }
        $medida=$medidas[$m1][$m2][$m3];
        $valores = " PF <b>$".round( $medida *  $item->Precio_final,2 ) .'</b>'.
                    " / M:$".round( $medida *  $item->Precio_final_mayorista,2 ) .
                    " / C:$".round( $medida *  $item->Precio_reventa ,2) ;
        return "$m1'' x $m2'' x  $m3 pies ".$valores;

    }
    public function execute(): ServerResponse
    {
        if ($this->getCallbackQuery() !== null) {
                $update         = $this->getUpdate();
                $callback_query = $update->getCallbackQuery();
                $callback_data  = $callback_query->getData();
                $callback_query_id = $callback_query->getId();
                //$message =  $update->getMessage();
                $message  = $callback_query->getMessage();
                $chat    =  $callback_query->getMessage()->getChat();
                $user    = $chat;
                $chat_id =  $callback_query->getMessage()->getChat()->getId();
                $user_id = $chat_id;
                $data['callback_query_id'] = $callback_query_id;
                $data['message_id']  = $callback_query->getMessage()->getMessageId();
                $data['chat_id']  = $callback_query->getMessage()->getChat()->getId();
                $data['message_id' ]  = $callback_query->getMessage()->getMessageId();
        }
        else
        {
                $message = $this->getMessage();
                $chat    = $message->getChat();
                $user    = $message->getFrom();
                $grupo = $message->getChat()->getTitle();

                $chat_id = $chat->getId();
                $user_id = $user->getId();
                
                $data['user_id']  = $message->getFrom()->getId();
                $data['chat_id']  = $message->getFrom()->getId();
        }
        $F=new Funciones;       
        
        if ( ! $F->Puede_usar_el_bot( $user_id, $this->telegram->getAdminList() ) )        
            return $this->replyToChat( 'Ingreso restringido'    , ['parse_mode'=>'HTML']    );
        
        

        //isAdmin($user_id = null): bool 
        $command = $message->getCommand();
        $text    = trim($message->getText(true));
       // $quien = $user->getFirstName().' @'.$user->getUsername(); 

        
        $quien='yo';
        $URL_BASE ="https://perezcompany.com.ar/back/api/";        
        $URL_BASE = 'http://'.$_SERVER['SERVER_NAME'].'/back/api/' ;
        $URL_BASE = 'https://www.arignon.com.ar/back/api/buscart/' ;
        $p1='';$p2='';$p3='';$p4='';  ;        
        $deep_linking_parameter = $message->getText(true);

        if ( $command == 'corre')  $deep_linking_parameter='corre '.$deep_linking_parameter;
        if ( $command == 'canto')  $deep_linking_parameter='canto '.$deep_linking_parameter;

        
        if ( $command == 'mdf')  $deep_linking_parameter='mdf '.$deep_linking_parameter;
        if ( $command == 'aglo')  $deep_linking_parameter='aglo '.$deep_linking_parameter;
        if ( $command == 'placa')  $deep_linking_parameter='placa '.$deep_linking_parameter;
        
        $par = explode(" ", $deep_linking_parameter );
        
        $data = [
            'text' => '',
            'chat_id'      => $chat_id,
			'parse_mode' => 'HTML',                     
            'disable_web_page_preview'=>false,
            ];	

        if (isset($par[0]) && !empty($par[0]) ) $p1=$par[0];
        if (isset($par[1]) && !empty($par[1]) ) $p2=$par[1];
        if (isset($par[2]) && !empty($par[2]) ) $p3=$par[2];
        if (isset($par[3]) && !empty($par[3]) ) $p4=$par[3];

         
        //$basico = $F->Basicos($deep_linking_parameter);
        //if ($basico) $p1=$basico;    

        $texto = 'Sin resultados';

        $LISTA='B';
        if (strtoupper($p1)=='B') 
        {
            $LISTA='B';
            $p1 = $p2;
            $p2 = $p3;
        }
        elseif (strtoupper($p1)=='A') 
        {
            $LISTA='A';
            $p1 = $p2;
            $p2 = $p3;
        }
        elseif (strtoupper($p1)=='C') 
        {
            $LISTA='C';
            $p1 = $p2;
            $p2 = $p3;
        }

        if (strlen($command) > 3 && substr($command,0,3)=='art')
           $p1=substr($command,3);
           
        if ( $p1=='' && $p2 == '' )
            {
                $data['text']= "Ejemplos de Uso:".PHP_EOL.$deep_linking_parameter.PHP_EOL.
                                "com $command" .PHP_EOL.
                                "text $text" .PHP_EOL.
                                "/art a GW1 ".PHP_EOL." /art C bisagra 450".PHP_EOL." /art 1520";
                return Request::sendMessage($data);
            }       

        $F=new Funciones;        
        $listado= $F->Art($p1,$p2,$p3,$p4);    
        if ( ( !$listado ) ||  $listado->result <> "OK") 
            return $this->replyToChat( 'No hay resultados'    , ['parse_mode'=>'HTML']    );
        $van=0;
        $texto = '<span class="tg-spoiler">'."<b> $deep_linking_parameter</b>".'</span>'.PHP_EOL;
        $extra= '';
        $MARCA='';
        $texto_copiar = '';
        foreach ( $listado->records as $item )
            {
            if (intval($item->Stock_minimo) < 1 ) continue;
            $van +=1;
            $MARCA = $item->Marca ?: NULL;
            $PRECIO= $item->Precio_final_mayorista;

            if ( strtoupper($item->Marca) =='EGGER')  $item->Marca='Ⓔgger';
            if ( strtoupper($item->Marca) =='FAPLAC')  $item->Marca='Ⓕaplac';
            if ( str_contains(strtoupper($item->Observaciones),'DOLAR'))  $item->Observaciones .='💵';
                /*
                //if ( empty($item->Stock_actual) ) continue; 
                // $item->Stock_actual='---';
                //🟩🟨🟧🟠🔴 ❗💲❎❌ ❌✅
                //$item->Stock_actual = '<b>'.$item->Stock_actual.'</b>';                
                //$item->Detalle = str_replace('+','',$item->Detalle);
                */
                $extra= '';
                /*
                if ($item->GRUPO == 'SALI' || $item->ID == 200 )
                {
                    $extra=PHP_EOL;
                    $pie=0.3048;$coef=0.2734;
                    $ancho=1; $largo=1; $pies=1;                
                    for ($a = 1; $a <= 3; $a++) 
                        for ($l = 3; $l <= 6 ; $l++) 
                            for ($p = 8; $p <= 13; $p++) 
                                {
                                $v = $a*$l*$p*$pie * $coef ;
                                $medidas[$a][$l][$p]= $v ;
                                }
                        
                    
                    $extra.= $this->valores($LISTA,1,1,10,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,3,8,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,3,9,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,3,10,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,3,11,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,3,12,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,3,13,$item,$medidas).PHP_EOL;

                    $extra.= $this->valores($LISTA,1,4,8,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,4,9,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,4,10,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,4,11,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,4,12,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,4,13,$item,$medidas).PHP_EOL;

                    $extra.= $this->valores($LISTA,1,5,8,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,5,9,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,5,10,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,5,11,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,5,12,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,5,13,$item,$medidas).PHP_EOL;

                    $extra.= $this->valores($LISTA,1,6,8,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,6,9,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,6,10,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,6,11,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,6,12,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,1,6,13,$item,$medidas).PHP_EOL;
                    $extra.= PHP_EOL;
                    $extra.= $this->valores($LISTA,2,4,8,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,2,4,9,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,2,4,10,$item,$medidas).PHP_EOL;
                    $extra.= PHP_EOL;
                    $extra.= $this->valores($LISTA,3,3,8,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,3,3,9,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,3,3,10,$item,$medidas).PHP_EOL;
                    $extra.= PHP_EOL;
                    $extra.= $this->valores($LISTA,2,6,8,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,2,6,9,$item,$medidas).PHP_EOL;
                    $extra.= $this->valores($LISTA,2,6,10,$item,$medidas).PHP_EOL;
                }
                */            
            if ($LISTA == 'A') $PRECIO= $item->Precio_final;
            if ($LISTA == 'C') $PRECIO= $item->Precio_reventa;

            if ( $van > 20 )
            {
                //$T='<span class="tg-spoiler"></span> sjkadfdfs ';
                //$this->replyToChat( $T    , ['parse_mode'=>'HTML']    );
                //$this->replyToChat( $texto_copiar    , ['parse_mode'=>'HTML']    );
                $this->replyToChat( $texto    , ['parse_mode'=>'HTML']    );
                if ( count($listado->records) > 20 ) exit;
                $texto=''; $van=0;
            }
           
            $ABC ="$item->Precio_costo/".intval($item->A)."/".intval($item->B)."/".intval($item->C);
            if ( $item->Dolar > 0 ) $ABC .= "USD$item->Dolar ($item->ctz)";
            $PABC ="$item->Precio_final/$item->Precio_final_mayorista/$item->Precio_reventa";
            $MOSTRAR='';
            $hay=false;
            $mostrar=false;
            if (intval($item->Stock_actual) > 0 ) $hay=true;
            if (intval($item->Stock_minimo) > 0 ) $mostrar=true;
            $date = new \DateTime($item->date_modified);   


            $now = new \DateTime();

            if ($PRECIO < 4 )    CONTINUE    ;
            //if ($mostrar) $MOSTRAR='Mostrar en listados:<b>SI</b>'.PHP_EOL;

            if ( $item->Maestro   )
                $MOSTRAR.=   "Maestro <b>". str_pad(intval($item->Precio_maestro), 6, '0', STR_PAD_LEFT) . '</b>'.
                        str_pad(number_format($item->Coef_maestro, 2, '.', '')  , 6, '0', STR_PAD_LEFT).
                        "<b>".str_pad(intval($item->Precio_maestro*$item->Coef_maestro) , 6, '0', STR_PAD_LEFT). '</b>'.
                       " /maestro$item->Maestro";
            $CODIGOS= "/art$item->ID-$item->Detalle $item->Marca".PHP_EOL.                
                        "$item->GRUPO/$item->ARTIC <b>$item->Presentacion</b>";
            
            if ($item->url)  { $CODIGOS .= " 📷";}
            if (str_contains(strtoupper($CODIGOS), 'PVC')) { $CODIGOS .= " 🟠PVC";}
            if (str_contains(strtoupper($CODIGOS), ' MEL ')) { $CODIGOS .= " ☵";}
            if (str_contains(strtoupper($CODIGOS), 'COLA')) { $CODIGOS .= " ㏄";} 
            if (str_contains(strtoupper($CODIGOS), 'DOLAR')) { $CODIGOS .= " 🥬";} 
            $CODIGOS= str_replace('mm ','㎜ ',$CODIGOS);
            $CODIGOS= str_replace('MM ','㎜ ',$CODIGOS);
            $CODIGOS= str_replace('PREENCOLADO','㏄',$CODIGOS);
            $item->Espesor = str_replace('mm','㎜',$item->Espesor);
            $item->Espesor = str_replace('MM','㎜',$item->Espesor);

            $PROV=$item->Proveedor;
            
            $PRECIOS="P$LISTA: <b><u>$".$PRECIO."</u></b>" ;
            if ( $item->ListaPrecio <> null )
                 $PROV.=" <i> $item->ListaPrecio</i>";
            else
                 $PROV.=" Ult:".$date->format('d/m'). ' '.$item->Observaciones;

            if ( $hay and $PRECIO > 4 AND  $item->Stock_minimo > 0 )  $PRECIOS='   🟩'.$PRECIOS;
            $STOCK = '';
            

            if (!empty($item->Stock_actual))
                $STOCK = 'Stock:<b>'.$item->Stock_actual.'</b> '.$date->diff($now)->format("Hace %a dias")  ;

            $ultimos='';
            if ( $listado->RecordCount > 1 )
                {                                        
                    
                    $texto.=$CODIGOS.PHP_EOL;
                    $texto.=$PROV.PHP_EOL;                    
                    $texto.='  '.$PRECIOS.' '.$STOCK.PHP_EOL;
                    //$texto_copiar .= '<pre><code class="language-Art'.$item->ID. '">'.
                    
                    $texto_copiar .= "▪️$item->ID $item->Detalle <b>$".$PRECIO.'</b>'.PHP_EOL; 
                }
                else
                {
                    $texto_copiar = '<code class="language-art">'."$item->Detalle ".
                    "  $item->Unidad  $item->Presentacion $PRECIOS"."</code>";
                    $texto= "$item->ID - $item->GRUPO/$item->ARTIC".PHP_EOL.                               
                            $texto_copiar. PHP_EOL.$item->Unidad .
                            "  $item->Unidad  $item->Presentacion".PHP_EOL.                                
                            "P".$item->ID_PROVEEDOR.' '.$item->Proveedor." ".$date->format('d/m')." <i>$item->ListaPrecio</i>".PHP_EOL.        
                            $ABC."/$item->Coef".PHP_EOL.    
                            $PABC.PHP_EOL.      
                            $PRECIOS.PHP_EOL.     
                            $STOCK.PHP_EOL.
                            $item->Observaciones. PHP_EOL.
                            $MOSTRAR.PHP_EOL.
                            "Cambiar stock 👉/stock$item->ID ". PHP_EOL.
                            "Cambiar costo 👉/costo$item->ID".
                            "Sacar foto 👉/fotoart$item->ID".
                            " 👉/art$item->ID ". PHP_EOL;                           
                            if ($item->Color)
                            {
                             $item->Color = str_replace(" ", "_", $item->Color);
                             $texto.= "/color$item->Color $item->Marca   $item->Espesor ".PHP_EOL;                                                             
                            }
                            $UV= $F->movsXart($item->ID);                               
                            $van=0;
                            //Funciones::debug_a_admins('UV'.$item->ID,$UV);                            
                            if ( $UV->totalRecordCount )
                            {
                                $ultimos='<u>Ultimos vendidos</u>'. PHP_EOL;
                                foreach ( $UV->records as $M )
                                {
                                    $van +=1;
                                    $date = new \DateTime($M->date_created);                                       
                                    $ultimos.="/pedido$M->ID_MOVIMIENTO ".$date->format('d/m')." $ $M->Precio_unitario $M->Nombre ".PHP_EOL; 
                                    if ($van > 6 ) break;
                                }
                             }
                             $texto.=$ultimos.$extra;   
                             //$texto = '<pre><code class="language-art">'.$texto.'</code></pre>';
                             if ($item->url == null  )
                                return $this->replyToChat( $texto    , ['parse_mode'=>'HTML']    );
                            else
                            {
                                $data['caption'] = $texto;
                                $data['photo']   = Request::encodeFile($item->url);	
                                return Request::sendPhoto($data);
                            }
                }    
            }
            $texto_copiar = '<code class="language-art">'.$texto_copiar.'</code>';
        $this->replyToChat( $texto_copiar    , ['parse_mode'=>'HTML']    );
        return $this->replyToChat( $texto    , ['parse_mode'=>'HTML']    );
        $MENU = new Menu;        
        $MENU->armar_menu('PRIN',1,7);
        $MENU->actual_item = 1;        
        $data =array_merge($data, $MENU->getData());        
        return Request::sendPhoto($data);
    }
}