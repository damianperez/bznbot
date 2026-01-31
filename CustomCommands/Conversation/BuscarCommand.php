<?php

/**
 * This file is part of the PHP Telegram Bot example-bot package.
 * https://github.com/php-telegram-bot/example-bot/
 *
 * (c) PHP Telegram Bot Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * User "/Buscar" command
 *
 * Example of the Conversation functionality in form of a simple Buscar.
 */

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Funciones;

class BuscarCommand extends UserCommand
{
    /**
     * @var string
     */
    protected $name = 'buscar';

    /**
     * @var string
     */
    protected $description = 'Buscar artículos en el catálogo';

    /**
     * @var string
     */
    protected $usage = '/buscar';

    /**
     * @var string
     */
    protected $version = '0.4.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * @var bool
     */
    protected $private_only = true;

    /**
     * Conversation Object
     *
     * @var Conversation
     */
    protected $conversation;

    /**
     * Main command execution
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        $chat    = $message->getChat();
        $user    = $message->getFrom();
        $text    = trim($message->getText(true));
        $chat_id = $chat->getId();
        $user_id = $user->getId();

        // Preparing response
        $data = [
            'chat_id'      => $chat_id,
            // Remove any keyboard by default
            'reply_markup' => Keyboard::remove(['selective' => true]),
        ];

        if ($chat->isGroupChat() || $chat->isSuperGroup()) {
            // Force reply is applied by default so it can work with privacy on
            $data['reply_markup'] = Keyboard::forceReply(['selective' => true]);
        }

        // Conversation start
        $this->conversation = new Conversation($user_id, $chat_id, $this->getName());

        // Load any existing notes from this conversation
        $notes = &$this->conversation->notes;
        !is_array($notes) && $notes = [];

        // Load the current state of the conversation
        $state = $notes['state'] ?? 0;

        $result = Request::emptyResponse();

        // State machine
        // Every time a step is achieved the state is updated
        switch ($state) {
            case 0:
                if ($text === '') {
                    $notes['state'] = 0;
                    $this->conversation->update();

                    $data['text'] = 'Ingresar un código o algunas palabras para realizar la búsqueda  (Ejemplo: corredera 400)';

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['name'] = $text;
                

            // No break!
            case 1:
                

                $notes['busca'] = $text;
                $par = explode(" ", $text );


                $p1='';$p2='';$p3='';$p4='';
                if (isset($par[0]) && !empty($par[0]) ) $p1=$par[0];
                if (isset($par[1]) && !empty($par[1]) ) $p2=$par[1];
                if (isset($par[2]) && !empty($par[2]) ) $p3=$par[2];
                if (isset($par[3]) && !empty($par[3]) ) $p4=$par[3];
                
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

                $text = '';
            // No break!
            case 2:
                if ($text === '' || !is_numeric($text)) {
                    $notes['state'] = 2;
                    $this->conversation->update();

                    $data['text'] = 'Type your age:';
                    if ($text !== '') {
                        $data['text'] = 'Age must be a number';
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['age'] = $text;
                $text         = '';

            // No break!
            case 3:
                if ($text === '' || !in_array($text, ['M', 'F'], true)) {
                    $notes['state'] = 3;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard(['M', 'F']))
                        ->setResizeKeyboard(true)
                        ->setOneTimeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = 'Select your gender:';
                    if ($text !== '') {
                        $data['text'] = 'Choose a keyboard option to select your gender';
                    }

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['gender'] = $text;

            // No break!
            case 4:
                if ($message->getLocation() === null) {
                    $notes['state'] = 4;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard(
                        (new KeyboardButton('Share Location'))->setRequestLocation(true)
                    ))
                        ->setOneTimeKeyboard(true)
                        ->setResizeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = 'Share your location:';

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['longitude'] = $message->getLocation()->getLongitude();
                $notes['latitude']  = $message->getLocation()->getLatitude();

            // No break!
            case 5:
                if ($message->getPhoto() === null) {
                    $notes['state'] = 5;
                    $this->conversation->update();

                    $data['text'] = 'Insert your picture:';

                    $result = Request::sendMessage($data);
                    break;
                }

                $photo             = $message->getPhoto()[0];
                $notes['photo_id'] = $photo->getFileId();

            // No break!
            case 6:
                if ($message->getContact() === null) {
                    $notes['state'] = 6;
                    $this->conversation->update();

                    $data['reply_markup'] = (new Keyboard(
                        (new KeyboardButton('Share Contact'))->setRequestContact(true)
                    ))
                        ->setOneTimeKeyboard(true)
                        ->setResizeKeyboard(true)
                        ->setSelective(true);

                    $data['text'] = 'Share your contact information:';

                    $result = Request::sendMessage($data);
                    break;
                }

                $notes['phone_number'] = $message->getContact()->getPhoneNumber();

            // No break!
            case 7:
                $this->conversation->update();
                $out_text = '/Buscar result:' . PHP_EOL;
                unset($notes['state']);
                foreach ($notes as $k => $v) {
                    $out_text .= PHP_EOL . ucfirst($k) . ': ' . $v;
                }

                $data['photo']   = $notes['photo_id'];
                $data['caption'] = $out_text;

                $this->conversation->stop();

                $result = Request::sendPhoto($data);
                break;
        }

        return $result;
    }
}
