<?php
    App::uses('AppController', 'Controller');
    App::uses('CakeTime', 'Utility');

    class SlidepricemobilesController extends AppController {
        public $components = array('Paginator');
        public $uses = array('User', 'Domain', 'Slidepricemobile');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'slidepricemobiles')));

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
                $this->Slidepricemobile->create();
                if($this->Slidepricemobile->save($requestData['Slidepricemobile'])){
                    $this->Session->setFlash(__('La slide a été crée. N\'oubliez pas de configurer les langues.'), 'flash_success');
                    $this->redirect(array('controller' => 'Slidepricemobiles', 'action' => 'edit', 'admin' => true, 'id' => $this->Slidepricemobile->id), false);
                }
                else{
                    $this->Session->setFlash(__('Erreur lors de la sauvegarde'), 'flash_warning');
                    $this->redirect(array('controller' => 'Slidepricemobiles', 'action' => 'create', 'admin' => true),false);
                }

                //Redirection edition
                $this->redirect(array('controller' => 'Slidepricemobiles', 'action' => 'list', 'admin' => true), false);
            }
        }

        public function admin_list(){
            //Les slides
			
			$this->Paginator->settings = array(
				'fields' => array('Slidepricemobile.*', 'SlidepricemobileLang.*','Lang.name'),
                'order' => array('Slidepricemobile.id' => 'desc'),
                'paramType' => 'querystring',
				 'joins' => array(
                    array(
                        'table' => 'slidepricemobile_langs',
                        'alias' => 'SlidepricemobileLang',
                        'type'  => 'left',
                        'conditions' => array('SlidepricemobileLang.slide_id = Slidepricemobile.id', 'SlidepricemobileLang.lang_id = 1',)
                    ),
                    array(
                        'table' => 'langs',
                        'alias' => 'Lang',
                        'type'  => 'left',
                        'conditions' => array('Lang.id_lang = SlidepricemobileLang.lang_id')
                    )
                ),
                'limit' => 25
            );

            $slidepricemobiles = $this->Paginator->paginate($this->Slidepricemobile);
            $this->set(compact('slidepricemobiles'));
        }

        public function admin_edit($id){
            if($this->request->is('post')){
				$requestData = $this->request->data;
				$tab_duplicate = $requestData['Slidepricemobile'][1];
                $requestData = $this->validForm('edit', $id);
				
                //En cas d'erreur
                if($requestData === false)
                    $this->redirect(array('controller' => 'slidepricemobiles', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
                elseif(is_array($requestData)){
                    //Si on a un msg pour l'utilisateur
                    if(isset($requestData[0])){
                        $msg = $requestData[0];
                        $requestData = $requestData[1];
                    }
                }
				if(is_array($tab_duplicate)){
					if($tab_duplicate['duplicate_belgique']){
						$requestData['SlidepricemobileLang'][11] = $requestData['SlidepricemobileLang'][1];
						$requestData['SlidepricemobileLang'][11]['lang_id'] = 11;
					}
					if($tab_duplicate['duplicate_canada']){
						$requestData['SlidepricemobileLang'][8] = $requestData['SlidepricemobileLang'][1];
						$requestData['SlidepricemobileLang'][8]['lang_id'] = 8;
					}
					if($tab_duplicate['duplicate_suisse']){
						$requestData['SlidepricemobileLang'][10] = $requestData['SlidepricemobileLang'][1];
						$requestData['SlidepricemobileLang'][10]['lang_id'] = 10;
					}
					if($tab_duplicate['duplicate_luxembourg']){
						$requestData['SlidepricemobileLang'][12] = $requestData['SlidepricemobileLang'][1];
						$requestData['SlidepricemobileLang'][12]['lang_id'] = 12;
					}
				}
				
                $updateData = $requestData['Slidepricemobile'];
                $updateData['validity_end'] = (empty($updateData['validity_end']) ?null:$updateData['validity_end']);
                $updateData = $this->Slidepricemobile->value($updateData);
                if($this->Slidepricemobile->updateAll($updateData, array('Slidepricemobile.id' => $requestData['Slidepricemobile']['id']))){
                    //Chaque langue
                    $this->loadModel('SlidepricemobileLang');
					$lang_done = array();
                    foreach($requestData['SlidepricemobileLang'] as $lang){
						if(!in_array($lang['lang_id'],$lang_done )){
                        if($this->SlidepricemobileLang->lang_exist($lang['slide_id'], $lang['lang_id'])){
                            //Update
							if($lang['title_mobile']){
									$updateData = array();
									$updateData['date_fin'] = $lang['date_fin'];
									$updateData['text_compteur'] = $lang['text_compteur'];
									$updateData['title_mobile'] = $lang['title_mobile'];
									$updateData['name_mobile'] = $lang['name_mobile'];
									$updateData['font_font_mobile_1'] = $lang['font_font_mobile_1'];
									$updateData['font_size_mobile_1'] = $lang['font_size_mobile_1'];
								//if(!substr_count($updateData['font_size_mobile_1'], 'px' ))$updateData['font_size_mobile_1'] = $updateData['font_size_mobile_1'].'px';
									$updateData['font_color_mobile_1'] = $lang['font_color_mobile_1'];
									$updateData['date_fin_mobile_size'] = $lang['date_fin_mobile_size'];
									$updateData['date_fin_mobile_color'] = $lang['date_fin_mobile_color'];
									
									$updateData = $this->SlidepricemobileLang->value($updateData);
									$ret = $this->SlidepricemobileLang->updateAll($updateData, array('SlidepricemobileLang.slide_id' => $lang['slide_id'], 'SlidepricemobileLang.lang_id' => $lang['lang_id']));
							}
                        }else{
                            //Insert
                            $this->SlidepricemobileLang->save($lang);
                        }
						}
						array_push($lang_done , $lang['lang_id']);
                    }
                    //Avons-nous un msg pour l'utilisateur ??
                    if(isset($msg))
                        $this->Session->setFlash(__($msg), 'flash_warning');
                    else
                        $this->Session->setFlash(__('Mise à jour du slide'), 'flash_success');
                }else
                    $this->Session->setFlash(__('Echec de la mise à jour du slide'), 'flash_warning');

				$this->redirect(array('controller' => 'Slidepricemobiles', 'action' => 'list', 'admin' => true), false);
            }

            $this->loadModel('Lang');
            //L'id de la langue, le code, le nom de la langue
            $langs = $this->Lang->getLang(true);

            //On récupère toutes les infos du slide
            $slide = $this->Slidepricemobile->find('all',array(
                'fields' => array('Slidepricemobile.*', 'SlidepricemobileLang.*','Lang.name'),
                'conditions' => array('Slidepricemobile.id' => $id),
                'joins' => array(
                    array(
                        'table' => 'slidepricemobile_langs',
                        'alias' => 'SlidepricemobileLang',
                        'type'  => 'left',
                        'conditions' => array('SlidepricemobileLang.slide_id = Slidepricemobile.id')
                    ),
                    array(
                        'table' => 'langs',
                        'alias' => 'Lang',
                        'type'  => 'left',
                        'conditions' => array('Lang.id_lang = SlidepricemobileLang.lang_id')
                    )
                ),
                'recursive' => -1
            ));

            ///Les infos du slide
            $slideDatas = $slide[0]['Slidepricemobile'];
            //On explose les id des domains
            $slideDatas['domain'] = explode(',', $slideDatas['domain']);
            //Un tableau qui contient les données pour chaque langue renseigné
            $langDatas = array();
            foreach($slide as $key => $slideLang){
                $langDatas[$slideLang['SlidepricemobileLang']['lang_id']] = $slideLang['SlidepricemobileLang'];
            }

            //Les domains pour les checkbox
            $domain_select = $this->Domain->find('list', array(
                'fields' => array('domain')
            ));

            //Nombre de domaines
            $count = count($domain_select);
            //La moitié
            $half = floor($count/2);

            $this->set(compact('slideDatas', 'langDatas', 'langs', 'domain_select', 'count', 'half'));
        }

        public function admin_activate($id){
            //on active le slide
            $this->Slidepricemobile->id = $id;
            if($this->Slidepricemobile->saveField('active', 1))
                $this->Session->setFlash(__('Le slide a été activé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de l\'activation du slide.'),'flash_warning');

            $this->redirect(array('controller' => 'Slidepricemobiles', 'action' => 'list', 'admin' => true), false);
        }

        public function admin_deactivate($id){
            //on désactive le slide
            $this->Slidepricemobile->id = $id;
            if($this->Slidepricemobile->saveField('active', 0))
                $this->Session->setFlash(__('Le slide a été désactivé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de la désactivation du slide.'),'flash_warning');

            $this->redirect(array('controller' => 'Slidepricemobiles', 'action' => 'list', 'admin' => true), false);
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
            $requestData['Slidepricemobile'] = Tools::checkFormField($requestData['Slidepricemobile'], $template[$mode]['fieldForm'], $template[$mode]['requiredForm']);
            if($requestData['Slidepricemobile'] == false){
                $this->Session->setFlash(__('Erreur dans le formulaire'), 'flash_warning');
                if($mode === 'create')
                    $this->redirect(array('controller' => 'Slidepricemobiles', 'action' => 'create', 'admin' => true), false);
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'Slidepricemobiles', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }

            //Si aucun domain n'a été checked
            if(empty($requestData['domain'])){
                $this->Session->setFlash(__('Sélectionner au minimum un domaine.'), 'flash_warning');
                if($mode === 'create')
                    return false;
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'Slidepricemobiles', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }

            //Check sur la date
            if(!$this->check_date($requestData['Slidepricemobile']['validity_start']))
                if($mode === 'create')
                    return false;
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'Slidepricemobiles', 'action' => 'edit', 'admin' => true, 'id' => $id), false);

            //Check sur la date
            if(!empty($requestData['Slidepricemobile']['validity_end']) && !$this->check_date($requestData['Slidepricemobile']['validity_end']))
                if($mode === 'create')
                    return false;
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'Slidepricemobiles', 'action' => 'edit', 'admin' => true, 'id' => $id), false);

            //Si position < 1
            if($requestData['Slidepricemobile']['position'] < 1){
                $this->Session->setFlash(__('La position minimum est de 1'), 'flash_warning');
                if($mode === 'create')
                    return false;
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'Slidepricemobiles', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }

            //En mode edition, check les langues et photos
            if($mode === 'edit'){
                //On supprime les langues qui n'ont pas été renseigné
                foreach($requestData['SlidepricemobileLang'] as $key => $lang){
                    $lang = Tools::checkFormField($lang, array('lang_id', 'slide_id',  'date_fin','title_mobile', 'font_size_mobile_1', 'font_color_mobile_1', 'date_fin_mobile_size', 'date_fin_mobile_color'), array('lang_id', 'slide_id'));
                    if($lang === false){
                        $this->Session->setFlash(__('Erreur avec l\'un des formulaires d\'une langue.'), 'flash_warning');
                        $this->redirect(array('controller' => 'Slidepricemobiles', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
                    }

                    //Si y a pas d'image déjà enregistré et si pas de nouvelle image alors on supprime la langue
                   // if(empty($lang['name']) && $lang['file']['error'] == 4 && $lang['file']['size'] == 0)
                       //unset($requestData['SlidepriceLang'][$key]);
                }

                //Si aucune langue n'est renseigné
                if(empty($requestData['SlidepricemobileLang'])){
                    $this->Session->setFlash(__('Veuillez renseigner au moins une langue.'),'flash_warning');
                    return false;
                }

                //Flag si tout c'est bien passé ou partiellement ou pas du tout
                $countFile = 0;
                $countSize = 0;
                $countMime = 0;
                $countGood = 0;
                $countBad = 0;
                //On sauvegarde les photos, s'il y a photo
                foreach($requestData['SlidepricemobileLang'] as $key => $lang){
                    //Pour les photos téléchargées

					
					if($this->isUploadedFile($lang['file_mobile'])){
                        $countFile++;
                        //Les infos de la photo de la langue
                        $dataSlide = getimagesize($lang['file_mobile']['tmp_name']);

                        //Est-ce un fichier image autorisé ??
                        if(!in_array($dataSlide['mime'], array('image/jpeg', 'image/pjpeg'))){
                            $countMime++;
                            continue;
                        }

                        //Test pour la taille de l'image
                        /*if($dataSlide[0] !== Configure::read('Slide.width')){// || $dataSlide[1] !== Configure::read('Slide.height')
                            $countSize++;
                            continue;
                        }*/

                        //Le nom du slide
                        $filename = $lang['slide_id'].'-Slidepricemobile-'.$lang['lang_id'].'.jpg';
                        //On save le nom du fichier
                        $requestData['SlidepricemobileLang'][$key]['name_mobile'] = $filename;

                        //En déplace le fichier
                        if(!move_uploaded_file($lang['file_mobile']['tmp_name'], Configure::read('Site.pathSlideprice').DS.$filename))
                            $countBad++;
                        else
                            $countGood++;


                        //Est-ce un fichier image autorisé ??
                        /*if(Tools::formatFile($this->allowed_mime_types, $lang['file']['type'],'Image')){
                            //La taille de l'image
                            $dataSlide = getimagesize($lang['file']['tmp_name']);
                            //Extension du fichier
                            $mime = explode('/', $dataSlide['mime']);
                            //Le nom du fichier
                            $filename = $lang['slide_id'].'-slide-'.$lang['lang_id'].'.jpg';
                            //On save le nom du fichier
                            $requestData['SlideLang'][$key]['name'] = $filename;

                            if(!Tools::imageCropAndResized($lang['file']['tmp_name'], Configure::read('Site.pathSlide').DS.$filename,$mime[1],
                                0,0,$dataSlide[1],$dataSlide[0],Configure::read('Slide.height'),Configure::read('Slide.width'))){
                                $this->Session->setFlash(__('Impossible de redimensionner votre slide.'), 'flash_warning');
                                $this->redirect(array('controller' => 'slides', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
                            }
                        }*/
                    }
                }
            }

            //Les domaines
            $requestData['Slidepricemobile']['domain'] = implode(',', array_keys($requestData['domain']));

            //Restructuration des données
            $date = new DateTime($requestData['Slidepricemobile']['validity_start']);
            $requestData['Slidepricemobile']['validity_start'] = $date->format('Y-m-d H:i:s');
            //Date de fin
            if(!empty($requestData['Slidepricemobile']['validity_end'])){
                $date = new DateTime($requestData['Slidepricemobile']['validity_end']);
                $requestData['Slidepricemobile']['validity_end'] = $date->format('Y-m-d H:i:s');
            }

            //Permet de définir le message pour l'utilisateur
            if(isset($countFile)){
                if($countSize != 0 || $countBad != 0 || $countMime != 0){
                    $msg = 'Sur '.$countFile.' image(s) reçue(s)  : '.$countMime.' n\'est(ne sont) pas autorisée(s) / '.$countSize.' n\'est(ne sont) pas dans la bonne taille / '.$countBad.' n\'a(n\'ont) pu être sauvegardé / '.$countGood.' a(ont) été sauvegardé.';
                    return array(0 => $msg, '1' => $requestData);
                }
            }

            return $requestData;
        }
    }