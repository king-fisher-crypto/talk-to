<?php
    App::uses('AppController', 'Controller');
    App::uses('CakeTime', 'Utility');

    class BlockController extends AppController {
        public $components = array('Paginator');
        public $uses = array('User', 'Domain', 'Block');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'block')));

        public function beforeFilter() {

            parent::beforeFilter();
        }

        public function admin_create(){

            //Les domains pour les checkbox
            $domain_select = $this->Domain->find('list', array(
                'fields' => array('domain')
            ));

            //Nombre de domaines
            $count = count($domain_select);
            //La moitié
            $half = floor($count/2);

            $this->set(compact('domain_select', 'count', 'half'));

            if($this->request->is('post')){

                $requestData = $this->validForm('create');

                //Si return false, alors retour sur le formulaire
                if($requestData === false)
                    return;
                elseif(is_array($requestData)){
                    //Si on a un msg pour l'utilisateur, dans cette action on s'en fiche du msg
                    if(isset($requestData[0])){
                        $requestData = $requestData[1];
                    }
                }
                //Si return false, alors retour sur le formulaire
                if($requestData === false)
                    return;

                //On save les donnée
                $this->Block->create();
                if($this->Block->save($requestData['Block'])){
                    $this->Session->setFlash(__('Le block a été crée. N\'oubliez pas de configurer les langues.'), 'flash_success');
                    $this->redirect(array('controller' => 'block', 'action' => 'edit', 'admin' => true, 'id' => $this->Block->id), false);
                }
                else{
                    $this->Session->setFlash(__('Erreur lors de la sauvegarde'), 'flash_warning');
                    $this->redirect(array('controller' => 'block', 'action' => 'create', 'admin' => true),false);
                }

                //Redirection edition
                $this->redirect(array('controller' => 'block', 'action' => 'list', 'admin' => true), false);
            }
        }

        public function admin_list(){
            //Les slides
            $this->Paginator->settings = array(
				'fields' => array('Block.*', 'BlockLang.*','Lang.name'),
                'order' => array('Block.id' => 'desc'),
                'paramType' => 'querystring',
				 'joins' => array(
                    array(
                        'table' => 'block_langs',
                        'alias' => 'BlockLang',
                        'type'  => 'left',
                        'conditions' => array('BlockLang.block_id = Block.id', 'BlockLang.lang_id = 1',)
                    ),
                    array(
                        'table' => 'langs',
                        'alias' => 'Lang',
                        'type'  => 'left',
                        'conditions' => array('Lang.id_lang = BlockLang.lang_id')
                    )
                ),
                'limit' => 25
            );
			

            $block = $this->Paginator->paginate($this->Block);

            $this->set(compact('block'));
        }

        public function admin_edit($id){
            if($this->request->is('post')){
                $requestData = $this->validForm('edit', $id);

                //En cas d'erreur
                if($requestData === false)
                    $this->redirect(array('controller' => 'block', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
                elseif(is_array($requestData)){
                    //Si on a un msg pour l'utilisateur
                    if(isset($requestData[0])){
                        $msg = $requestData[0];
                        $requestData = $requestData[1];
                    }
                }

                $updateData = $requestData['Block'];
                $updateData = $this->Block->value($updateData);
                if($this->Block->updateAll($updateData, array('Block.id' => $requestData['Block']['id']))){
                    //Chaque langue
                    $this->loadModel('BlockLang');
                    foreach($requestData['BlockLang'] as $lang){
                        if($this->BlockLang->lang_exist($lang['block_id'], $lang['lang_id'])){
                            //Update
                            $updateData = array();
                            $updateData['title'] = $lang['title'];
                            $updateData['link'] = $lang['link'];
							$updateData['text1'] = $lang['text1'];
							$updateData['text2_1'] = $lang['text2_1'];
							$updateData['text2_2'] = $lang['text2_2'];
							$updateData['text2_3'] = $lang['text2_3'];
							$updateData['text3']  = $lang['text3'];
								
                            $updateData = $this->BlockLang->value($updateData);
                            $this->BlockLang->updateAll($updateData, array('BlockLang.block_id' => $lang['block_id'], 'BlockLang.lang_id' => $lang['lang_id']));
                        }else{
                            //Insert
                            $this->BlockLang->save($lang);
                        }
                    }
                    //Avons-nous un msg pour l'utilisateur ??
                    if(isset($msg))
                        $this->Session->setFlash(__($msg), 'flash_warning');
                    else
                        $this->Session->setFlash(__('Mise à jour du block'), 'flash_success');
                }else
                    $this->Session->setFlash(__('Echec de la mise à jour du block'), 'flash_warning');

                $this->redirect(array('controller' => 'block', 'action' => 'list', 'admin' => true), false);
            }

            $this->loadModel('Lang');
            //L'id de la langue, le code, le nom de la langue
            $langs = $this->Lang->getLang(true);

            //On récupère toutes les infos du slide
            $block = $this->Block->find('all',array(
                'fields' => array('Block.*', 'BlockLang.*','Lang.name'),
                'conditions' => array('Block.id' => $id),
                'joins' => array(
                    array(
                        'table' => 'block_langs',
                        'alias' => 'BlockLang',
                        'type'  => 'left',
                        'conditions' => array('BlockLang.block_id = Block.id')
                    ),
                    array(
                        'table' => 'langs',
                        'alias' => 'Lang',
                        'type'  => 'left',
                        'conditions' => array('Lang.id_lang = BlockLang.lang_id')
                    )
                ),
                'recursive' => -1
            ));

            ///Les infos du slide
            $blockDatas = $block[0]['Block'];
            //On explose les id des domains
            $blockDatas['domain'] = explode(',', $blockDatas['domain']);
            //Un tableau qui contient les données pour chaque langue renseigné
            $langDatas = array();
            foreach($block as $key => $blockLang){
                $langDatas[$blockLang['BlockLang']['lang_id']] = $blockLang['BlockLang'];
            }

            //Les domains pour les checkbox
            $domain_select = $this->Domain->find('list', array(
                'fields' => array('domain')
            ));

            //Nombre de domaines
            $count = count($domain_select);
            //La moitié
            $half = floor($count/2);

            $this->set(compact('blockDatas', 'langDatas', 'langs', 'domain_select', 'count', 'half'));
        }

        public function admin_activate($id){
            //on active le slide
            $this->Block->id = $id;
            if($this->Block->saveField('active', 1))
                $this->Session->setFlash(__('Le block a été activé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de l\'activation du block.'),'flash_warning');

            $this->redirect(array('controller' => 'block', 'action' => 'list', 'admin' => true), false);
        }

        public function admin_deactivate($id){
            //on désactive le slide
            $this->Block->id = $id;
            if($this->Block->saveField('active', 0))
                $this->Session->setFlash(__('Le block a été désactivé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de la désactivation du block.'),'flash_warning');

            $this->redirect(array('controller' => 'block', 'action' => 'list', 'admin' => true), false);
        }


        private function validForm($mode, $id = 0){
            //Le template pour les modes
            $template['create'] = array(
                'fieldForm' => array('active'),
               'requiredForm' => array()
            );
            $template['edit'] = array(
                'fieldForm' => array('active', 'id'),
                'requiredForm' => array('id')
            );
            //Les données du formulaire
            $requestData = $this->request->data;

            //Check le formulaire
            $requestData['Block'] = Tools::checkFormField($requestData['Block'], $template[$mode]['fieldForm'], $template[$mode]['requiredForm']);
            if($requestData['Block'] == false){
                $this->Session->setFlash(__('Erreur dans le formulaire'), 'flash_warning');
                if($mode === 'create')
                    $this->redirect(array('controller' => 'block', 'action' => 'create', 'admin' => true), false);
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'block', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }

            //Si aucun domain n'a été checked
            if(empty($requestData['domain'])){
                $this->Session->setFlash(__('Sélectionner au minimum un domaine.'), 'flash_warning');
                if($mode === 'create')
                    return false;
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'block', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }


            //En mode edition, check les langues et photos
            if($mode === 'edit'){
                //On supprime les langues qui n'ont pas été renseigné
                foreach($requestData['BlockLang'] as $key => $lang){
                    $lang = Tools::checkFormField($lang, array('lang_id', 'block_id', 'title', 'link', 'text1', 'text2_1', 'text2_2', 'text2_3', 'text3'), array('lang_id', 'block_id'));
                    if($lang === false){
                        $this->Session->setFlash(__('Erreur avec l\'un des formulaires d\'une langue.'), 'flash_warning');
                        $this->redirect(array('controller' => 'block', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
                    }

                }

                //Si aucune langue n'est renseigné
                if(empty($requestData['BlockLang'])){
                    $this->Session->setFlash(__('Veuillez renseigner au moins une langue.'),'flash_warning');
                    return false;
                }

            }

            //Les domaines
            $requestData['Block']['domain'] = implode(',', array_keys($requestData['domain']));



            return $requestData;
        }
    }