<?php
    App::uses('AppController', 'Controller');
    App::uses('CakeTime', 'Utility');

    class ColumnsController extends AppController {
        public $components = array('Paginator');
        public $uses = array('User', 'Domain', 'LeftColumn');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'columns')));

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
                $this->LeftColumn->create();
                if($this->LeftColumn->save($requestData['LeftColumn'])){
                    $this->Session->setFlash(__('L\'élement a été crée. N\'oubliez pas de configurer les langues.'), 'flash_success');
                    $this->redirect(array('controller' => 'columns', 'action' => 'edit', 'admin' => true, 'id' => $this->LeftColumn->id), false);
                }
                else{
                    $this->Session->setFlash(__('Erreur lors de la sauvegarde'), 'flash_warning');
                    $this->redirect(array('controller' => 'columns', 'action' => 'create', 'admin' => true),false);
                }

                //Redirection listing
                $this->redirect(array('controller' => 'columns', 'action' => 'list', 'admin' => true), false);
            }
        }

        public function admin_list(){
            //Les différents blocs de la colonne
            $this->Paginator->settings = array(
                'fields' => array('LeftColumn.*', 'LeftColumnLang.title'),
                'joins' => array(
                    array(
                        'table' => 'left_column_langs',
                        'alias' => 'LeftColumnLang',
                        'type'  => 'left',
                        'conditions' => array(
                            'LeftColumnLang.left_column_id = LeftColumn.id',
                            'LeftColumnLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'order' => array('LeftColumn.id' => 'desc'),
                'paramType' => 'querystring',
                'limit' => 25
            );

            $column = $this->Paginator->paginate($this->LeftColumn);

            $this->set(compact('column'));
        }

        public function admin_edit($id){
            if($this->request->is('post')){
                $requestData = $this->validForm('edit', $id);

                //En cas d'erreur
                if($requestData === false)
                    $this->redirect(array('controller' => 'columns', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
                elseif(is_array($requestData)){
                    //Si on a un msg pour l'utilisateur
                    if(isset($requestData[0])){
                        $msg = $requestData[0];
                        $requestData = $requestData[1];
                    }
                }

                $updateData = $requestData['LeftColumn'];
                $updateData['validity_end'] = (empty($updateData['validity_end']) ?null:$updateData['validity_end']);
                $updateData = $this->LeftColumn->value($updateData);
                if($this->LeftColumn->updateAll($updateData, array('LeftColumn.id' => $requestData['LeftColumn']['id']))){
                    //Chaque langue
                    $this->loadModel('LeftColumnLang');
                    foreach($requestData['LeftColumnLang'] as $lang){
                        if($this->LeftColumnLang->lang_exist($lang['left_column_id'], $lang['lang_id'])){
                            //Update
                            $updateData = array();
                            $updateData['title'] = $lang['title'];
                            $updateData['alt'] = $lang['alt'];
                            $updateData['name'] = $lang['name'];
                            $updateData['link'] = $lang['link'];
                            $updateData = $this->LeftColumnLang->value($updateData);
                            $this->LeftColumnLang->updateAll($updateData, array('LeftColumnLang.left_column_id' => $lang['left_column_id'], 'LeftColumnLang.lang_id' => $lang['lang_id']));
                        }else{
                            //Insert
                            $this->LeftColumnLang->save($lang);
                        }
                    }
                    //Avons-nous un msg pour l'utilisateur ??
                    if(isset($msg))
                        $this->Session->setFlash(__($msg), 'flash_warning');
                    else
                        $this->Session->setFlash(__('Mise à jour de l\'élément'), 'flash_success');
                }else
                    $this->Session->setFlash(__('Echec de la mise à jour de l\'élément'), 'flash_warning');

                $this->redirect(array('controller' => 'columns', 'action' => 'list', 'admin' => true), false);
            }

            $this->loadModel('Lang');
            //L'id de la langue, le code, le nom de la langue
            $langs = $this->Lang->getLang(true);

            //On récupère toutes les infos de l'élément
            $element = $this->LeftColumn->find('all',array(
                'fields' => array('LeftColumn.*', 'LeftColumnLang.*','Lang.name'),
                'conditions' => array('LeftColumn.id' => $id),
                'joins' => array(
                    array(
                        'table' => 'left_column_langs',
                        'alias' => 'LeftColumnLang',
                        'type'  => 'left',
                        'conditions' => array('LeftColumnLang.left_column_id = LeftColumn.id')
                    ),
                    array(
                        'table' => 'langs',
                        'alias' => 'Lang',
                        'type'  => 'left',
                        'conditions' => array('Lang.id_lang = LeftColumnLang.lang_id')
                    )
                ),
                'recursive' => -1
            ));

            ///Les infos de l'élément
            $elementDatas = $element[0]['LeftColumn'];
            //On explose les id des domains
            $elementDatas['domain'] = explode(',', $elementDatas['domain']);
            //Un tableau qui contient les données pour chaque langue renseigné
            $langDatas = array();
            foreach($element as $key => $elementLang){
                $langDatas[$elementLang['LeftColumnLang']['lang_id']] = $elementLang['LeftColumnLang'];
            }

            //Les domains pour les checkbox
            $domain_select = $this->Domain->find('list', array(
                'fields' => array('domain')
            ));

            //Nombre de domaines
            $count = count($domain_select);
            //La moitié
            $half = floor($count/2);

            $this->set(compact('elementDatas', 'langDatas', 'langs', 'domain_select', 'count', 'half'));
        }

        public function admin_activate($id){
            //on active l'élément
            $this->LeftColumn->id = $id;
            if($this->LeftColumn->saveField('active', 1))
                $this->Session->setFlash(__('L\'élément a été activé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de l\'activation de l\'élement.'),'flash_warning');

            $this->redirect(array('controller' => 'columns', 'action' => 'list', 'admin' => true), false);
        }

        public function admin_deactivate($id){
            //on désactive l'élément
            $this->LeftColumn->id = $id;
            if($this->LeftColumn->saveField('active', 0))
                $this->Session->setFlash(__('L\'élément a été désactivé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de la désactivation de l\'élement.'),'flash_warning');

            $this->redirect(array('controller' => 'columns', 'action' => 'list', 'admin' => true), false);
        }

        private function check_date($date){
            //Les dates dans le bon format
            if(preg_match('/\d{2}-\d{2}-\d{4}/', $date) === 0 || preg_match('/\d{2}-\d{2}-\d{4}/', $date) === false){
                $this->Session->setFlash(__('La date est incorrecte. Respectez le format suivant : JJ-MM-AAA'),'flash_warning');
                return false;
            }
            return true;
        }

        private function validForm($mode, $id = 0){
            //Le template pour les modes
            $template['create'] = array(
                'fieldForm' => array('active', 'position', 'validity_start', 'validity_end'),
                'requiredForm' => array('position', 'validity_start')
            );
            $template['edit'] = array(
                'fieldForm' => array('active', 'id', 'position', 'validity_start', 'validity_end'),
                'requiredForm' => array('id', 'position', 'valididty_start')
            );
            //Les données du formulaire
            $requestData = $this->request->data;

            //Check le formulaire
            $requestData['LeftColumn'] = Tools::checkFormField($requestData['LeftColumn'], $template[$mode]['fieldForm'], $template[$mode]['requiredForm']);
            if($requestData['LeftColumn'] == false){
                $this->Session->setFlash(__('Erreur dans le formulaire'), 'flash_warning');
                if($mode === 'create')
                    $this->redirect(array('controller' => 'columns', 'action' => 'create', 'admin' => true), false);
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'columns', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }

            //Si aucun domain n'a été checked
            if(empty($requestData['domain'])){
                $this->Session->setFlash(__('Sélectionner au minimum un domaine.'), 'flash_warning');
                if($mode === 'create')
                    return false;
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'columns', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }

            //Check sur la date
            if(!$this->check_date($requestData['LeftColumn']['validity_start']))
                if($mode === 'create')
                    return false;
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'columns', 'action' => 'edit', 'admin' => true, 'id' => $id), false);

            //Check sur la date
            if(!empty($requestData['LeftColumn']['validity_end']) && !$this->check_date($requestData['LeftColumn']['validity_end']))
                if($mode === 'create')
                    return false;
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'columns', 'action' => 'edit', 'admin' => true, 'id' => $id), false);

            //Si date de fin renseignée et plus ancien que la date de début
            if(!empty($requestData['LeftColumn']['validity_end'])){
                $tmpDate = explode('-', $requestData['LeftColumn']['validity_start']);
                $date_start = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                $tmpDate = explode('-', $requestData['LeftColumn']['validity_end']);
                $date_end = $tmpDate[2].'-'.$tmpDate[1].'-'.$tmpDate[0];
                if($date_end <= $date_start){
                    $this->Session->setFlash(__('La date de fin est moins récente que la date de début'),'flash_warning');
                    if($mode === 'create')
                        return false;
                    elseif($mode === 'edit')
                        $this->redirect(array('controller' => 'columns', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
                }
            }

            //Si position < 1
            if($requestData['LeftColumn']['position'] < 1){
                $this->Session->setFlash(__('La position minimum est de 1'), 'flash_warning');
                if($mode === 'create')
                    return false;
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'columns', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }

            //En mode edition, check les langues et photos
            if($mode === 'edit'){
                //On supprime les langues qui n'ont pas été renseigné
                foreach($requestData['LeftColumnLang'] as $key => $lang){
                    $lang = Tools::checkFormField($lang, array('lang_id', 'left_column_id', 'title', 'alt', 'file', 'name', 'link'), array('lang_id', 'left_column_id'));
                    if($lang === false){
                        $this->Session->setFlash(__('Erreur avec l\'un des formulaires d\'une langue.'), 'flash_warning');
                        $this->redirect(array('controller' => 'columns', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
                    }

                    //Si y a pas d'image déjà enregistré et si pas de nouvelle image alors on supprime la langue
                    if(empty($lang['name']) && $lang['file']['error'] == 4 && $lang['file']['size'] == 0)
                        unset($requestData['LeftColumnLang'][$key]);
                }

                //Si aucune langue n'est renseigné
                if(empty($requestData['LeftColumnLang'])){
                    $this->Session->setFlash(__('Veuillez renseigner au moins une langue.'),'flash_warning');
                    return false;
                }

                //Flag si tout c'est bien passé ou partiellement ou pas du tout
                $countFile = 0;
                $countMime = 0;
                $countGood = 0;
                $countBad = 0;
                //On sauvegarde les photos, s'il y a photo
                foreach($requestData['LeftColumnLang'] as $key => $lang){
                    //Pour les photos téléchargées
                    if($this->isUploadedFile($lang['file'])){
                        $countFile++;
                        //Les infos de la photo de la langue
                        $dataElement = getimagesize($lang['file']['tmp_name']);

                        //Est-ce un fichier image autorisé ??
                        if(!in_array($dataElement['mime'], array('image/jpeg', 'image/pjpeg', 'image/gif')) ){
                            $countMime++;
                            continue;
                        }

                        //Le nom de l'élément
						if($dataElement['mime'] == 'image/gif')
                        	$filename = $lang['left_column_id'].'-block-'.$lang['lang_id'].'.gif';
						else
							$filename = $lang['left_column_id'].'-block-'.$lang['lang_id'].'.jpg';
                        //On save le nom du fichier
                        $requestData['LeftColumnLang'][$key]['name'] = $filename;

                        //En déplace le fichier
                        if(!move_uploaded_file($lang['file']['tmp_name'], Configure::read('Site.pathLeftColumn').DS.$filename))
                            $countBad++;
                        else
                            $countGood++;
                    }
                }
            }

            //Les domaines
            $requestData['LeftColumn']['domain'] = implode(',', array_keys($requestData['domain']));

            //Restructuration des données
            $date = new DateTime($requestData['LeftColumn']['validity_start']);
            $requestData['LeftColumn']['validity_start'] = $date->format('Y-m-d H:i:s');
            //Date de fin
            if(!empty($requestData['LeftColumn']['validity_end'])){
                $date = new DateTime($requestData['LeftColumn']['validity_end']);
                $requestData['LeftColumn']['validity_end'] = $date->format('Y-m-d H:i:s');
            }

            //Permet de définir le message pour l'utilisateur
            if(isset($countFile)){
                if($countBad != 0 || $countMime != 0){
                    $msg = 'Sur '.$countFile.' image(s) reçue(s)  : '.$countMime.' n\'est(ne sont) pas autorisée(s) / '.$countBad.' n\'a(n\'ont) pu être sauvegardé / '.$countGood.' a(ont) été sauvegardé.';
                    return array(0 => $msg, '1' => $requestData);
                }
            }

            return $requestData;
        }
    }