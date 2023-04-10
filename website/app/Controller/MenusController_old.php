<?php
    App::uses('AppController', 'Controller');
    App::uses('CakeTime', 'Utility');

    class MenusController_old extends AppController {
        public $components = array('Paginator');
        public $uses = array('TopMenu', 'Category', 'PageCategory', 'MenuLink', 'MenuLinkLang', 'MenuBlockLang', 'MenuBlock');
        public $helpers = array('Paginator' => array('url' => array('controller' => 'menus')));

        public function beforeFilter() {

            parent::beforeFilter();
        }

        public function admin_menu(){
            if($this->request->is('post')){
                $requestData = $this->request->data;

                //On décode le menu
                $requestData['Menu'] = json_decode($requestData['Menu'], true);

                //Si le menu vide
                if(empty($requestData['Menu'])){
                    $this->Session->setFlash(__('Veuillez sélectionner au minimum un menu.'),'flash_warning');
                    $this->redirect(array('controller' => 'menus', 'action' => 'menu', 'admin' => true),false);
                }

                //On supprime le menu actuel
                $this->TopMenu->deleteAll(array('TopMenu.id !=' => 0),false);
                //On save le nouveau menu
                if($this->TopMenu->saveMany($requestData['Menu'])){
                    //On génére le cache avec le nouveau menu
                    $this->generateMenu();
                    $this->Session->setFlash(__('Le nouveau menu est enregistré.'),'flash_success');
                }
                else
                    $this->Session->setFlash(__('Erreur le nouveau menu n\'a pu être sauvegardé.'),'flash_warning');
                $this->redirect(array('controller' => 'menus', 'action' => 'menu', 'admin' => true), false);
            }

            if(isset($this->params->query['modif'])){
                $this->Session->setFlash(__('Cliquez sur "Enregistrer" pour mettre à jour le menu.'), 'flash_info');
                unset($this->params->query['modif']);
            }


            //Tout les univers
            $menu_select['univers'] = $this->Category->find('all', array(
                'fields' => array('Category.id', 'Category.active', 'CategoryLang.name'),
                'joins' => array(
                    array(
                        'table' => 'category_langs',
                        'alias' => 'CategoryLang',
                        'type' => 'inner',
                        'conditions' => array(
                            'CategoryLang.category_id = Category.id',
                            'CategoryLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'order' => 'CategoryLang.name asc',
                'recursive' => -1
            ));

            //Toutes les catégories
            $menu_select['cms'] = $this->PageCategory->find('all', array(
                'fields' => array('PageCategory.id', 'PageCategory.active', 'PageCategoryLang.name'),
                'joins' => array(
                    array(
                        'table' => 'page_category_langs',
                        'alias' => 'PageCategoryLang',
                        'type' => 'inner',
                        'conditions' => array(
                            'PageCategoryLang.page_category_id = PageCategory.id',
                            'PageCategoryLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'order' => 'PageCategoryLang.name asc',
                'recursive' => -1
            ));

            //Tout les liens
            $menu_select['link'] = $this->MenuLink->find('all',  array(
                'fields' => array('MenuLink.id', 'MenuLinkLang.title'),
                'joins' => array(
                    array(
                        'table' => 'menu_link_langs',
                        'alias' => 'MenuLinkLang',
                        'type' => 'left',
                        'conditions' => array(
                            'MenuLinkLang.menu_link_id = MenuLink.id',
                            'MenuLinkLang.lang_id = '.$this->Session->read('Config.id_lang')
                        )
                    )
                ),
                'order' => 'MenuLinkLang.title asc',
                'recursive' => -1
            ));

            //Toutes les pages spiriteo

            //Le menu actuel
            $current_menu = $this->TopMenu->find('all', array(
                'order' => 'position asc',
                'recursive' => -1
            ));

            //Le nom de toutes les univers associés avec leur id
            $listUnivers = $this->Category->CategoryLang->find('list', array(
                'fields' => array('category_id', 'name'),
                'conditions' => array('CategoryLang.lang_id' => $this->Session->read('Config.id_lang')),
                'recursive' => -1
            ));

            //Le nom de toutes les catégorie cms associés avec leur id
            $listCMS = $this->PageCategory->PageCategoryLang->find('list', array(
                'fields' => array('page_category_id', 'name'),
                'conditions' => array('PageCategoryLang.lang_id' => $this->Session->read('Config.id_lang')),
                'recursive' => -1
            ));

            //Le nom de tout les lien associés avec leur id
            $listLink = $this->MenuLinkLang->find('list', array(
                'fields' => array('MenuLinkLang.menu_link_id', 'MenuLinkLang.title'),
                'conditions' => array('MenuLinkLang.lang_id' => $this->Session->read('Config.id_lang')),
                'recursive' => -1
            ));

            //Pour chaque menu, on va récupérer le nom
            foreach($current_menu as $key => $row){
                //Si c'est l'accueil
                if($row['TopMenu']['contenu_id'] == 0)
                    $current_menu[$key]['TopMenu']['name'] = __('Accueil');
                elseif($row['TopMenu']['contenu_type'] === 'univers')
                    $current_menu[$key]['TopMenu']['name'] = (isset($listUnivers[$row['TopMenu']['contenu_id']]) ?$listUnivers[$row['TopMenu']['contenu_id']]:__('Pas de nom'));
                elseif($row['TopMenu']['contenu_type'] === 'cms')
                    $current_menu[$key]['TopMenu']['name'] = (isset($listCMS[$row['TopMenu']['contenu_id']]) ?$listCMS[$row['TopMenu']['contenu_id']]:__('Pas de nom'));
                elseif($row['TopMenu']['contenu_type'] === 'link')
                    $current_menu[$key]['TopMenu']['name'] = (isset($listLink[$row['TopMenu']['contenu_id']]) ?$listLink[$row['TopMenu']['contenu_id']]:__('Pas de nom'));
            }

            $this->set(compact('menu_select', 'current_menu'));
        }

        public function admin_add_link(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                if(isset($requestData['idLink'])){
                    $this->getView($requestData['idLink'],'/Elements/admin_form_link');
                }
                $this->jsonRender(array('return' => false, 'msg' => __('Impossible de créer un nouveau lien.')));
            }
        }

        public function admin_add_block_link(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                if(isset($requestData['idBlockLink'])){
                    $this->getView($requestData['idBlockLink'],'/Elements/admin_form_block_link');
                }
                $this->jsonRender(array('return' => false, 'msg' => __('Impossible de créer un nouveau bloc de lien.')));
            }
        }

        public function admin_create_link(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                $requestData['MenuLink'] = Tools::checkFormField($requestData['MenuLink'], array('target_blank'));
                if($requestData['MenuLink'] === false)
                    $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de créer le lien')));

                //Pour chaque lang
                foreach($requestData['MenuLinkLang'] as $key => $val){
                    //Si le formulaire est vide
                    if(count(array_keys($val, '')) >= 2)
                        //On le supprime
                        unset($requestData['MenuLinkLang'][$key]);
                }

                //Si aucune langue n'est renseigné
                if(empty($requestData['MenuLinkLang']))
                    $this->jsonRender(array('return' => false, 'clean' => false, 'msg' => __('Renseigné au moins une langue.')));

                //Les langues restantes
                foreach($requestData['MenuLinkLang'] as $lang){
                    $lang = Tools::checkFormField($lang, array('lang_id', 'title', 'link'), array('lang_id', 'title', 'link'));
                    if($lang === false)
                        $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de créer le lien')));
                }

                //On save le nouveau lien
                $this->MenuLink->create();
                if($this->MenuLink->save($requestData['MenuLink'])){
                    //On sauvegarde les langues
                    foreach($requestData['MenuLinkLang'] as $lang){
                        $lang['menu_link_id'] = $this->MenuLink->id;
                        $this->MenuLinkLang->save($lang);
                    }

                    $this->jsonRender(array('return' => true, 'clean' => true, 'msg' => __('Le lien a été crée.'),
                                            'update' => Router::url(array('controller' => 'menus', 'action' => 'update', 'admin' => true))));
                }else
                    $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Erreur lors de la sauvegarde du lien.')));
            }
        }

        public function admin_create_block_link(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                //Pour chaque lang
                foreach($requestData['MenuBlockLang'] as $key => $val){
                    //Si le formulaire est vide
                    if(count(array_keys($val, '')) >= 1)
                        //On le supprime
                        unset($requestData['MenuBlockLang'][$key]);
                }

                //Si aucune langue n'est renseigné
                if(empty($requestData['MenuBlockLang']))
                    $this->jsonRender(array('return' => false, 'clean' => false, 'msg' => __('Renseigné au moins une langue.')));

                //Les langues restantes
                foreach($requestData['MenuBlockLang'] as $lang){
                    $lang = Tools::checkFormField($lang, array('lang_id', 'title'), array('lang_id', 'title'));
                    if($lang === false)
                        $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de créer le bloc de lien')));
                }


                //On save le nouveau bloc
                $this->MenuBlock->create();
                if($this->MenuBlock->save(array('active' => 1))){
                    //On sauvegarde les langues
                    foreach($requestData['MenuBlockLang'] as $lang){
                        $lang['menu_block_id'] = $this->MenuBlock->id;
                        $this->MenuBlockLang->save($lang);
                    }

                    $this->jsonRender(array('return' => true, 'clean' => true, 'msg' => __('Le bloc de lien a été crée.'),
                                            'update' => Router::url(array('controller' => 'menus', 'action' => 'update', 'admin' => true))));
                }else
                    $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Erreur lors de la sauvegarde du bloc de lien.')));
            }
        }

        public function admin_update(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                if(isset($requestData['mode'])){
                    //Selon l'update demandé
                    switch($requestData['mode']){
                        case 'menu_link' :
                            //Tout les liens
                            $links = $this->MenuLink->find('all', array(
                                'fields' => array('MenuLink.id', 'MenuLinkLang.title'),
                                'joins' => array(
                                    array(
                                        'table' => 'menu_link_langs',
                                        'alias' => 'MenuLinkLang',
                                        'type' => 'left',
                                        'conditions' => array(
                                            'MenuLinkLang.menu_link_id = MenuLink.id',
                                            'MenuLinkLang.lang_id = '.$this->Session->read('Config.id_lang')
                                        )
                                    )
                                ),
                                'order' => 'MenuLinkLang.title asc',
                                'recursive' => -1
                            ));

                            $this->getView(0, '/Elements/admin_select_link', $links);
                            break;
                        case 'menu_block_link' :
                            //Tout les blocs de liens
                            $blocks = $this->MenuBlockLang->find('list', array(
                                'fields'        => array('menu_block_id', 'title'),
                                'conditions'    => array('MenuBlockLang.lang_id' => $this->Session->read('Config.id_lang')),
                                'recursive'     => -1
                            ));

                            $this->getView(0, '/Elements/admin_select_block_link', $blocks);
                            break;
                        case 'link_action' :
                            $this->getView($requestData['idLink'], '/Elements/admin_link_action');
                            break;
                        case 'block_link_action' :
                            $this->getView($requestData['idLink'], '/Elements/admin_block_link_action');
                            break;
                        case 'data' :
                            //On récupère les infos du lien
                            $link = $this->MenuLink->find('all', array(
                                'fields' => array('MenuLink.*', 'MenuLinkLang.*'),
                                'conditions' => array('MenuLink.id' => $requestData['idLink']),
                                'joins' => array(
                                array(
                                    'table' => 'menu_link_langs',
                                    'alias' => 'MenuLinkLang',
                                    'type' => 'left',
                                    'conditions' => array('MenuLinkLang.menu_link_id = MenuLink.id')
                                )
                            ),
                                'recursive' => -1
                            ));

                            if(empty($link))
                                $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Lien introuvable')));

                            //Pour chaque langue
                            $data = array();
                            foreach($link as $key => $lang){
                                $data[$lang['MenuLinkLang']['lang_id']]['title'] = $lang['MenuLinkLang']['title'];
                                $data[$lang['MenuLinkLang']['lang_id']]['link'] = $lang['MenuLinkLang']['link'];
                            }

                            //On rajoute les infos du lien
                            $data['MenuLink'] = $link[0]['MenuLink'];

                            //On va chercher la vue
                            $this->getView($data['MenuLink']['id'], '/Elements/admin_form_link', $data);
                            break;
                        case 'block_data' :
                            //On récupère les infos du block
                            $block = $this->MenuBlockLang->find('all', array(
                                'fields'        => array('MenuBlockLang.*'),
                                'conditions'    => array('MenuBlockLang.menu_block_id' => $requestData['idLink']),
                                'recursive'     => -1
                            ));

                            if(empty($block))
                                $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Bloc introuvable')));

                            //Pour chaque langue
                            $data = array();
                            foreach($block as $key => $lang){
                                $data[$lang['MenuBlockLang']['lang_id']]['title'] = $lang['MenuBlockLang']['title'];
                            }

                            //On rajoute les infos du lien
                            $data['menu_block_id'] = $block[0]['MenuBlockLang']['menu_block_id'];

                            //On va chercher la vue
                            $this->getView($data['menu_block_id'], '/Elements/admin_form_block_link', $data);
                            break;
                    }
                }
            }
        }

        public function admin_edit_link(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                //L'url pour de l'action update
                $updateUrl = Router::url(array('controller' => 'menus', 'action' => 'update', 'admin' => true));

                $requestData['MenuLink'] = Tools::checkFormField($requestData['MenuLink'], array('target_blank', 'id'), array('id'));
                if($requestData['MenuLink'] === false)
                    $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de modifier le lien'), 'update' => $updateUrl));

                //Pour chaque lang
                foreach($requestData['MenuLinkLang'] as $key => $val){
                    //Si le formulaire est vide
                    if(count(array_keys($val, '')) >= 2)
                        //On le supprime
                        unset($requestData['MenuLinkLang'][$key]);
                }

                //Si aucune langue n'est renseigné
                if(empty($requestData['MenuLinkLang']))
                    $this->jsonRender(array('return' => false, 'clean' => false, 'msg' => __('Renseigné au moins une langue.')));

                //Les langues restantes
                foreach($requestData['MenuLinkLang'] as $lang){
                    $lang = Tools::checkFormField($lang, array('lang_id', 'title', 'link'), array('lang_id', 'title', 'link'));
                    if($lang === false)
                        $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de modifier le lien'), 'update' => $updateUrl));
                }

                //On supprime les langues actuelles
                $this->MenuLinkLang->deleteAll(array('MenuLinkLang.menu_link_id' => $requestData['MenuLink']['id']), false);

                //On met à jour le lien
                $this->MenuLink->id = $requestData['MenuLink']['id'];
                if($this->MenuLink->saveField('target_blank', $requestData['MenuLink']['target_blank'])){
                    //On sauvegarde les langues
                    foreach($requestData['MenuLinkLang'] as $lang){
                        $lang['menu_link_id'] = $requestData['MenuLink']['id'];
                        $this->MenuLinkLang->save($lang);
                    }

                    //On va charcher le nom du lien pour la langue en cours
                    $link_name = $this->MenuLinkLang->find('first', array(
                        'fields' => array('MenuLinkLang.title'),
                        'conditions' => array('MenuLinkLang.menu_link_id' => $requestData['MenuLink']['id'], 'MenuLinkLang.lang_id' => $this->Session->read('Config.id_lang')),
                        'recursive' => -1
                    ));

                    $link_name = (isset($link_name['MenuLinkLang']['title']) && !empty($link_name['MenuLinkLang']['title']) ?$link_name['MenuLinkLang']['title']:__('Sans titre'));

                    $this->jsonRender(array('return' => true, 'clean' => true, 'msg' => __('Le lien a été modifié.'), 'id' => $requestData['MenuLink']['id'], 'name' => $link_name,
                                            'update' => $updateUrl));
                }else
                    $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Erreur lors de la MAJ du lien.')));
            }
        }

        public function admin_edit_block_link(){
            if($this->request->is('ajax')){
                $requestData = $this->request->data;

                //L'url pour de l'action update
                $updateUrl = Router::url(array('controller' => 'menus', 'action' => 'update', 'admin' => true));

                //L'id du bloc
                if(!isset($requestData['MenuBlockLang']['menu_block_id']) || empty($requestData['MenuBlockLang']['menu_block_id']))
                    $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de modifier le bloc de lien'), 'update' => $updateUrl));

                $idBlock = $requestData['MenuBlockLang']['menu_block_id'];
                unset($requestData['MenuBlockLang']['menu_block_id']);

                //Pour chaque lang
                foreach($requestData['MenuBlockLang'] as $key => $val){
                    //Si le formulaire est vide
                    if(count(array_keys($val, '')) >= 1)
                        //On le supprime
                        unset($requestData['MenuBlockLang'][$key]);
                }

                //Si aucune langue n'est renseigné
                if(empty($requestData['MenuBlockLang']))
                    $this->jsonRender(array('return' => false, 'clean' => false, 'msg' => __('Renseigné au moins une langue.')));

                //Les langues restantes
                foreach($requestData['MenuBlockLang'] as $lang){
                    $lang = Tools::checkFormField($lang, array('lang_id', 'title'), array('lang_id', 'title'));
                    if($lang === false)
                        $this->jsonRender(array('return' => false, 'clean' => true, 'msg' => __('Impossible de modifier le bloc de lien'), 'update' => $updateUrl));
                }

                //On supprime les langues actuelles
                $this->MenuBlockLang->deleteAll(array('MenuBlockLang.menu_block_id' => $idBlock), false);

                //On met à jour le bloc
                //On sauvegarde les langues
                foreach($requestData['MenuBlockLang'] as $lang){
                    $lang['menu_block_id'] = $idBlock;
                    $this->MenuBlockLang->save($lang);
                }

                $this->jsonRender(array('return' => true, 'clean' => true, 'msg' => __('Le bloc de lien a été modifié.'), 'update' => $updateUrl));
            }
        }

        public function admin_delete_link($id){
            if(empty($id) || !is_numeric($id)){
                $this->Session->setFlash(__('Impossible de supprimer le lien'),'flash_warning');
                $this->redirect(array('controller' => 'menus', 'action' => 'menu', 'admin' => true));
            }

            //On supprime les langues
            $this->MenuLinkLang->deleteAll(array('MenuLinkLang.menu_link_id' => $id), false);
            //On supprime le lien
            $this->MenuLink->deleteAll(array('MenuLink.id' => $id), false);
            //On le supprime du menu
            $this->TopMenu->deleteAll(array('TopMenu.contenu_type' => 'link', 'TopMenu.contenu_id' => $id), false);

            $this->Session->setFlash(__('Le lien a été supprimé.'),'flash_success');
            $this->redirect(array('controller' => 'menus', 'action' => 'menu', 'admin' => true), false);
        }

        public function admin_delete_block_link($id){
            if(empty($id) || !is_numeric($id)){
                $this->Session->setFlash(__('Impossible de supprimer le bloc lien'),'flash_warning');
                $this->redirect(array('controller' => 'menus', 'action' => 'menu', 'admin' => true));
            }

            //On supprime les langues
            $this->MenuBlockLang->deleteAll(array('MenuBlockLang.menu_block_id' => $id), false);
            //On supprime le lien
            $this->MenuBlock->deleteAll(array('MenuBlock.id' => $id), false);

            $this->Session->setFlash(__('Le bloc de lien a été supprimé.'),'flash_success');
            $this->redirect(array('controller' => 'menus', 'action' => 'menu', 'admin' => true), false);
        }

        private function getView($idLink, $view, $data = array()){
            $this->layout = '';
            $this->loadModel('Lang');
            //L'id de la langue, le code, le nom de la langue
            $langs = $this->Lang->getLang(true);
            $this->set(compact('idLink', 'langs', 'data'));
            $response = $this->render($view);

            //On retourne la vue
            $this->jsonRender(array('return' => true, 'html' => $response->body()));
        }

        public function generateMenu(){
            //On va chercher le menu
            $menu = $this->TopMenu->find('all');
            //On va chercher les langues
            $this->loadModel('Lang');
            $langs = $this->Lang->find('list', array(
                'fields'        => array('language_code'),
                'conditions'    => array('active' => 1),
                'recursive'     => -1
            ));

            //L'indice du dernier enregistrement
            $lastKey = count($menu) - 1;
            //Pour chaque langue
            foreach($langs as $lang_id => $language){
                //Init la variable pour le rendu html
                $html = '<ul>';
                //Pour chaque menu
                foreach($menu as $key => $row){
                    if($row['TopMenu']['contenu_id'] == 0){
                        $html.= '<li><a class="parent_title" href="'.Router::url(array('controller' => 'home', 'action' => 'index', 'admin' => false, 'language' => $this->params['language'])).'">'.__('Accueil').'</a></li>';
                        if($key !== $lastKey)
                            $html.= '<li class="sep"></li>';
                    }elseif($row['TopMenu']['contenu_type'] === 'univers'){
                        //On va chercher les infos de l'univers
                        $dataUnivers = $this->Category->find('first', array(
                            'fields' => array('CategoryLang.name','CategoryLang.link_rewrite','CategoryLang.category_id'),
                            'conditions' => array('Category.active' => 1, 'Category.id' => $row['TopMenu']['contenu_id']),
                            'joins' => array(
                                array(
                                    'table' => 'category_langs',
                                    'alias' => 'CategoryLang',
                                    'type' => 'inner',
                                    'conditions' => array(
                                        'CategoryLang.category_id = Category.id',
                                        'CategoryLang.lang_id = '.$lang_id
                                    )
                                )
                            ),
                            'recursive' => -1
                        ));

                        //S'il y a des données
                        if(!empty($dataUnivers)){
                            $html.= '<li><a class="parent_title" href="'.Router::url(array(
                                    'language' => $language,
                                    'controller' => 'category',
                                    'action' => 'displayUnivers',
                                    'admin' => false,
                                    'id' => $dataUnivers['CategoryLang']['category_id'],
                                    'link_rewrite' => $dataUnivers['CategoryLang']['link_rewrite']
                                )).'">'.$dataUnivers['CategoryLang']['name'].'</a></li>';

                            if($key !== $lastKey)
                                $html.= '<li class="sep"></li>';
                        }
                    }elseif($row['TopMenu']['contenu_type'] === 'cms'){
                        //On va chercher les infos de la catégorie cms
                        $dataCms = $this->PageCategory->find('all', array(
                            'fields' => array('PageCategoryLang.name', 'PageLang.page_id', 'PageLang.link_rewrite', 'PageLang.name'),
                            'conditions' => array('PageCategory.id' => $row['TopMenu']['contenu_id'], 'PageCategory.active' => 1),
                            'joins' => array(
                                array(
                                    'table' => 'page_category_langs',
                                    'alias' => 'PageCategoryLang',
                                    'type' => 'inner',
                                    'conditions' => array(
                                        'PageCategoryLang.page_category_id = PageCategory.id',
                                        'PageCategoryLang.lang_id = '.$lang_id
                                    )
                                ),
                                array(
                                    'table' => 'pages',
                                    'alias' => 'Page',
                                    'type' => 'inner',
                                    'conditions' => array(
                                        'Page.page_category_id = PageCategory.id',
                                        'Page.active = 1'
                                    )
                                ),
                                array(
                                    'table' => 'page_langs',
                                    'alias' => 'PageLang',
                                    'type' => 'inner',
                                    'conditions' => array(
                                        'PageLang.page_id = Page.id',
                                        'PageLang.lang_id = '.$lang_id
                                    )
                                )
                            ),
                            'order' => 'PageLang.name asc',
                            'recursive' => -1
                        ));

                        /*if($row['TopMenu']['contenu_id'] == 4){
                            debug($dataCms);
                            die();
                        }*/

                        //On regarde s'il y a des catégories
                        $listingCms = array();
                        foreach($dataCms as $page){
                            //On save le nom de la catégorie parent
                            if(!isset($listingCms['nameCat']))
                                $listingCms['nameCat'] = $page['PageCategoryLang']['name'];

                            //Avons-nous une sous-catégorie ??
                            $explodeData = explode(Configure::read('Site.menuDelimiter'),$page['PageLang']['name']);
                            //Si la taille du tableau est plus grande, alors sous-catégorie repérée
                            if(count($explodeData) > 1){
                                //Si la sous-catégorie n'existe pas encore
                                if(!isset($listingCms[$explodeData[0]]))
                                    $listingCms[$explodeData[0]] = array();

                                //On rajoute la page
                                $listingCms[$explodeData[0]][] = array(
                                    'id'            => $page['PageLang']['page_id'],
                                    'link_rewrite'  => $page['PageLang']['link_rewrite'],
                                    'name'          => $explodeData[1]
                                );
                            }else{
                                //On stocke les infos de la page en cours
                                $listingCms[] = array(
                                    'id'            => $page['PageLang']['page_id'],
                                    'link_rewrite'  => $page['PageLang']['link_rewrite'],
                                    'name'          => $page['PageLang']['name']
                                );
                            }
                        }

                        if(!empty($listingCms)){
                            $html.= '<li><span class="parent_title">'.$listingCms['nameCat'].'</span>';
                            $html.= '<div class="sub_cat grid_12"><ul>';
                            //Pour stocker les liens directs
                            $htmlLink = '';
                            //Pour chaque page de la catégorie ou sous-catégorie
                            foreach($listingCms as $keyCms => $page){
                                //On évite 'nameCat'
                                if($keyCms === 'nameCat')
                                    continue;
                                //est-ce un lien direct ?
                                if(is_int($keyCms))
                                    $htmlLink.= '<li><a class="font-item" href="'.Router::url(array(
                                            'language' => $language,
                                            'controller' => 'pages',
                                            'action' => 'display',
                                            'admin' => false,
                                            'id' => $page['id'],
                                            'link_rewrite' => $page['link_rewrite']
                                        )).'">'.$page['name'].'</a></li>';
                                else{   //Nous avons une sous catégorie
                                    $html.= '<div class="sub_link"><li class="child_title">'. $keyCms .'</li>';
                                    $html.= '<ul>';
                                    //Pour chaque page de la sous-catégorie
                                    foreach($page as $pageCat){
                                        $html.= '<li><a href="'.Router::url(array(
                                                'language' => $language,
                                                'controller' => 'pages',
                                                'action' => 'display',
                                                'admin' => false,
                                                'id' => $pageCat['id'],
                                                'link_rewrite' => $pageCat['link_rewrite']
                                            )).'">'.$pageCat['name'].'</a></li>';
                                    }
                                    $html.= '</ul></div>';
                                }
                            }
                            //On ajoute les liens directs en dernier
                            $html.= $htmlLink;
                            //On ferme la liste
                            $html.= '</ul></div></li>';
                            //$html.= '</ul></li>';
                            if($key !== $lastKey)
                                $html.= '<li class="sep"></li>';
                        }
                    }elseif($row['TopMenu']['contenu_type'] === 'link'){
                        //On va chercher les infos du lien
                        $dataLink = $this->MenuLink->find('first', array(
                            'fields' => array('MenuLink.target_blank', 'MenuLinkLang.title', 'MenuLinkLang.link'),
                            'conditions' => array('MenuLink.id' => $row['TopMenu']['contenu_id']),
                            'joins' => array(
                                array(
                                    'table' => 'menu_link_langs',
                                    'alias' => 'MenuLinkLang',
                                    'type' => 'inner',
                                    'conditions' => array(
                                        'MenuLinkLang.menu_link_id = MenuLink.id',
                                        'MenuLinkLang.lang_id = '.$lang_id
                                    )
                                )
                            ),
                            'recursive' => -1
                        ));

                        if(!empty($dataLink)){
                            $html.= '<li><a class="parent_title" href="'.$dataLink['MenuLinkLang']['link'].'"'.($dataLink['MenuLink']['target_blank'] == 0 ?'':' target="_blank"').'>'.$dataLink['MenuLinkLang']['title'].'</a></li>';
                            if($key !== $lastKey)
                                $html.= '<li class="sep"></li>';
                        }
                    }
                }
                //On ferme la liste
                $html.= '</ul>';

                //On supprime l'ancien cache
                Cache::delete('menu-navigation-'.$language, Configure::read('nomCacheNavigation'));
                //On crée le cache
                Cache::write('menu-navigation-'.$language, $html, Configure::read('nomCacheNavigation'));
            }
        }
    }