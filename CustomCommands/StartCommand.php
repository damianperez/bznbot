<?php
namespace Longman\TelegramBot\Commands\UserCommands;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entities\UserProfilePhotos;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
class StartCommand extends UserCommand
{
    protected $name = 'Start';
    protected $description = 'Arranca el bot, muestra el QR';
    protected $usage = '/Start';
    protected $version = '1.2.0';
    protected $private_only = false;
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        $from       = $message->getFrom();
        $user_id    = $from->getId();
        $chat_id    = $message->getChat()->getId();
        $message_id = $message->getMessageId();

        $data = [
            'chat_id'             => $chat_id,
 //           'reply_to_message_id' => $message_id,
            'parse_mode' => 'HTML',
        ];

        // Send chat action "typing..."
        Request::sendChatAction([
            'chat_id' => $chat_id,
            'action'  => ChatAction::TYPING,
        ]);

        $caption = sprintf(
            'Your Id: %d' . PHP_EOL .
            'Name: %s %s' . PHP_EOL .
            'Username: %s',
            $user_id,
            $from->getFirstName(),
            $from->getLastName(),
            $from->getUsername()
        );
        $caption = "Bienvenido a BRONZEN ".trim($from->getFirstName().' '.$from->getLastName())." ($user_id ".  $from->getUsername().")";

        // Fetch the most recent user profile photo
        $limit  = 1;
        $offset = null;

        $user_profile_photos_response = Request::getUserProfilePhotos([
            'user_id' => $user_id,
            'limit'   => $limit,
            'offset'  => $offset,
        ]);


        
        $buchon = array(   'chat_id' => 662767623,
        'text' => $caption,
        'parse_mode' => 'HTML' );
        $bot_api_key  = "676438755:AAG3QBJ5owYiwMjV2wiluXIJB5DGxFyjKbY";
		$bot_username = '@Buchonbot';
        $buchon['text']=$caption;        
        $response = file_get_contents("https://api.telegram.org/bot$bot_api_key/sendMessage?" . http_build_query($buchon) );
        if ($user_profile_photos_response->isOk()) {
            /** @var UserProfilePhotos $user_profile_photos */
            $user_profile_photos = $user_profile_photos_response->getResult();

            if ($user_profile_photos->getTotalCount() > 0) {
                $photos = $user_profile_photos->getPhotos();
                // Get the best quality of the profile photo
                $photo   = end($photos[0]);
                $file_id = $photo->getFileId();
                $data['photo']   = $file_id;
                $data['caption'] = $caption;
                Request::sendPhoto($data);
            }
        }
        else
        {
            // No Photo just send text
            $data['text'] = $caption;
           Request::sendMessage($data);
        }         
        
        $texto1="<b>¡Bienvenidos a Bronzen ".trim($from->getFirstName().' '.$from->getLastName())." !</b>".PHP_EOL.
"SOMOS BRONZEN Están, estás, estamos.";

$texto2 = "Los herrajes están presentes en todo.
En cada puerta, en cada ventana, en cada mueble.
Están en cocinas, livings y baños, en forma de aluminio, madera o vidrio.
Siempre facilitando el movimiento y aportando diseño.

En BRONZEN lo sabemos y por eso estamos con vos.
En todas las soluciones, en todos los espacios, en todos los materiales.

Comprometidos en ofrecerte todo para garantizarte el mejor servicio,
con stock permanente, entrega inmediata en todo el país
y el precio más conveniente, siempre.
";
$texto3 = "<b>Porque sabemos que para estar en todo lo que necesitás
tenemos que estar en todo</b>";

        $data['caption'] = $texto1;
        $data['photo']   = Request::encodeFile($this->telegram->getDownloadPath() . '/logo.png');	
        Request::sendPhoto($data);        
        $data['caption'] = $texto2;        
        $data['photo']   = Request::encodeFile($this->telegram->getDownloadPath() . '/somos.png');
        Request::sendPhoto($data);     

        $data['caption'] = 'Podés compartir el bot mediante este QR o este link'.PHP_EOL.
                          'https://t.me/BronzenBot';
        $data['photo']   = Request::encodeFile($this->telegram->getDownloadPath() . '/qrbot.jpg');	        
        Request::sendPhoto($data);     

        $data['text'] = $texto3;

        Request::sendMessage($data);
        // Do nothing
        return Request::emptyResponse();
/*
norden - Estado actual del Pilote Norden
foto   - Snapshot de la WebCam al río
start  - Mostrar el QR para compartir
help   - Ayuda
*/

    }
}