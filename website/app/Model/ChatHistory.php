<?php
    App::uses('AppModel', 'Model');
    /**
     * ChatHistory Model
     *
     * @property ChatHistory $ChatHistory
     */
    class ChatHistory extends AppModel {

        public function saveHistoric($idChat){
            if(empty($idChat) || !is_numeric($idChat))
                return false;

            //On récupère les messages du chat
            App::import('Model', 'ChatMessage');
            $chatMessage = new ChatMessage();
            $messages = $chatMessage->find('all', array(
                'fields' => array('ChatMessage.chat_id','ChatMessage.user_id', 'ChatMessage.content', 'ChatMessage.date_add'),
                'conditions' => array('ChatMessage.chat_id' => $idChat),
                'recursive' => -1
            ));

            //pour chaque message
            $saveData = array();
            foreach($messages as $message){
                //Necessaire sinon le beforeSave effacer la date des messages
                $message['ChatMessage']['date'] = $message['ChatMessage']['date_add'];
                unset($message['ChatMessage']['date_add']);
                $saveData[] = $message['ChatMessage'];
            }

            //Save les messages
            $this->saveMany($saveData);
        }
    }
