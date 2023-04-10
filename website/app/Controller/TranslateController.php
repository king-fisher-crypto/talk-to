<?php
App::uses('AppController', 'Controller');
App::uses('CakeTime', 'Utility');


class TranslateController extends AppController {
    protected $locale_dir = '';
    protected $pot_filename = 'default.pot';
    protected $site_langs = array();
    protected $site_langs_options = array();

    public function beforeFilter()
    {
        $this->locale_dir = realpath(dirname(__FILE__)."/../").'/Locale/';
        $this->pot_filename = $this->locale_dir.$this->pot_filename;


        $this->loadModel('Lang');
        $this->site_langs = $this->Lang->find("all", array(
            'fields' => array('name','language_code'),
            'order' => 'name',
            'conditions' => array(
                //'id_lang !=' => '1'
            )
        ));

        foreach ($this->site_langs AS $k => $lang){
            $language_code = $lang['Lang']['language_code'];
            if ($language_code == 'fre'){
                $language_code = 'fra';
                $this->site_langs[$k]['Lang']['language_code']= $language_code;
            }
            $this->site_langs_options[$language_code] = $lang['Lang']['name'].' ('.$language_code.')';

        }
        /* Récupération de la liste des .po existants sur serveur */
            $files = array();
            foreach ($this->site_langs_options AS $iso => $lang){
                $pofilename = $this->locale_dir.$iso.'/LC_MESSAGES/default.po';
                if (file_exists($pofilename)){
                    $files[$iso] = array(
                        'langue'   => $lang,
                        'filename' => $pofilename,
                        'exists'   => true,
                        'filemtime'=> date("d/m/Y",filemtime($pofilename))
                    );
                }else{
                    $files[$iso] = array(
                        'langue'   => $lang,
                        'filename' => $pofilename,
                        'exists'   => false,
                        'filemtime'=> false
                    );
                }
            }


        $this->set(array(
            'lang_options' => $this->site_langs_options,
            'po_files_exists' => $files
        ));

        parent::beforeFilter();
    }
    public function admin_upload()
    {
        $uploadedFile = isset($this->request->data['pofile']['tmp_name'])?$this->request->data['pofile']['tmp_name']:false;
        if (!$uploadedFile){
            $this->Session->setFlash(__('Aucun fichier n\'a été transmis.'),'flash_error');
            $this->redirect(array('controller' => 'translate', 'action' => 'index' , 'admin' => true),false);
        }
        if (!file_exists($uploadedFile)){
            $this->Session->setFlash(__('Erreur de téléchargement de fichier, veuillez contacter l\'admin serveur'),'flash_error');
            $this->redirect(array('controller' => 'translate', 'action' => 'index' , 'admin' => true),false);
        }

        if (!in_array($this->request->data['langue'], array_keys($this->site_langs_options))){
            $this->Session->setFlash(__('Veuillez sélectionner une langue valide pour qualifier ce fichier .po'),'flash_error');
            $this->redirect(array('controller' => 'translate', 'action' => 'index' , 'admin' => true),false);
        }
        if (mime_content_type($uploadedFile) !== 'text/x-po'){
            $this->Session->setFlash(__('Le fichier que vous tentez d\'importer ne semble pas être un fichier .po valide'),'flash_error');
            $this->redirect(array('controller' => 'translate', 'action' => 'index' , 'admin' => true),false);
        }

        /* On créé le dossier si inexistant */
            $destDir = $this->locale_dir.$this->request->data['langue'].'/LC_MESSAGES/';
            if (!file_exists($destDir)){
                mkdir($destDir, 0755, true);
            }

        /* On backup l'ancien fichier */
            if (file_exists($destDir.'default.po')){
                $destBackup = $destDir.date("Y-m")."/";
                if (!file_exists($destBackup))
                    mkdir($destBackup, 0755, true);
                if (!copy($destDir.'default.po', $destBackup.date("Y-m-d-H-i-s").".po")){
                    $this->Session->setFlash(__('Impossible d\'effectuer une copie de sauvegarde avant de remplacer le fichier. Veuillez contacter votre administrateur serveur'),'flash_error');
                    $this->redirect(array('controller' => 'translate', 'action' => 'index' , 'admin' => true),false);
                }
            }

        /* On place le nouveau fichier */
            copy($uploadedFile, $destDir.'default.po');

        /* On vide le cache */
        $files = array();
        $files = array_merge($files, glob(CACHE . 'persistent' . DS . '*'));
        foreach ($files AS $f){
            if (is_file($f))
                unlink($f);
        }

        $this->Session->setFlash(__('Votre fichier traduction a bien été mis en production'),'flash_success');
        $this->redirect(array('controller' => 'translate', 'action' => 'index' , 'admin' => true),false);
    }
    public function admin_index()
    {

        $date = new DateTime();
        $this->set(array(
            'date_pot' => CakeTime::format(filemtime($this->pot_filename), '%d/%m/%Y', 'Paris')
        ));
    }
    public function admin_get_pot()
    {
        $this->autoRender = false;
        $locale_dir = realpath(dirname(__FILE__)."/../").'/Locale/';
        $pot_file = $locale_dir.'default.pot';

        $cmd = 'cd '.realpath(dirname(__FILE__)."/../").' && Console/cake i18n extract --extract-core no --validation-domain --output ';
        $cmd.= $locale_dir.' --merge no --overwrite yes ';
        $cmd.= '--paths '.realpath(dirname(__FILE__)."/../");

        shell_exec($cmd);

        $this->response->file($pot_file, array(
            'download' => true,
            'name'     => 'default.pot'
        ));
    }
    public function admin_get_po()
    {
        $iso = isset($this->request->query['iso'])?$this->request->query['iso']:false;
        if (empty($iso)){
            $this->Session->setFlash(__('Fichier introuvable'),'flash_success');
            $this->redirect(array('controller' => 'translate', 'action' => 'index' , 'admin' => true),false);
        }
        $this->autoRender = false;
        $filename = realpath(dirname(__FILE__)."/../").'/Locale/'.$iso.'/LC_MESSAGES/default.po';
        if (!file_exists($filename)){
            $this->Session->setFlash(__('Fichier inexistant'),'flash_success');
            $this->redirect(array('controller' => 'translate', 'action' => 'index' , 'admin' => true),false);
        }

        $this->response->file($filename, array(
            'download' => true,
            'name'     => $iso.'.po'
        ));
    }
    private function backupExistingPotFiles($locale_dir=false)
    {
        if (!file_exists($locale_dir) || !is_dir($locale_dir))return false;
        $found = array();
        if ($handle = opendir($locale_dir)) {
            while (false !== ($entry = readdir($handle))) {
                if (strpos($entry, '.pot') !== false)
                    $found[] = $locale_dir.$entry;
            }
        }

        $dir = $locale_dir."backup_pots/".date("Y-m")."/";
        if (!file_exists($dir) && !is_dir($dir)){
            mkdir($dir, 0755, true);
        }

        $copyok = 0;
        foreach ($found AS $file){
            $fileDest = $dir.date("ymd-Hi")."-".basename($file);
            if (copy($file, $fileDest))
                $copyok++;
        }
        return ($copyok == count($found));
    }
}