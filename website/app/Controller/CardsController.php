<?php
App::uses('AppController', 'Controller');

/**
 * Controller for the card admin views and regular user card views.
 */
class CardsController extends AppController
{
    public $components = array('Paginator');
    public $helpers = array('Paginator', 'Language');

    /**
     * Initialization for the controller
     */
    public function beforeFilter()
    {
        if ($this->request->is('ajax')) {
            $this->layout = 'ajax';
            $this->set('isAjax',1);
        }

        parent::beforeFilter();

        $this->Auth->allow('process_selection','process_subscribe_email');
    }

    public function admin_activate($id)
    {
        $this->Card->id = $id;
        if ($this->Card->saveField('active', 1)) {
            $this->Session->setFlash(__('Le jeu est activé ! Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', $this->getMenuRegenerationsFlashLink());
        } else {
            $this->Session->setFlash(__('Erreur lors de l\'activation.'), 'flash_warning');
        }
        $this->redirect(array('controller' => 'cards', 'action' => 'list', 'admin' => true), false);
    }

    public function admin_create($post = true, $id = null)
    {
        // load models
        $this->loadModel('Card');
        $this->loadModel('CardLang');
        $this->loadModel('Lang');

        //
        $langs = $this->Lang->getAllLangs();
        $card = null;
        if ($id) {
            $card = $this->Card->find('first', ['conditions' => ['card_id' => $id]]);
        }

        if ((!$this->request->is('post') || !$post) && empty($this->request->data['Card'])) {
            if ($card) {
                $this->request->data['Card'] = $card['Card'];
                foreach ($card['CardLang'] as $e_lang) {
                    $this->request->data['CardLang']['i' . $e_lang['lang_id']] = $e_lang;
                }
            }
        } else {
            $requestData = $this->request->data;

            //
            foreach ($requestData['CardLang'] as $lang_key => &$e_lang) {
                if (!array_filter($e_lang)) {
                    $e_lang = null;
                    continue;
                }
                $lang_key = (int) substr($lang_key, 1);
                $e_lang['lang_id'] = $lang_key;

                $e_lang['description'] = Tools::clearUrlImage($e_lang['description']);
            } unset($e_lang);
            $requestData['CardLang'] = array_filter($requestData['CardLang']);

            if (!$requestData['CardLang']) {
                $this->Session->setFlash(__('Une erreur est survenue, les données soumises sont invalides ou incomplétes.'), 'flash_warning');
                $this->admin_create(false);
                return;
            }

            // check uploaded images
            @mkdir(ROOT . '/' . Configure::read('Site.cardImages'), 0777, true);
            $image_field_suff = 'image';
            $p = 0;
            foreach ($requestData['Card'] as $k => &$v) {
                if (strpos($k, $image_field_suff) !== strlen($k) - strlen($image_field_suff)) {
                    continue;
                }

                $file = $v;
                $v = $card ? $card['Card'][$k] : '';
                if ($this->isUploadedFile($file)) {
                    $imgInfo = getimagesize($file['tmp_name']);
                    if (in_array($imgInfo['mime'], array('image/png', 'image/jpeg', 'image/pjpeg'))) {
                        $filename = $k . '.' . strtolower(substr($imgInfo['mime'], 6));
                        while (file_exists(ROOT . '/' . Configure::read('Site.cardImages') . DS . $p . '-' . $filename)) {
                            $p = (int) $p + 1;
                        }
                        if (move_uploaded_file($file['tmp_name'], ROOT . '/' . Configure::read('Site.cardImages') . DS . $p . '-' . $filename)) {
                            if ($v && file_exists(ROOT . '/' . Configure::read('Site.cardImages') . DS . $v)) {
                                // delete old file
                                @unlink(ROOT . '/' . Configure::read('Site.cardImages') . DS . $v);
                            }
                            $v =  $p . '-' . $filename;
                        }
                    }
                }
            } unset($k, $v);

            // actual save
            if ($card) {
                $this->Card->id = (int) $card['Card']['card_id'];
            }
            $this->Card->save($requestData['Card']);

            // save langs
            foreach ($requestData['CardLang'] as &$e_lang) {
                $e_lang['card_id'] = $this->Card->id;

            } unset($e_lang);
            if ($card) {
                $this->Card->CardLang->deleteAll(array('CardLang.card_id' => $card['Card']['card_id']), false);
            }
            $this->Card->CardLang->saveMany($requestData['CardLang']);

            //
            $this->Session->setFlash(__('Le jeu a bien été enregistré ! Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', $this->getMenuRegenerationsFlashLink());
            $this->redirect(array('controller' => 'cards', 'action' => 'list', 'admin' => true), false);
            return;
        }

        if (!$card && empty($this->request->data)) {
            $default_lang_id = reset($langs);
            $default_lang_id = (int) $default_lang_id['Lang']['id_lang'];
            $this->request->data = $this->getDefaultCardData($default_lang_id);
        }

        $card_game_types = $this->Card->getGameTypes();
        $card_display_modes = $this->Card->getDisplayModes();
        $this->set(compact('card', 'langs', 'card_game_types', 'card_display_modes'));

        $this->render('admin_create');
    }

    public function admin_create_item($card_id, $post = true, $card_item_id = null)
    {
        // load models
        $this->loadModel('Card');
        $this->loadModel('CardItem');
        $this->loadModel('CardItemLang');
        $this->loadModel('Lang');

        //
        $langs = $this->Lang->getAllLangs();
        $card = null;
        if ($card_id) {
            $card = $this->Card->find('first', ['conditions' => ['card_id' => $card_id]]);
        }
        $card_item = null;
        if ($card_item_id) {
            $card_item = $this->CardItem->find('first', ['conditions' => ['card_item_id' => $card_item_id]]);
            if ($card && $card_item['CardItem']['card_id'] !== $card['Card']['card_id']) {
                $this->Session->setFlash(__('Une erreur est survenue, les données soumises sont incohérentes.'), 'flash_warning');
                $this->redirect(array('controller' => 'cards', 'action' => 'list', 'admin' => true), false);
                return;
            }
        }
        if (!$card && $card_item) {
            $card = $this->Card->find('first', ['conditions' => ['card_id' => $card_item['CardItem']['card_id']]]);
        }
        if (!$card) {
            $this->Session->setFlash(__('Une erreur est survenue, la page n\'a pas été trouvée.'), 'flash_warning');
            $this->redirect(array('controller' => 'cards', 'action' => 'list', 'admin' => true), false);
            return;
        }

        if ((!$this->request->is('post') || !$post) && empty($this->request->data['CardItem'])) {
            if ($card_item) {
                $this->request->data['CardItem'] = $card_item['CardItem'];
                foreach ($card_item['CardItemLang'] as $e_lang) {
                    $this->request->data['CardItemLang']['i' . $e_lang['lang_id']] = $e_lang;
                }
            }
        } else {
            $requestData = $this->request->data;

            //
            foreach ($requestData['CardItemLang'] as $lang_key => &$e_lang) {
                if (!array_filter($e_lang)) {
                    $e_lang = null;
                    continue;
                }
                $lang_key = (int) substr($lang_key, 1);
                $e_lang['lang_id'] = $lang_key;

                $e_lang['description'] = Tools::clearUrlImage($e_lang['description']);
            } unset($e_lang);
            $requestData['CardItemLang'] = array_filter($requestData['CardItemLang']);

            if (!$requestData['CardItemLang']) {
                $this->Session->setFlash(__('Une erreur est survenue, les données soumises sont invalides ou incomplétes.'), 'flash_warning');
                $this->admin_create(false);
                return;
            }

            // check uploaded images
            @mkdir(ROOT . '/' . Configure::read('Site.cardItemImages'), 0777, true);
            $image_field_suff = 'image';
            $p = 0;
            foreach ($requestData['CardItem'] as $k => &$v) {
                if (strpos($k, $image_field_suff) !== strlen($k) - strlen($image_field_suff)) {
                    continue;
                }

                $file = $v;
                $v = $card_item ? $card_item['CardItem'][$k] : '';
                if ($this->isUploadedFile($file)) {
                    $imgInfo = getimagesize($file['tmp_name']);
                    if (in_array($imgInfo['mime'], array('image/png','image/jpeg', 'image/pjpeg'))) {
                        $filename = $k . '.' . strtolower(substr($imgInfo['mime'], 6));
                        while (file_exists(ROOT . '/' . Configure::read('Site.cardItemImages') . DS . $p . '-' . $filename)) {
                            $p = (int) $p + 1;
                        }
                        if (move_uploaded_file($file['tmp_name'], ROOT . '/' . Configure::read('Site.cardItemImages') . DS . $p . '-' . $filename)) {
                            if ($v && file_exists(ROOT . '/' . Configure::read('Site.cardItemImages') . DS . $v)) {
                                // delete old file
                                @unlink(ROOT . '/' . Configure::read('Site.cardItemImages') . DS . $v);
                            }
                            $v =  $p . '-' . $filename;
                        }
                    }
                }
            } unset($k, $v);

            // actual save
            if ($card_item) {
                $this->CardItem->id = (int) $card_item['CardItem']['card_item_id'];
            } else {
                $requestData['CardItem']['card_id'] = $card['Card']['card_id'];
            }
            $this->CardItem->save($requestData['CardItem']);

            // save langs
            foreach ($requestData['CardItemLang'] as &$e_lang) {
                $e_lang['card_item_id'] = $this->CardItem->id;
            } unset($e_lang);
            if ($card) {
                $this->CardItem->CardItemLang->deleteAll(array('CardItemLang.card_item_id' => $card_item['CardItem']['card_item_id']), false);
            }
            $this->CardItem->CardItemLang->saveMany($requestData['CardItemLang']);

            //
            $this->Session->setFlash(__('La carte a bien été enregistrée !'), 'flash_success');
            $this->redirect(array('controller' => 'cards', 'action' => 'list_items', 'id' => $card['Card']['card_id'], 'admin' => true), false);
            return;
        }

        if (!$card_item && empty($this->request->data)) {
            $default_lang_id = reset($langs);
            $default_lang_id = (int) $default_lang_id['Lang']['id_lang'];
            $this->request->data = $this->getDefaultCardItemData($default_lang_id);
        }

        $this->set(compact('card', 'card_item', 'langs'));

        $this->render('admin_create_item');
    }

    public function admin_create_items_from_zip($card_id, $post = true, $card_item_id = null)
    {
        // load models
        $this->loadModel('Card');
        $this->loadModel('CardItem');
        $this->loadModel('CardItemLang');
        $this->loadModel('Lang');

        $card = null;
        if ($card_id) {
            $card = $this->Card->find('first', ['conditions' => ['card_id' => $card_id]]);
        }
        if (!$card) {
            $this->Session->setFlash(__('Une erreur est survenue, la page n\'a pas été trouvée.'), 'flash_warning');
            $this->redirect(array('controller' => 'cards', 'action' => 'list', 'admin' => true), false);
            return;
        }

        if (((!$this->request->is('post') || !$post) && empty($this->request->data['File']['zip'])) ||
            empty($this->request->data['File']['zip']['type']) || !$this->isUploadedFile($this->request->data['File']['zip']) ||
            $this->request->data['File']['zip']['type'] !== 'application/zip') {
            $this->Session->setFlash(__('Vous devez joindre un fichier zip valide.'), 'flash_warning');
            $this->redirect(array('controller' => 'cards', 'action' => 'list_items', 'id' => $card['Card']['card_id'], 'admin' => true), false);
            return;
        }

        $file = $this->request->data['File']['zip'];

        set_time_limit(360);

        @mkdir(ROOT . '/' . Configure::read('Site.cardItemImages'), 0777, true);

        $zip = new ZipArchive;
        $res = @$zip->open($file['tmp_name']);
        $p = 0;
        do {
            $tmpfname = ROOT . '/' . Configure::read('Site.cardItemImages') . '/ZIP-CARDS-' . $p++ . '/';
        } while (file_exists($tmpfname));
        try {
            $res = $res === true && @$zip->extractTo($tmpfname);
            $zip->close();
            if (!$res) {
                $this->Session->setFlash(__('Le fichier zip joint ne peut être ouvert.'), 'flash_warning');
                $this->redirect(array('controller' => 'cards', 'action' => 'list_items', 'id' => $card['Card']['card_id'], 'admin' => true), false);
                return;
            }
            $files = [$tmpfname];
            do {
                $changed = [];
                foreach ($files as &$f) {
                    if (is_dir($f)) {
                        $changed = array_merge($changed, glob($f . '/*', GLOB_MARK));
                        $f = false;
                    }
                } unset($f);
                $files = array_unique(array_filter(array_merge($files, $changed)));
            } while ($changed);

            $langs = $this->Lang->getAllLangs();
            $default_lang_id = reset($langs);
            $default_lang_id = (int) $default_lang_id['Lang']['id_lang'];

            $count = 0;
            $p = 0;
            foreach ($files as $f) {
                if (!is_file($f)) {
                    continue;
                }
                $imgInfo = getimagesize($f);
                if (!in_array($imgInfo['mime'], array('image/png','image/jpeg', 'image/pjpeg'))) {
                    continue;
                }
                $filename = 'image.' . strtolower(substr($imgInfo['mime'], 6));
                while (file_exists(ROOT . '/' . Configure::read('Site.cardItemImages') . DS . $p . '-' . $filename)) {
                    $p = (int) $p + 1;
                }
                $filename = $p . '-' . $filename;
                if (!@rename($f, ROOT . '/' . Configure::read('Site.cardItemImages') . DS . $filename)) {
                    continue;
                }
                $this->CardItem->id = 0;
                $this->CardItem->save([
                    'card_id' => $card['Card']['card_id'],
                    'image' => $filename
                ]);
                $title = basename($f);
                $title = substr($title, 0, strpos($title, '.'));
                $title = preg_replace('/^[^a-z]+/i', '', $title);
                if (!$title) {
                    $title = __('Carte') . ' ' . ($count + 1);
                }
                $this->CardItemLang->save([
                    'card_item_id' => $this->CardItem->id,
                    'lang_id' => $default_lang_id,
                    'title' => $title
                ]);
                $count++;
            }
        } finally {
            $this->delete_files($tmpfname);
        }

        $this->Session->setFlash(__('%d cartes ont été ajoutées.', [$count]), 'flash_success');
        $this->redirect(array('controller' => 'cards', 'action' => 'list_items', 'id' => $card['Card']['card_id'], 'admin' => true), false);
        return;
    }

    public function admin_create_result($card_id, $post = true, $card_result_id = null)
    {
        // load models
        $this->loadModel('Card');
        $this->loadModel('CardResult');
        $this->loadModel('CardResultLang');
        $this->loadModel('Lang');

        //
        $langs = $this->Lang->getAllLangs();
        $card = null;
        if ($card_id) {
            $card = $this->Card->find('first', ['conditions' => ['card_id' => $card_id]]);
        }
        $card_result = null;
        if ($card_result_id) {
            $card_result = $this->CardResult->find('first', ['conditions' => ['card_result_id' => $card_result_id]]);
            if ($card && $card_result['CardResult']['card_id'] !== $card['Card']['card_id']) {
                $this->Session->setFlash(__('Une erreur est survenue, les données soumises sont incohérentes.'), 'flash_warning');
                $this->redirect(array('controller' => 'cards', 'action' => 'list', 'admin' => true), false);
                return;
            }
        }
        if (!$card && $card_result) {
            $card = $this->Card->find('first', ['conditions' => ['card_id' => $card_result['CardResult']['card_id']]]);
        }
        if (!$card) {
            $this->Session->setFlash(__('Une erreur est survenue, la page n\'a pas été trouvée.'), 'flash_warning');
            $this->redirect(array('controller' => 'cards', 'action' => 'list', 'admin' => true), false);
            return;
        }

        if ((!$this->request->is('post') || !$post) && empty($this->request->data['CardResultLang'])) {
            if ($card_result) {
                $this->request->data['CardResult'] = $card_result['CardResult'];
                foreach ($card_result['CardResultLang'] as $e_lang) {
                    $this->request->data['CardResultLang']['i' . $e_lang['lang_id']] = $e_lang;
                }
            }
        } else {
            $requestData = $this->request->data;

            //
            foreach ($requestData['CardResultLang'] as $lang_key => &$e_lang) {
                if (!array_filter($e_lang)) {
                    $e_lang = null;
                    continue;
                }
                $lang_key = (int) substr($lang_key, 1);
                $e_lang['lang_id'] = $lang_key;

                $e_lang['description'] = Tools::clearUrlImage($e_lang['description']);
            } unset($e_lang);
            $requestData['CardResultLang'] = array_filter($requestData['CardResultLang']);

            if (!$requestData['CardResultLang']) {
                $this->Session->setFlash(__('Une erreur est survenue, les données soumises sont invalides ou incomplétes.'), 'flash_warning');
                $this->admin_create(false);
                return;
            }

            // actual save
            if ($card_result) {
                $this->CardResult->id = (int) $card_result['CardResult']['card_result_id'];
            } else {
                $requestData['CardResult']['card_id'] = $card['Card']['card_id'];
            }
            $this->CardResult->save(isset($requestData['CardResult']) ? $requestData['CardResult'] : []);

            // save langs
            foreach ($requestData['CardResultLang'] as &$e_lang) {
                $e_lang['card_result_id'] = $this->CardResult->id;
            } unset($e_lang);
            if ($card) {
                $this->CardResult->CardResultLang->deleteAll(array('CardResultLang.card_result_id' => $card_result['CardResult']['card_result_id']), false);
            }
            $this->CardResult->CardResultLang->saveMany($requestData['CardResultLang']);

            //
            $this->Session->setFlash(__('La carte a bien été enregistrée !'), 'flash_success');
            $this->redirect(array('controller' => 'cards', 'action' => 'list_results', 'id' => $card['Card']['card_id'], 'admin' => true), false);
            return;
        }

        if (!$card_result && empty($this->request->data)) {
            $default_lang_id = reset($langs);
            $default_lang_id = (int) $default_lang_id['Lang']['id_lang'];
            $this->request->data = $this->getDefaultCardResultData($default_lang_id);
        }

        $card_result_types = $this->CardResult->getResultTypes();
        $this->set(compact('card', 'card_result', 'langs', 'card_result_types'));

        $this->render('admin_create_result');
    }

    public function admin_edit($id, $post = true)
    {
        $this->admin_create($post, $id);
    }

    public function admin_edit_item($id, $post = true)
    {
        $this->admin_create_item(null, $post, $id);
    }

    public function admin_edit_result($id, $post = true)
    {
        $this->admin_create_result(null, $post, $id);
    }

    public function admin_deactivate($id)
    {
        $this->Card->id = $id;
        if ($this->Card->saveField('active', 0)){
            $this->Session->setFlash(__('Le jeu est désactivé ! Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', $this->getMenuRegenerationsFlashLink());
        } else {
            $this->Session->setFlash(__('Erreur lors de la désactivation !'), 'flash_warning');
        }
        $this->redirect(array('controller' => 'cards', 'action' => 'list', 'admin' => true), false);
    }

    public function admin_list()
    {
        // load models
        $this->loadModel('Card');
        $this->loadModel('CardLang');
        $this->loadModel('CardItem');
        $this->loadModel('CardResult');
        $this->loadModel('Lang');

        // search
        $conditions = array();
        if (isset($this->request->data['Search']['search'])) {
            if (isset($this->request->query['page'])) {
                unset($this->request->query['page']);
            }
            if (isset($this->request->data['Search']['title'])) {
                $conditions = array(
                    'OR' => array(
                        'CardLang.meta_title LIKE' => '%'. $this->request->data['Search']['title'] .'%',
                        'CardLang.title LIKE' => '%'. $this->request->data['Search']['title'] .'%'
                    )
                );
            }
        }

        $this->Paginator->settings = array(
            'fields' => array('Card.*', 'CardLang.*', 'Lang.name', 'Lang.language_code', 'GROUP_CONCAT(Lang.name) AS langs'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'card_langs',
                    'alias' => 'CardLang',
                    'type' => 'right',
                    'conditions' => array('Card.card_id = CardLang.card_id')
                ),
                array(
                    'table' => 'langs',
                    'alias' => 'Lang',
                    'type' => 'left',
                    'conditions' => array('Lang.id_lang = CardLang.lang_id')
                )
            ),
            'recursive' => -1,
            'group' => 'Card.card_id',
            'order' => 'CardLang.meta_title asc',
            'paramType' => 'querystring',
            'limit' => 5
        );

        $tmp_pages = $this->Paginator->paginate($this->Card);
        $cards = array();
        foreach ($tmp_pages as $card) {
            $pageTransit = end($cards);
            if ($pageTransit !== false) {
                if ($pageTransit['card_id'] == $card['Card']['card_id']) {
                    $keys = array_keys($cards);
                    $lastKey = end($keys);
                    $cards[$lastKey]['lang_title'].= ', '.$card['Lang']['title'];
                    continue;
                }
            }
            $cards[] = array(
                'card_items_count' => $this->CardItem->find('count', [
                    'conditions' => array('card_id' => $card['Card']['card_id'])
                ]),
                'card_results_count' => $this->CardResult->find('count', [
                    'conditions' => array('card_id' => $card['Card']['card_id'])
                ]),
                'langs'         => str_replace(',', ', ', isset($card[0]['langs']) ? $card[0]['langs'] : ''),
                'card_id'       => $card['Card']['card_id'],
                'title'         => $card['CardLang']['title'],
                'active'        => $card['Card']['active'],
                'lang_title'    => $card['Lang']['name'],
                'language_code' => $card['Lang']['language_code'],
                'url_path'      => $card['CardLang']['url_path']
            );
        }

        $langs = $this->Lang->getLang(true);
        $this->set(compact('cards', 'langs'));
    }

    public function admin_list_items($card_id)
    {
        // load models
        $this->loadModel('Card');
        $this->loadModel('CardLang');
        $this->loadModel('CardItem');
        $this->loadModel('CardItemLang');
        $this->loadModel('Lang');

        //
        $card = null;
        if ($card_id) {
            $card = $this->Card->find('first', ['conditions' => ['card_id' => $card_id]]);
        }
        if (!$card) {
            $this->Session->setFlash(__('Une erreur est survenue, la page n\'a pas été trouvée.'), 'flash_warning');
            $this->redirect(array('controller' => 'cards', 'action' => 'list', 'admin' => true), false);
            return;
        }

        // search
        $conditions = array();
        if (isset($this->request->data['Search']['search'])) {
            if (isset($this->request->query['page'])) {
                unset($this->request->query['page']);
            }
            if (isset($this->request->data['Search']['title'])) {
                $conditions = array(
                    'OR' => array(
                        'CardItemLang.title LIKE' => '%'. $this->request->data['Search']['title'] .'%'
                    )
                );
            }
        }

        $conditions['CardItem.card_id'] = $card_id;

        $this->Paginator->settings = array(
            'fields' => array('CardItem.*', 'CardItemLang.*', 'Lang.name', 'Lang.language_code', 'GROUP_CONCAT(Lang.name) AS langs'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'card_item_langs',
                    'alias' => 'CardItemLang',
                    'type' => 'right',
                    'conditions' => array('CardItem.card_item_id = CardItemLang.card_item_id')
                ),
                array(
                    'table' => 'langs',
                    'alias' => 'Lang',
                    'type' => 'left',
                    'conditions' => array('Lang.id_lang = CardItemLang.lang_id')
                )
            ),
            'recursive' => -1,
            'group' => 'CardItem.card_item_id',
            'order' => 'CardItemLang.title asc',
            'paramType' => 'querystring',
            'limit' => 10
        );

        $tmp_pages = $this->Paginator->paginate($this->CardItem);
        $card_items = array();
        foreach ($tmp_pages as $card_item) {
            $pageTransit = end($card_items);
            if ($pageTransit !== false) {
                if ($pageTransit['card_item_id'] == $card_item['CardItem']['card_item_id']) {
                    $keys = array_keys($card_items);
                    $lastKey = end($keys);
                    $card_items[$lastKey]['lang_title'].= ', '.$card_item['Lang']['title'];
                    continue;
                }
            }
            $card_items[] = array(
                'card_items_count' => $this->CardItem->find('count', [
                    'conditions' => array('card_item_id' => $card_item['CardItem']['card_item_id'])
                ]),
                'langs'         => str_replace(',', ', ', isset($card_item[0]['langs']) ? $card_item[0]['langs'] : ''),
                'card_item_id'  => $card_item['CardItem']['card_item_id'],
                'title'         => $card_item['CardItemLang']['title'],
                'keywords'      => $card_item['CardItemLang']['keywords'],
                'lang_title'    => $card_item['Lang']['name'],
                'language_code' => $card_item['Lang']['language_code'],
            );
        }

        $langs = $this->Lang->getLang(true);
        $this->set(compact('card', 'card_items', 'langs'));
    }

    public function admin_list_results($card_id)
    {
        // load models
        $this->loadModel('Card');
        $this->loadModel('CardLang');
        $this->loadModel('CardResult');
        $this->loadModel('CardResultLang');
        $this->loadModel('Lang');

        //
        $card = null;
        if ($card_id) {
            $card = $this->Card->find('first', ['conditions' => ['card_id' => $card_id]]);
        }
        if (!$card) {
            $this->Session->setFlash(__('Une erreur est survenue, la page n\'a pas été trouvée.'), 'flash_warning');
            $this->redirect(array('controller' => 'cards', 'action' => 'list', 'admin' => true), false);
            return;
        }

        // search
        $conditions = array();
        if (isset($this->request->data['Search']['search'])) {
            if (isset($this->request->query['page'])) {
                unset($this->request->query['page']);
            }
            if (isset($this->request->data['Search']['title'])) {
                $conditions = array(
                    'OR' => array(
                        'CardResultLang.title LIKE' => '%'. $this->request->data['Search']['title'] .'%'
                    )
                );
            }
        }

        $conditions['CardResult.card_id'] = $card_id;

        $this->Paginator->settings = array(
            'fields' => array('CardResult.*', 'CardResultLang.*', 'Lang.name', 'Lang.language_code', 'GROUP_CONCAT(Lang.name) AS langs'),
            'conditions' => $conditions,
            'joins' => array(
                array(
                    'table' => 'card_result_langs',
                    'alias' => 'CardResultLang',
                    'type' => 'right',
                    'conditions' => array('CardResult.card_result_id = CardResultLang.card_result_id')
                ),
                array(
                    'table' => 'langs',
                    'alias' => 'Lang',
                    'type' => 'left',
                    'conditions' => array('Lang.id_lang = CardResultLang.lang_id')
                )
            ),
            'recursive' => -1,
            'group' => 'CardResult.card_result_id',
            'order' => 'CardResultLang.title asc',
            'paramType' => 'querystring',
            'limit' => 10
        );

        $tmp_pages = $this->Paginator->paginate($this->CardResult);
        $card_results = array();
        foreach ($tmp_pages as $card_result) {
            $pageTransit = end($card_results);
            if ($pageTransit !== false) {
                if ($pageTransit['card_result_id'] == $card_result['CardResult']['card_result_id']) {
                    $keys = array_keys($card_results);
                    $lastKey = end($keys);
                    $card_results[$lastKey]['lang_title'].= ', '.$card_result['Lang']['title'];
                    continue;
                }
            }
            $card_results[] = array(
                'card_results_count' => $this->CardResult->find('count', [
                    'conditions' => array('card_result_id' => $card_result['CardResult']['card_result_id'])
                ]),
                'langs'         => str_replace(',', ', ', isset($card_result[0]['langs']) ? $card_result[0]['langs'] : ''),
                'card_result_id'  => $card_result['CardResult']['card_result_id'],
                'title'         => $card_result['CardResultLang']['title'],
                'lang_title'    => $card_result['Lang']['name'],
                'language_code' => $card_result['Lang']['language_code'],
            );
        }

        $langs = $this->Lang->getLang(true);
        $this->set(compact('card', 'card_results', 'langs'));
    }

    public function admin_true_delete($id)
    {
        // load models
        $this->loadModel('Card');
        $this->loadModel('CardLang');
        $this->loadModel('CardItem');
        $this->loadModel('CardItemLang');

        if ($this->Card->deleteAll(array('Card.card_id' => $id), true, true)) {
            $this->Session->setFlash(__('Le jeu a été supprimé ! Si votre modification affecte le menu pensez à le regénérer'), 'flash_success', $this->getMenuRegenerationsFlashLink());
        } else {
            $this->Session->setFlash(__('Erreur lors de la suppression !'), 'flash_warning');
        }
        $this->redirect(array('controller' => 'cards', 'action' => 'list', 'admin' => true), false);
    }

    public function admin_true_delete_item($id)
    {
        // load models
        $this->loadModel('CardItem');
        $this->loadModel('CardItemLang');

        $card_item = null;
        $card_item = $this->CardItem->find('first', ['conditions' => ['card_item_id' => $id]]);

        if ($this->CardItem->deleteAll(array('CardItem.card_item_id' => $id), true, true)) {
            $this->Session->setFlash(__('La carte a été supprimée !'), 'flash_success');
        } else {
            $this->Session->setFlash(__('Erreur lors de la suppression !'), 'flash_warning');
        }
        if (!$card_item) {
            $this->redirect(array('controller' => 'cards', 'action' => 'list', 'admin' => true), false);
        } else {
            $this->redirect(array('controller' => 'cards', 'action' => 'list_items', 'id' => $card_item['CardItem']['card_id'], 'admin' => true), false);
        }
    }

    public function admin_true_delete_result($id)
    {
        // load models
        $this->loadModel('CardResult');
        $this->loadModel('CardResultLang');

        $card_result = null;
        $card_result = $this->CardResult->find('first', ['conditions' => ['card_result_id' => $id]]);

        if ($this->CardResult->deleteAll(array('CardResult.card_result_id' => $id), true, true)) {
            $this->Session->setFlash(__('Le texte résultat a été supprimée !'), 'flash_success');
        } else {
            $this->Session->setFlash(__('Erreur lors de la suppression !'), 'flash_warning');
        }
        if (!$card_result) {
            $this->redirect(array('controller' => 'cards', 'action' => 'list', 'admin' => true), false);
        } else {
            $this->redirect(array('controller' => 'cards', 'action' => 'list_results', 'id' => $card_result['CardResult']['card_id'], 'admin' => true), false);
        }
    }


    /**
     * php delete function that deals with directories recursively
     */
    private function delete_files($target)
    {
        $target = rtrim($target, '/');
        if (is_dir($target)) {
            $files = glob($target . '/*', GLOB_MARK);
            foreach($files as $file){
                $this->delete_files($file);
            }
            @rmdir($target);
        } else if (is_file($target)) {
            @unlink($target);
        }
    }

    public function display($url_path = '')
    {
        // load models
        $this->loadModel('Card');
        $this->loadModel('CardLang');
        $this->loadModel('CardItem');
        $this->loadModel('CardItemLang');
        $this->loadModel('Lang');

        $card = $this->CardLang->find('first', [
            'conditions' => array('url_path' => $url_path, 'lang_id' => $this->Session->read('Config.id_lang')),
            'recursive' => 1,
        ]);

        // if not found go back to home page
        if (empty($url_path) || empty($card)) {
            $this->return404(false);
            $this->Session->setFlash(__('Cette page n\'existe pas.'), 'flash_warning');
            $this->redirect(array('controller' => 'home', 'action' => 'index', 'admin' => false));
            return;
        }

        $this->site_vars['meta_title']       = $card['CardLang']['meta_title'];
        $this->site_vars['meta_keywords']    = $card['CardLang']['meta_keywords'];
        $this->site_vars['meta_description'] = $card['CardLang']['meta_description'];

        // get card items
        $cardItems = $this->CardItemLang->find('all', array(
            'conditions' => array('CardItem.card_id' => $card['CardLang']['card_id'], 'CardItemLang.lang_id' => $card['CardLang']['lang_id']),
            'recursive' => 1,
        ));

        if (empty($cardItems)) {
            $this->Session->setFlash(__('Pas de cartes trouvées dans ce jeu '), 'flash_warning');
            $this->redirect(array('controller' => 'home', 'action' => 'index', 'admin' => false));
            return;
        }

        $this->set(compact('card', 'cardItems'));
    }

    private function getDefaultCardData($default_lang_id) {
        return [
            'Card' => [
                'active' => 1,
                'item_bg_color' => 'transparent',
                'step_choose_bg_color' => 'transparent',
                'step_interpretation_bg_color' => 'transparent',
                'step_result_bg_color' => 'transparent',
                'embed_image_text_color' => '#61439d',
            ],
            'CardLang' => [
                'i' . $default_lang_id => [
                    'step_choose_title' => 'Sélectionnez vos cartes',
                    'step_choose_lines' =>
                               'Choisissez votre première carte'  .
                        "\n" . 'Choisissez votre deuxième carte'  .
                        "\n" . 'Choisissez votre troisième carte' .
                        "\n" . 'Choisissez votre quatrième carte' .
                        "\n" . 'Choisissez votre cinquième carte' .
                    '',
                    'step_interpretation_title' => 'Croisement des informations',
                    'step_result_title' => 'Interprétation',
                ]
            ]
        ];
    }

    private function getDefaultCardItemData($default_lang_id) {
        return [
            'CardItem' => [
            ],
            'CardItemLang' => [
            ]
        ];
    }

    private function getDefaultCardResultData($default_lang_id) {
        return [
            'CardResult' => [
            ],
            'CardResultLang' => [
            ]
        ];
    }

    private function getMenuRegenerationsFlashLink() {
        return array('link' => Router::url(array('controller' => 'menus', 'action' => 'killroutes', 'admin' => true), true), 'messageLink' => __('ICI'));
    }

    public function process_selection($post = true) {
        $this->layout = 'ajax';
        $this->render(false);

        // load models
        $this->loadModel('Card');
        $this->loadModel('CardLang');
        $this->loadModel('CardItem');
        $this->loadModel('CardItemLang');
        $this->loadModel('CardResult');
        $this->loadModel('CardResultLang');
        $this->loadModel('Lang');
        $this->loadModel('Agent');

        $this->loadModel('CategoryLang');
        $this->loadModel('CategoryUser');

        //
        App::uses('FrontblockHelper', 'View/Helper');
        $fbH = new FrontblockHelper(new View());

        //
        if ((!$this->request->is('post') || !$post) || empty($this->request->data['card_id']) || empty($this->request->data['card_item_ids']) || empty($this->request->data['lang_id'])) {
            echo json_encode(['error' => 'Invalid parameters']);
            return false;
        }

        //
        $card = $this->Card->find('first', ['conditions' => ['Card.card_id' => $this->request->data['card_id']]]);
        if (!$card) {
            echo json_encode(['error' => 'Invalid parameters (card_id)']);
            return false;
        }
        $card['CardLang'] = array_filter($card['CardLang'], function($v) {
            return $v['lang_id'] == $this->request->data['lang_id'];
        });
        $card['CardLang'] = $card['CardLang'] ? reset($card['CardLang']) : null;

        //
        if (is_array($this->request->data['card_item_ids'])) {
            $this->request->data['card_item_ids'] = array_map(function($v) {
                return (int) $v;
            }, $this->request->data['card_item_ids']);
        }
        $card_items = $this->CardItem->find('all', [
            'conditions' => ['CardItem.card_item_id' => $this->request->data['card_item_ids']],
            'order' => array('CardItem.card_item_id'),
        ]);
        if (!$card_items) {
            echo json_encode(['error' => 'Invalid parameters (card_item_ids)']);
            return false;
        }
        foreach ($card_items as &$card_item) {
            $card_item['CardItemLang'] = array_filter($card_item['CardItemLang'], function($v) {
                return $v['lang_id'] == $this->request->data['lang_id'];
            });
            $card_item['CardItemLang'] = $card_item['CardItemLang'] ? reset($card_item['CardItemLang']) : null;
        } unset($card_item);

        //
        $a = $this->request->data['card_item_ids'];
        sort($a);
        mt_srand(crc32(sha1($this->request->data['card_id'] . '-' . implode(',', $this->request->data['card_item_ids']))));

        //
        $result = [];
		$result['card_id'] = $this->request->data['card_id'];
        $result['selected_keywords'] = [];
        $result['card_item_answer'] = [];
        $answer_yes_count = 0;

        // compute answer and keywords
        foreach ($card_items as $card_item) {
            for ($index = 0; $index <= count($this->request->data['card_item_ids']); ++$index) {
                if ($this->request->data['card_item_ids'][$index] == $card_item['CardItem']['card_item_id']) {
                    break;
                }
            }

            $keyword = '';
            $keywords = !empty($card_item['CardItemLang']['keywords']) ? $card_item['CardItemLang']['keywords'] : false;
            if ($keywords) {
                $keywords = explode(',', $keywords);
                $id = mt_rand(0, count($keywords) - 1);
                $keyword = $keywords[$id];
            }
            $result['selected_keywords'][$index] = ucfirst(trim($keyword));

            $a = mt_rand(0, 1);
            $answer_yes_count+= $a;
            $result['card_item_answer'][$index] = $a === 1;
        }
        ksort($result['card_item_answer']);
        $result['card_item_answer'] = array_values($result['card_item_answer']);
        $result['answer'] = 2 * $answer_yes_count > count($card_items);

        // we now the answer to give, we can select result text
        $result_db_conditions = array(
            'CardResult.card_id' => $this->request->data['card_id'],
            'CardResultLang.lang_id' => $this->request->data['lang_id']
        );

        if ($card['Card']['game_type'] == Card::GAME_TYPE_YES_NO) {
            $result_db_conditions['CardResult.type'] = $result['answer'] ? CardResult::RESULT_TYPE_YES : CardResult::RESULT_TYPE_NO;
        }

        $texts_count = $this->CardResultLang->find('count', array(
            'conditions' => $result_db_conditions,
            'recursive' => 1,
        ));

        $result['text'] = $texts_count ? $this->CardResultLang->find('first', array(
            'conditions' => $result_db_conditions,
            'limit' => 1,
            'offset' => mt_rand(0, $texts_count - 1),
            'order' => array('CardResult.card_result_id'),
            'recursive' => 1,
        )) : '';

        if (is_array($result['text'])) {
            $result['text'] = $result['text']['CardResultLang']['description'];
        }

        // we might have left empty keywords, now that we know the result we can fill them
        $default_keywords = null;
        foreach ($card_items as $card_item) {
            for ($index = 0; $index <= count($this->request->data['card_item_ids']); ++$index) {
                if ($this->request->data['card_item_ids'][$index] == $card_item['CardItem']['card_item_id']) {
                    break;
                }
            }

            if (!$result['selected_keywords'][$index]) {
                if ($default_keywords === null) {
                    $default_keywords = preg_split('/[^\\w]+/ui', $result['text']);
                    $default_keywords = array_filter(array_map('trim', $default_keywords), function($v) {
                        return strlen($v) > 8;
                    });
                    $default_keywords = array_values(array_unique($default_keywords));
                }
                if ($default_keywords) {
                    $id = mt_rand(0, count($default_keywords) - 1);
                    $keyword = $default_keywords[$id];
                    array_splice($default_keywords, $id, 1);
                }
                if (!$keyword) {
                    $keyword = !empty($card_item['CardItemLang']['title']) ? $card_item['CardItemLang']['title'] : '';
                }
                $result['selected_keywords'][$index] = ucfirst(trim($keyword));
            }
        }

        //
        $result['tr'] = [
            'card_title'        => __('Symbolique générale'),
			'result_title'      => __('Résultat du tirage'),
            'next_title'        => __('Et maintenant qu\'allez-vous faire ?'),
            'ins_title'         => __('Je veux consulter de suite !'),
            'ins_form'          => 'formulaire',
            'see_game'          => __('Découvrir'),

            'answer_is'         => __('La réponse à votre question est'),
            'chat'              => __('Tchat'),
            'email'             => __('Email'),
            'main_again'        => __('Refaire un tirage'),
            'main_other'        => __('Découvrez nos autres tirages'),
            'no'                => __('Non'),
            'side_exp_see_more' => __('Contacter'),
            'side_exp_title'    => __('Je suis disponible pour vous'),
            'side_rev_desc'     => __('Passez votre curseur sur les cartes pour afficher leurs significations'),
            'side_rev_title'    => __('Rappel de votre tirage'),
            'tel'               => __('Tel'),
            'wait_please'       => __('Veuillez patienter ...'),
            'yes'               => __('Oui'),
			'side_exp_see_title'  => __('Nous pouvons approfondir votre tirage'),
			'side_exp_see_desc'   => __('et vous donner plus de détails sur votre histoire dès maintenant'),
        ];

        //
        $agents = $this->Agent->find('all', array(
            'conditions' => array('deleted' => '0', 'active' => '1', 'valid' => '1', 'role' => 'agent', 'agent_status' => 'available'),
            'fields' => array('firstname', 'pseudo', 'agent_number', 'agent_status', 'id', 'reviews_avg', 'consult_email', 'consult_chat', 'consult_phone'),
            'limit' => 1,
            'order' => array('list_pos'),
            'recursive' => 1,
        ));

        $result['related_experts'] = [];
        foreach ($agents as $agent) {
            $agent = $agent['Agent'];

            $categoryLangs = $this->CategoryUser->find('all',array(
                'fields' => array('CategoryLang.category_id', 'CategoryLang.name', 'CategoryLang.link_rewrite'),
                'conditions' => array('CategoryUser.user_id' => $agent['id']),
                'joins' => array(
                    array(
                        'table' => 'category_langs',
                        'alias' => 'CategoryLang',
                        'type'  => 'left',
                        'conditions' => array(
                            'CategoryLang.category_id = CategoryUser.category_id',
                            'CategoryLang.lang_id = ' . (int) $this->request->data['lang_id']
                        )
                    )
                ),
                'recursive' => -1
            ));

            $categories = [];
            foreach ($categoryLangs as $categoryLang) {
                $categories[] = [
                    'name' => $categoryLang['CategoryLang']['name']
                ];
            }

            $link = $fbH->Html->url(
                array(
                    'language'      => $this->Session->read('Config.language'),
                    'controller'    => 'agents',
                    'action'        => 'display',
                    'link_rewrite'  => strtolower(str_replace(' ','-',$agent['pseudo'])),
                    'agent_number'  => $agent['agent_number']
                ),
                array(
                    'title'         => $agent['pseudo']
                )
            );

            $result['related_experts'][] = [
                'name' => $agent['pseudo'] ? $agent['pseudo'] : $agent['firstname'],
                'rating' => $agent['reviews_avg'],
                'status' => $agent['agent_status'],
                'link' => $link,
                'categories' => $categories,
                'has_email' => $agent['consult_email'],
                'has_tchat' => $agent['consult_chat'],
                'has_phone' => $agent['consult_phone'],
				'profile_image' => '/media/photo/'.$agent['agent_number'][0].'/'.$agent['agent_number'][1].'/'.$agent['agent_number'].'_listing.jpg'
            ];
        }
        $result['default_expert_profile_image'] = '/dist/images/cards/empty-profile-img.png';


        //
        $other_cards = [];
        $cards = $this->Card->find('all', ['conditions' => ['Card.active' => 1]]);
        foreach ($cards as $c) {
            $c['CardLang'] = array_filter($c['CardLang'], function($v) {
                return $v['lang_id'] == $this->request->data['lang_id'];
            });
            $c['CardLang'] = $c['CardLang'] ? reset($c['CardLang']) : null;

            $a = [];
            $a['name'] = $c['CardLang']['title'];
            $a['embed_image'] = $c['Card']['embed_image'];
            $a['embed_image_text_color'] = $c['Card']['embed_image_text_color'];
            $a['link'] = $c['CardLang']['url_path'];
            $a['description'] = $c['CardLang']['step_result_embed'];

            if (!$a['name'] || !$a['embed_image']) {
                continue;
            }
            $other_cards[] = $a;
        }
        $result['other_games'] = $other_cards;

        //
        $user = $this->Session->read('Auth.User');
        if (empty($user) || $user['role'] !== 'client') {
            $result['register_form'] = $this->getSubscribeForm();
            $result['next_link'] = '';
        } else {
            $result['register_form'] = '';
            $result['next_link'] = '/';
        }
		
		if (empty($user)) {
			$result['email_form'] = $this->getEmailForm();
		}else{
			$result['email_form'] = '';
		}
        //
        echo json_encode($result);
        return false;
    }

    //
    private function getSubscribeForm() {
        $this->loadModel('UserCountry');
        $this->loadModel('Country');
        $this->loadModel('Lang');
        $this->loadModel('CategoryLang');

        $select_countries = $this->UserCountry->getCountriesForSelect($this->Session->read('Config.id_lang'));

        $view = new View($this, false);
        return $view->element('account_subscribe_2', [
            'firstname' => '',
            'email'     => '',
            'email2'    => '',
            'select_countries' => $select_countries,
            'source_ins' => 'card',
        ]);
    }
	
	 private function getEmailForm() {
        $this->loadModel('UserCountry');
        $this->loadModel('Country');
        $this->loadModel('Lang');
        $this->loadModel('CategoryLang');

        $view = new View($this, false);
        return $view->element('account_subscribe_email', [
            'firstname' => '',
            'email'     => '',
        ]);
    }
	
	public function process_subscribe_email($post = true) {
        $this->layout = 'ajax';
        $this->render(false);
		
		 if ( empty($this->request->data['cardId']) || empty($this->request->data['email'])) {
            echo json_encode(['error' => 'Erreur de saisie.']);
            return false;
        }
		
		//check if email is good
		$string = preg_match('/^[.\w-]+@([\w-]+\.)+[a-zA-Z]{2,6}$/', $this->request->data['email']);
		if(!$string){
			$this->jsonRender(array('return' => false, 'error' => __('Email invalide.')));
			return false;
		} 
		
		//save email si existe pas dans db users
		$this->loadModel('User');
		$this->loadModel('CardEmail');
		
		$user_exist = $this->User->find('first', array(
						'conditions'    => array('User.email' => $this->request->data['email']),
						'recursive'     => -1
					));
		$user_exist2 = $this->CardEmail->find('first', array(
						'conditions'    => array('CardEmail.email' => $this->request->data['email']),
						'recursive'     => -1
					));
		
		if(!$user_exist && !$user_exist2){
		
			$this->CardEmail->create();
			$requestDataEmail = array();
			$requestDataEmail['CardEmail']['card_id'] = $this->request->data['cardId'];
			$requestDataEmail['CardEmail']['date_add'] = date('Y-m-d H:i:s');
			$requestDataEmail['CardEmail']['email'] = $this->request->data['email'];
			if($this->CardEmail->save($requestDataEmail)){
				$result = array('return' => true);
				echo json_encode($result);
				return false;
			}else{
				$this->jsonRender(array('return' => false, 'error' => __('Erreur technique')));
				return false;
			}
		}else{
			$result = array('return' => true);
			echo json_encode($result);
			return false;
		}
		
	}

    /**
     * show result page
     */
    public  function result()
    {
        $params=$this->request->data;
        // get all information about card picked
        $cardInformation=array();
        $this->loadModel('CardItem');

        $listCardPicked=explode(',',$params['picked-cards']);

        foreach ($listCardPicked as $card ){
            $cardItem = $this->CardItem->find('all',array(
                'conditions' => array('id' => $card),
                'recursive' => -1,
            ));
            $cardInformation[]=$cardItem;
        }
		
        $this->set(compact('pickedCards', 'params','cardInformation'));
    }

    /**
     * show step 2 card
     */
    public  function step2()
    {

        $params=$this->request->data;
    }
}
