<?php
    App::uses('AppController', 'Controller');
    App::uses('CakeTime', 'Utility');

    class SlidesController extends AppController {
        public $components = array('Paginator');
        public $uses = array('User', 'Domain', 'Slide');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'slides')));

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
                $this->Slide->create();
                if($this->Slide->save($requestData['Slide'])){
                    $this->Session->setFlash(__('La slide a été crée. N\'oubliez pas de configurer les langues.'), 'flash_success');
                    $this->redirect(array('controller' => 'slides', 'action' => 'edit', 'admin' => true, 'id' => $this->Slide->id), false);
                }
                else{
                    $this->Session->setFlash(__('Erreur lors de la sauvegarde'), 'flash_warning');
                    $this->redirect(array('controller' => 'slides', 'action' => 'create', 'admin' => true),false);
                }

                //Redirection edition
                $this->redirect(array('controller' => 'slides', 'action' => 'list', 'admin' => true), false);
            }
        }

        public function admin_list(){
            //Les slides
			
			 $this->Paginator->settings = array(
				'fields' => array('Slide.*', 'SlideLang.*','Lang.name'),
                'order' => array('Slide.id' => 'desc'),
                'paramType' => 'querystring',
				 'joins' => array(
                    array(
                        'table' => 'slide_langs',
                        'alias' => 'SlideLang',
                        'type'  => 'left',
                        'conditions' => array('SlideLang.slide_id = Slide.id', 'SlideLang.lang_id = 1',)
                    ),
                    array(
                        'table' => 'langs',
                        'alias' => 'Lang',
                        'type'  => 'left',
                        'conditions' => array('Lang.id_lang = SlideLang.lang_id')
                    )
                ),
                'limit' => 25
            );

            $slides = $this->Paginator->paginate($this->Slide);

            $this->set(compact('slides'));
        }

        public function admin_edit($id){
            if($this->request->is('post')){
				$req = $this->request->data;
				$tab_duplicate = $req['Slide'][1];
                $requestData = $this->validForm('edit', $id);

                //En cas d'erreur
                if($requestData === false)
                    $this->redirect(array('controller' => 'slides', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
                elseif(is_array($requestData)){
                    //Si on a un msg pour l'utilisateur
                    if(isset($requestData[0])){
                        $msg = $requestData[0];
                        $requestData = $requestData[1];
                    }
                }
				
				
				if(is_array($tab_duplicate)){
					if($tab_duplicate['duplicate_belgique']){
						$requestData['SlideLang'][11] = $requestData['SlideLang'][1];
						$requestData['SlideLang'][11]['lang_id'] = 11;
					}
					if($tab_duplicate['duplicate_canada']){
						$requestData['SlideLang'][8] = $requestData['SlideLang'][1];
						$requestData['SlideLang'][8]['lang_id'] = 8;
					}
					if($tab_duplicate['duplicate_suisse']){
						$requestData['SlideLang'][10] = $requestData['SlideLang'][1];
						$requestData['SlideLang'][10]['lang_id'] = 10;
					}
					if($tab_duplicate['duplicate_luxembourg']){
						$requestData['SlideLang'][12] = $requestData['SlideLang'][1];
						$requestData['SlideLang'][12]['lang_id'] = 12;
					}
				}

                $updateData = $requestData['Slide'];
                $updateData['validity_end'] = (empty($updateData['validity_end']) ?null:$updateData['validity_end']);
                $updateData = $this->Slide->value($updateData);
                if($this->Slide->updateAll($updateData, array('Slide.id' => $requestData['Slide']['id']))){
                    //Chaque langue
                    $this->loadModel('SlideLang');
                    foreach($requestData['SlideLang'] as $lang){
                        if($this->SlideLang->lang_exist($lang['slide_id'], $lang['lang_id'])){
                            //Update
                            $updateData = array();
                            $updateData['title'] = $lang['title'];
                            $updateData['alt'] = $lang['alt'];
                            $updateData['name'] = $lang['name'];
                            $updateData['link'] = $lang['link'];
							$updateData['code1'] = $lang['code1'];
							if($lang['titre1'] && !$updateData['code1'])$updateData['code1'] = 'P';
							$updateData['titre1'] = $lang['titre1'];
							$updateData['color1'] = $lang['color1'];
							$updateData['size1'] = $lang['size1'];
							//if(!substr_count($updateData['size1'], 'px' ))$updateData['size1'] = $updateData['size1'].'px';
							$updateData['font1'] = $lang['font1'];
							$updateData['code2'] = $lang['code2'];
							if($lang['titre2'] && !$updateData['code2'])$updateData['code2'] = 'P';
							$updateData['titre2'] = $lang['titre2'];
							$updateData['color2'] = $lang['color2'];
							$updateData['size2'] = $lang['size2'];
							//if(!substr_count($updateData['size2'], 'px' ))$updateData['size2'] = $updateData['size2'].'px';
							$updateData['font2'] = $lang['font2'];
							$updateData['code3'] = $lang['code3'];
							if($lang['titre3'] && !$updateData['code3'])$updateData['code3'] = 'P';
							$updateData['titre3'] = $lang['titre3'];
							$updateData['color3'] = $lang['color3'];
							$updateData['size3'] = $lang['size3'];
							//if(!substr_count($updateData['size3'], 'px' ))$updateData['size3'] = $updateData['size3'].'px';
							$updateData['font3'] = $lang['font3'];
							$updateData['code4'] = $lang['code4'];
							if($lang['titre4'] && !$updateData['code4'])$updateData['code4'] = 'P';
							$updateData['titre4'] = $lang['titre4'];
							$updateData['color4'] = $lang['color4'];
							$updateData['size4'] = $lang['size4'];
							//if(!substr_count($updateData['size4'], 'px' ))$updateData['size4'] = $updateData['size4'].'px';
							$updateData['font4'] = $lang['font4'];
							$updateData['code5'] = $lang['code5'];
							if($lang['titre5'] && !$updateData['code5'])$updateData['code5'] = 'P';
							$updateData['titre5'] = $lang['titre5'];
							$updateData['color5'] = $lang['color5'];
							$updateData['size5'] = $lang['size5'];
							//if(!substr_count($updateData['size5'], 'px' ))$updateData['size5'] = $updateData['size5'].'px';
							$updateData['font5'] = $lang['font5'];
							$updateData['align1'] = $lang['align1'];
							$updateData['align2'] = $lang['align2'];
							$updateData['align3'] = $lang['align3'];
							$updateData['align4'] = $lang['align4'];
							$updateData['align5'] = $lang['align5'];
							$updateData['titre_btn1'] = $lang['titre_btn1'];
							$updateData['link_btn1']  = $lang['link_btn1'];
							$updateData['color_btn1']  = $lang['color_btn1'];
							$updateData['back_btn1']  = $lang['back_btn1'];
							$updateData['titre_btn2'] = $lang['titre_btn2'];
							$updateData['link_btn2']  = $lang['link_btn2'];
							$updateData['color_btn2']  = $lang['color_btn2'];
							$updateData['back_btn2']  = $lang['back_btn2'];
							$updateData['color']  = $lang['color'];	
							$updateData['date_fin']  = $lang['date_fin'];	
							$updateData['text_compteur'] = $lang['text_compteur'];
							$updateData['color_compteur']  = $lang['color_compteur'];	
							$updateData['size_compteur']  = $lang['size_compteur'];	
							//if(!substr_count($updateData['size_compteur'], 'px' ))$updateData['size_compteur'] = $updateData['size_compteur'].'px';
                            $updateData = $this->SlideLang->value($updateData);
                            $this->SlideLang->updateAll($updateData, array('SlideLang.slide_id' => $lang['slide_id'], 'SlideLang.lang_id' => $lang['lang_id']));
                        }else{
                            //Insert
                            $this->SlideLang->save($lang);
                        }
                    }
                    //Avons-nous un msg pour l'utilisateur ??
                    if(isset($msg))
                        $this->Session->setFlash(__($msg), 'flash_warning');
                    else
                        $this->Session->setFlash(__('Mise à jour du slide'), 'flash_success');
                }else
                    $this->Session->setFlash(__('Echec de la mise à jour du slide'), 'flash_warning');

                $this->redirect(array('controller' => 'slides', 'action' => 'list', 'admin' => true), false);
            }

            $this->loadModel('Lang');
            //L'id de la langue, le code, le nom de la langue
            $langs = $this->Lang->getLang(true);

            //On récupère toutes les infos du slide
            $slide = $this->Slide->find('all',array(
                'fields' => array('Slide.*', 'SlideLang.*','Lang.name'),
                'conditions' => array('Slide.id' => $id),
                'joins' => array(
                    array(
                        'table' => 'slide_langs',
                        'alias' => 'SlideLang',
                        'type'  => 'left',
                        'conditions' => array('SlideLang.slide_id = Slide.id')
                    ),
                    array(
                        'table' => 'langs',
                        'alias' => 'Lang',
                        'type'  => 'left',
                        'conditions' => array('Lang.id_lang = SlideLang.lang_id')
                    )
                ),
                'recursive' => -1
            ));

            ///Les infos du slide
            $slideDatas = $slide[0]['Slide'];
            //On explose les id des domains
            $slideDatas['domain'] = explode(',', $slideDatas['domain']);
            //Un tableau qui contient les données pour chaque langue renseigné
            $langDatas = array();
            foreach($slide as $key => $slideLang){
                $langDatas[$slideLang['SlideLang']['lang_id']] = $slideLang['SlideLang'];
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
            $this->Slide->id = $id;
            if($this->Slide->saveField('active', 1))
                $this->Session->setFlash(__('Le slide a été activé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de l\'activation du slide.'),'flash_warning');

            $this->redirect(array('controller' => 'slides', 'action' => 'list', 'admin' => true), false);
        }

        public function admin_deactivate($id){
            //on désactive le slide
            $this->Slide->id = $id;
            if($this->Slide->saveField('active', 0))
                $this->Session->setFlash(__('Le slide a été désactivé'),'flash_success');
            else
                $this->Session->setFlash(__('Erreur lors de la désactivation du slide.'),'flash_warning');

            $this->redirect(array('controller' => 'slides', 'action' => 'list', 'admin' => true), false);
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
            $requestData['Slide'] = Tools::checkFormField($requestData['Slide'], $template[$mode]['fieldForm'], $template[$mode]['requiredForm']);
            if($requestData['Slide'] == false){
                $this->Session->setFlash(__('Erreur dans le formulaire'), 'flash_warning');
                if($mode === 'create')
                    $this->redirect(array('controller' => 'slides', 'action' => 'create', 'admin' => true), false);
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'slides', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }

            //Si aucun domain n'a été checked
            if(empty($requestData['domain'])){
                $this->Session->setFlash(__('Sélectionner au minimum un domaine.'), 'flash_warning');
                if($mode === 'create')
                    return false;
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'slides', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }

            //Check sur la date
            if(!$this->check_date($requestData['Slide']['validity_start']))
                if($mode === 'create')
                    return false;
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'slides', 'action' => 'edit', 'admin' => true, 'id' => $id), false);

            //Check sur la date
            if(!empty($requestData['Slide']['validity_end']) && !$this->check_date($requestData['Slide']['validity_end']))
                if($mode === 'create')
                    return false;
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'slides', 'action' => 'edit', 'admin' => true, 'id' => $id), false);

            //Si position < 1
            if($requestData['Slide']['position'] < 1){
                $this->Session->setFlash(__('La position minimum est de 1'), 'flash_warning');
                if($mode === 'create')
                    return false;
                elseif($mode === 'edit')
                    $this->redirect(array('controller' => 'slides', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
            }

            //En mode edition, check les langues et photos
            if($mode === 'edit'){
                //On supprime les langues qui n'ont pas été renseigné
                foreach($requestData['SlideLang'] as $key => $lang){
                    $lang = Tools::checkFormField($lang, array('lang_id', 'slide_id', 'title', 'alt', 'file', 'name', 'link'), array('lang_id', 'slide_id'));
                    if($lang === false){
                        $this->Session->setFlash(__('Erreur avec l\'un des formulaires d\'une langue.'), 'flash_warning');
                        $this->redirect(array('controller' => 'slides', 'action' => 'edit', 'admin' => true, 'id' => $id), false);
                    }

                    //Si y a pas d'image déjà enregistré et si pas de nouvelle image alors on supprime la langue
                    if(empty($lang['name']) && $lang['file']['error'] == 4 && $lang['file']['size'] == 0)
                        unset($requestData['SlideLang'][$key]);
                }

                //Si aucune langue n'est renseigné
                if(empty($requestData['SlideLang'])){
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
                foreach($requestData['SlideLang'] as $key => $lang){
                    //Pour les photos téléchargées
                    if($this->isUploadedFile($lang['file'])){
                        $countFile++;
                        //Les infos de la photo de la langue
                        $dataSlide = getimagesize($lang['file']['tmp_name']);

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
                        $filename = $lang['slide_id'].'-slide-'.$lang['lang_id'].'.jpg';
                        //On save le nom du fichier
                        $requestData['SlideLang'][$key]['name'] = $filename;

                        //En déplace le fichier
                        if(!move_uploaded_file($lang['file']['tmp_name'], Configure::read('Site.pathSlide').DS.$filename))
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
            $requestData['Slide']['domain'] = implode(',', array_keys($requestData['domain']));

            //Restructuration des données
            $date = new DateTime($requestData['Slide']['validity_start']);
            $requestData['Slide']['validity_start'] = $date->format('Y-m-d H:i:s');
            //Date de fin
            if(!empty($requestData['Slide']['validity_end'])){
                $date = new DateTime($requestData['Slide']['validity_end']);
                $requestData['Slide']['validity_end'] = $date->format('Y-m-d H:i:s');
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
		
		public function admin_duplicate($id){
				
				$this->loadModel('Lang');
				$this->loadModel('Slide');
				$this->loadModel('SlideLang');
				//L'id de la langue, le code, le nom de la langue
				$langs = $this->Lang->getLang(true);
				
				
				 $slide = $this->Slide->find('all',array(
					'fields' => array('Slide.*', 'SlideLang.*','Lang.name'),
					'conditions' => array('Slide.id' => $id),
					'joins' => array(
						array(
							'table' => 'slide_langs',
							'alias' => 'SlideLang',
							'type'  => 'left',
							'conditions' => array('SlideLang.slide_id = Slide.id')
						),
						array(
							'table' => 'langs',
							'alias' => 'Lang',
							'type'  => 'left',
							'conditions' => array('Lang.id_lang = SlideLang.lang_id')
						)
					),
					'recursive' => -1
				));
			
				$slider = $slide[0];
				unset($slider['Slide']['id']);
			
				$this->Slide->create();
                $new_id = $this->Slide->save($slider['Slide']);
			
			
				$this->Slide->id = $new_id['Slide']['id'];

				$updateData = $slider['Slide'];
                $updateData['validity_end'] = (empty($updateData['validity_end']) ?null:$updateData['validity_end']);
                $updateData = $this->Slide->value($updateData);
				$this->Slide->updateAll($updateData, array('Slide.id' => $new_id['Slide']['id']));
				
				foreach($slide as $ss){
					
					$updateData = $ss['SlideLang'];
					/*$updateData['title'] = "'".$updateData['title'] ."'";
					$updateData['alt'] = "'".$updateData['alt'] ."'";
					$updateData['name'] = "'".$updateData['name'] ."'";  
					$updateData['code1'] = "'".$updateData['code1'] ."'"; 
					$updateData['code2'] = "'".$updateData['code2'] ."'"; 
					$updateData['code3'] = "'".$updateData['code3'] ."'"; 
					$updateData['code4'] = "'".$updateData['code4'] ."'"; 
					$updateData['code5'] = "'".$updateData['code5'] ."'"; 
					$updateData['titre1'] = "'".$updateData['titre1'] ."'"; 
					$updateData['titre2'] = "'".$updateData['titre2'] ."'"; 
					$updateData['titre3'] = "'".$updateData['titre3'] ."'"; 
					$updateData['titre4'] = "'".$updateData['titre4'] ."'"; 
					$updateData['titre5'] = "'".$updateData['titre5'] ."'"; 
					$updateData['color1'] = "'".$updateData['color1'] ."'"; 
					$updateData['color2'] = "'".$updateData['color2'] ."'"; 
					$updateData['color3'] = "'".$updateData['color3'] ."'"; 
					$updateData['color4'] = "'".$updateData['color4'] ."'"; 
					$updateData['color5'] = "'".$updateData['color5'] ."'"; 
					$updateData['font1'] = "'".$updateData['font1'] ."'"; 
					$updateData['font2'] = "'".$updateData['font2'] ."'"; 
					$updateData['font3'] = "'".$updateData['font3'] ."'"; 
					$updateData['font4'] = "'".$updateData['font4'] ."'"; 
					$updateData['font5'] = "'".$updateData['font5'] ."'"; 
					$updateData['size1'] = "'".$updateData['size1'] ."'"; 
					$updateData['size2'] = "'".$updateData['size2'] ."'"; 
					$updateData['size3'] = "'".$updateData['size3'] ."'"; 
					$updateData['size4'] = "'".$updateData['size4'] ."'"; 
					$updateData['size5'] = "'".$updateData['size5'] ."'"; 
					$updateData['align1'] = "'".$updateData['align1'] ."'"; 
					$updateData['align2'] = "'".$updateData['align2'] ."'"; 
					$updateData['align3'] = "'".$updateData['align3'] ."'"; 
					$updateData['align4'] = "'".$updateData['align4'] ."'"; 
					$updateData['align5'] = "'".$updateData['align5'] ."'"; 
					$updateData['titre_btn1'] = "'".$updateData['titre_btn1'] ."'"; 
					$updateData['link_btn1'] = "'".$updateData['link_btn1'] ."'"; 
					$updateData['titre_btn2'] = "'".$updateData['titre_btn2'] ."'"; 
					$updateData['link_btn2'] = "'".$updateData['link_btn2'] ."'"; 
					$updateData['color_btn1'] = "'".$updateData['color_btn1'] ."'"; 
					$updateData['back_btn1'] = "'".$updateData['back_btn1'] ."'"; 
					$updateData['color_btn2'] = "'".$updateData['color_btn2'] ."'"; 
					$updateData['back_btn2'] = "'".$updateData['back_btn2'] ."'"; 
					$updateData['link'] = "'".$updateData['link'] ."'"; 
					$updateData['color'] = "'".$updateData['color'] ."'"; 
					$updateData['date_fin'] = "'".$updateData['date_fin'] ."'"; 
					$updateData['text_compteur'] = "'".$updateData['text_compteur'] ."'"; 
					$updateData['color_compteur'] = "'".$updateData['color_compteur'] ."'"; 
					$updateData['size_compteur'] = "'".$updateData['size_compteur'] ."'"; */
					$updateData['slide_id'] = $new_id['Slide']['id'];
					$this->SlideLang->save($updateData);
					//$this->SlideLang->updateAll($updateData, array('SlideLang.slide_id' => $new_id['Slide']['id'], 'SlideLang.lang_id' => $ss['SlideLang']['lang_id']));
				}
			
				$this->redirect(array('controller' => 'slides', 'action' => 'list', 'admin' => true), false);
				
		}
    }