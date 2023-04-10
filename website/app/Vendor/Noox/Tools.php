<?php

class Tools extends Controller {

    /**
     * Permet de crop et redimensionner une image.
     *
     * @param string $source Le chemin absolue de l'image source
     * @param string $dest Le chemin absolu de l'image de destination
     * @param string $srcType Le type de l'image source (jpg, png, gif)
     * @param float $cropX Coordonnée X du 1er point du crop
     * @param float $cropY Coordonnée Y du 1er point du crop
     * @param float $cropHeight La hauteur du crop
     * @param float $cropWidth La largeur du crop
     * @param float $destHeight La hauteur de l'image de destination
     * @param float $destWidth La largeur de l'image de destination
     * @param int   $quality La qualité de l'image finale
     *
     * @return bool false en cas d'erreur sinon true
     */
    public static function imageCropAndResized($source,$dest, $srcType, $cropX, $cropY, $cropHeight, $cropWidth, $destHeight, $destWidth, $quality=90){
        if(empty($source) || empty($dest)) return false;

        //Initialise les variables
        $imDst = imagecreatetruecolor($destWidth,$destHeight);

        $imSrc = self::getResourceImage($srcType, $source);

        //Erreur dans la création de l'image
        if($imSrc === false) return false;

        //On redimensionne
        //if(imagecopyresized($imDst,$imSrc,0,0,$cropX,$cropY,$destWidth,$destHeight,$cropWidth,$cropHeight) === false) return false;
        if(imagecopyresampled($imDst,$imSrc,0,0,$cropX,$cropY,$destWidth,$destHeight,$cropWidth,$cropHeight) === false) return false;
        //On sauvegarde l'image
        if(imagejpeg($imDst,$dest, $quality) === false) return false;

        //On libère les ressources
        imagedestroy($imDst);
        imagedestroy($imSrc);

        return true;
    }

    /**
     * Retourne une ressource d'image
     *
     * @param string    $type   L'extension du fichier
     * @param string    $source Le chemin absolu du fichier image
     * @return bool|resource
     */
    public static function getResourceImage($type, $source){
        if(empty($type) || empty($source))
            return false;

        //En minuscule
        $type = strtolower($type);

        //Selon le type de la source
        switch ($type){
            case 'gif' :
                $imSrc = imagecreatefromgif($source);
                break;
            case 'png' :
                $imSrc = imagecreatefrompng($source);
                break;
            case 'jpg' :
            case 'jpeg' :
                $imSrc = imagecreatefromjpeg($source);
                break;
            default :
                $imSrc = imagecreatefromjpeg($source);
        }

        return $imSrc;
    }

    /**
     * @param array $data           Les datas à vérifier
     * @param array $fields         Les champs du formulaire
     * @param array $fieldsRequired Les champs requis du formulaire
     * @param array $deep           Vérifie en profondeur les données reçues pour les champs en question
     * @return array|bool
     */
    public static function checkFormField($data,$fields, $fieldsRequired = array(), $deep=array()){
        if(empty($data) || empty($fields))
            return false;

        //Le tableau qui sera retourné
        $returnData = array();

        //Pour chaque champs du formulaire
        foreach($fields as $field){
            //Si le champ n'existe pas, return false
            if(!isset($data[$field]))
                return false;

            //Est-ce un champ requis ?
            if(in_array($field, $fieldsRequired)){
                //Si vide ou si il n'y a que des espaces
                if(empty($data[$field]) || ctype_space($data[$field]))
                    return false;
                //Si le champ contient un tableau et qu'il faut le parcourir, on le parcours et on vérifie les champs
                if(is_array($data[$field]) && in_array($field, $deep)){
                    //Les champs clé du tableau
                    $keyFields = array_keys($data[$field]);
                    if(!(self::checkFormField($data[$field], $keyFields, $keyFields)))
                        return false;
                }
            }

            //On l'ajoute au tableau final
            $returnData[$field] = $data[$field];
        }

        return $returnData;
    }

    /**
     * Vérifie l'extension d'un fichier depuis un input file.
     *
     * @param array $allowed_types Les types des fichiers acceptés
     * @param string $type Le type du fichier reçu depuis le input file
     * @param string $format Nom du format
     * @return bool
     */
    public static function formatFile($allowed_types, $type, $format){
        if($format == 'Audio' || $format == 'Image'){
            if(!in_array($type, $allowed_types[$format])) return false;
            return true;
        }
        return false;
    }

    /**
     * Converti une date dans la timezone de l'user
     *
     * @param string    $timezone_user  Timezone de l'utilisateur
     * @param string    $stringDate     Date à convertir
     * @return mixed
     */
    public static function dateUser($timezone_user, $stringDate, $format='Y-m-d H:i:s'){
        //Timezone User
		
		
        $dateTimezoneUser = new DateTimeZone($timezone_user);
        //Date user
        $dateTimeUser = new DateTime($stringDate);
        //On ajoute le décalage horaire
		//if($timezone_user != 'Europe/Paris'){
			$dateTimeUser->setTimestamp(($dateTimeUser->getTimestamp()+ $dateTimezoneUser->getOffset($dateTimeUser)));
		//}else{
		//	$dateTimeUser->setTimestamp(($dateTimeUser->getTimestamp()));
		//}

        return $dateTimeUser->format($format);
    }
	
	public static function dateZoneUser($timezone_user, $stringDate, $format='Y-m-d H:i:s'){
        //Timezone User
		
		
        $dateTimezoneUser = new DateTimeZone($timezone_user);
        //Date user
        $dateTimeUser = new DateTime($stringDate);
        //On ajoute le décalage horaire
		$dateTimeUser->setTimestamp(($dateTimeUser->getTimestamp()+ $dateTimezoneUser->getOffset($dateTimeUser)));

        return $dateTimeUser->format($format);
    }


    /**
     * Permet d'extraire, la 1er info dans le nom d'un fichier
     *
     * @param array     $data       Les chemins des fichiers
     * @param string    $separator  Pour délimiter les infos
     * @return array
     */
    public static function extractData($data, $separator){
        //Si vide return array
        if(empty($data)) return array();

        $returnData = array();
        foreach($data as $row){
            //Le nom du fichier
            $filename = basename($row);
            //Position derniere occurrence du separator
            $lengthData = strrpos($filename,$separator);
            $returnData[] = substr($filename,0,$lengthData);
        }

        return $returnData;
    }

    /**
     * Déplace les fichiers depuis $srcPath vers $destPath
     *
     * @param string    $srcPath    Le chemin du dossier source
     * @param string    $destPath   Le chemin du dossier de destination
     * @param array     $files      Les noms des fichiers à déplacer
     * @return bool
     */
    public static function moveFile($srcPath, $destPath, $files){
        if(empty($srcPath) || empty($destPath)) return false;

        foreach($files as $key => $file){
            //L'ancien fichier
            $old = $srcPath.'/'.$file['old'];
            //Le nouveau fichier
            $new = $destPath.'/'.$file['new'];
            //On crée le dossier au cas où
            if (!is_dir($destPath))
                mkdir($destPath, 0755, true);
            //On renomme (on déplace)
            if(!rename($old,$new)) return false;
        }

        return true;
    }

    /**
     * Supprime les fichiers
     *
     * @param array $files  Les fichiers avec le chemin complet
     * @return bool
     */
    public static function deleteFile($files){
        if(empty($files)) return false;

        foreach($files as $key => $file){
            if(!unlink($file)) return false;
        }

        return true;
    }

    /**
     * Explose une date sous le format JJ-MM-AAAA HH:mm sous la forme d'un tableau
     *
     * @param string    $date   La date à exploser
     * @return array    La date sous un format array
     */
    public static function explodeDate($date){
        if(empty($date) || !is_string($date)) return array();

        //Premier découpage, on sépare les heures du reste
        //INFOS : 0: date, 1:horaire
        $date = explode(' ',$date);

        $transit = explode('-',$date[0]);
        //INFOS :  0: jour, 1: mois, 2: année
        $data['A'] = $transit[2];
        $data['M'] = $transit[1];
        $data['J'] = $transit[0];
        //S'il y a des horaires
        if(!empty($date[1])){
            $transit = explode(':', $date[1]);
            //INFOS :
            $data['H']   = $transit[0];
            $data['Min'] = $transit[1];
        }

        return $data;
    }

    /**
     * Retourne la différence entre deux date en sec
     *
     * @param string    $start  La date de début
     * @param string    $end    La date de fin
     * @return mixed
     */
    public static function diffInSec($start, $end){
        if(empty($start) || empty($end))
            return false;
        $tmstmpStart = new DateTime($start);
        $tmstmpStart = $tmstmpStart->getTimestamp();
        $tmstmpEnd = new DateTime($end);
        $tmstmpEnd = $tmstmpEnd->getTimestamp();
        return ($tmstmpEnd - $tmstmpStart);
    }

    /*
     * Permet de savoir si c'est noox qui est sur le site
     */
    public static function isNoox()
    {
        return ($_SERVER["REMOTE_ADDR"] == "109.190.94.104");
    }


    /**
     * Permet de renvoyer une chaine sous le format d'une url
     *
     * @param string    $str   Le string à modifier
     * @return bool|mixed|string
     */
    public static function str2url($str){
        //Si la chaine est vide
        if(empty($str))
            return false;

        //On supprime les espaces inutiles en début et fin de chaine
        $str = trim($str);
        //Chaine en miniscule
        $str = mb_strtolower($str, "UTF-8");
        //On remplace les accents
        $str = self::replaceAccentedChars($str);

        //On supprime les caractères indésirables
        $str = preg_replace('/[^a-zA-Z0-9\s\'\:\/\[\]-]/','', $str);
        $str = preg_replace('/[\s\'\:\/\[\]-]+/', ' ', $str);
        $str = trim($str);
        $str = str_replace(array(' ', '/'), '-', $str);

        return $str;
    }

    /**
     * Replace all accented chars by their equivalent non accented chars.
     *
     * @param string $str
     * @return string
     */
    public static function replaceAccentedChars($str)
    {
        $patterns = array(
            /* Lowercase */
            '/[\x{0105}\x{00E0}\x{00E1}\x{00E2}\x{00E3}\x{00E4}\x{00E5}]/u',
            '/[\x{00E7}\x{010D}\x{0107}]/u',
            '/[\x{010F}]/u',
            '/[\x{00E8}\x{00E9}\x{00EA}\x{00EB}\x{011B}\x{0119}]/u',
            '/[\x{00EC}\x{00ED}\x{00EE}\x{00EF}]/u',
            '/[\x{0142}\x{013E}\x{013A}]/u',
            '/[\x{00F1}\x{0148}]/u',
            '/[\x{00F2}\x{00F3}\x{00F4}\x{00F5}\x{00F6}\x{00F8}]/u',
            '/[\x{0159}\x{0155}]/u',
            '/[\x{015B}\x{0161}]/u',
            '/[\x{00DF}]/u',
            '/[\x{0165}]/u',
            '/[\x{00F9}\x{00FA}\x{00FB}\x{00FC}\x{016F}]/u',
            '/[\x{00FD}\x{00FF}]/u',
            '/[\x{017C}\x{017A}\x{017E}]/u',
            '/[\x{00E6}]/u',
            '/[\x{0153}]/u',

            /* Uppercase */
            '/[\x{0104}\x{00C0}\x{00C1}\x{00C2}\x{00C3}\x{00C4}\x{00C5}]/u',
            '/[\x{00C7}\x{010C}\x{0106}]/u',
            '/[\x{010E}]/u',
            '/[\x{00C8}\x{00C9}\x{00CA}\x{00CB}\x{011A}\x{0118}]/u',
            '/[\x{0141}\x{013D}\x{0139}]/u',
            '/[\x{00D1}\x{0147}]/u',
            '/[\x{00D3}]/u',
            '/[\x{0158}\x{0154}]/u',
            '/[\x{015A}\x{0160}]/u',
            '/[\x{0164}]/u',
            '/[\x{00D9}\x{00DA}\x{00DB}\x{00DC}\x{016E}]/u',
            '/[\x{017B}\x{0179}\x{017D}]/u',
            '/[\x{00C6}]/u',
            '/[\x{0152}]/u');

        $replacements = array(
            'a', 'c', 'd', 'e', 'i', 'l', 'n', 'o', 'r', 's', 'ss', 't', 'u', 'y', 'z', 'ae', 'oe',
            'A', 'C', 'D', 'E', 'L', 'N', 'O', 'R', 'S', 'T', 'U', 'Z', 'AE', 'OE'
        );

        return preg_replace($patterns, $replacements, $str);
    }

    public static function saveAttachment($inputFile, $dest, $agent_number, $idMail){
        if($inputFile['size'] == 0 || empty($dest) || empty($agent_number) || empty($idMail))
            return false;

        //On récupère l'image selon son origine
        $srcType = substr($inputFile['type'],6);
        //Selon le type de la source
        switch ($srcType){
            case 'gif' :
                $imSrc = imagecreatefromgif($inputFile['tmp_name']);
                break;
            case 'png' :
                $imSrc = imagecreatefrompng($inputFile['tmp_name']);
                break;
            case 'jpg' :
            case 'jpeg' :
                $imSrc = imagecreatefromjpeg($inputFile['tmp_name']);
                break;
            default :
                $imSrc = imagecreatefromjpeg($inputFile['tmp_name']);
        }

        //On s'assure que le dossier de destination existe bien
        $destPath = $dest.'/'.$agent_number[0].'/'.$agent_number[1];
        if (!is_dir($destPath))
            mkdir($destPath, 0755, true);
        //On renomme le fichier
        $newName = $agent_number.'-'. $idMail .'.jpg';
        $destPath.= '/'.$newName;
        //Si erreur lors de la sauvegarde de l'image
        if(!imagejpeg($imSrc, $destPath, 90))
            return false;

        return true;
    }
	
	 public static function saveSupportAttachment($inputFile, $dest, $agent_number, $idMail){
        if($inputFile['size'] == 0 || empty($dest) || empty($agent_number) || empty($idMail))
            return false;

        //On s'assure que le dossier de destination existe bien
        $destPath = $dest.'/'.$agent_number[0].'/'.$agent_number[1];
        if (!is_dir($destPath))
            mkdir($destPath, 0755, true);
        
		 $path_parts = pathinfo($inputFile["name"]);
		$extension = $path_parts['extension'];
		 //On renomme le fichier
        $newName = $agent_number.'-'. $idMail .'.'.$extension;
        $destPath.= '/'.$newName;
        //Si erreur lors de la sauvegarde de l'image
		if(!move_uploaded_file( $inputFile['tmp_name'], $destPath ))
            return false;

        return true;
    }

    public static function clearUrlImage($data, $search = '/app/webroot', $replace = ''){
        //On effectue le recherche/remplace
        $data = str_replace($search, $replace, $data);

        return $data;
    }

    public static function texte_resume_html($texte, $nbreCar){
        if(is_numeric($nbreCar)){
            $PointSuspension		= '...';
            $LongueurAvantSansHtml	= strlen(trim(strip_tags($texte)));
            $MasqueHtmlSplit		= '#</?([a-zA-Z1-6]+)(?: +[a-zA-Z]+="[^"]*")*( ?/)?>#';
            $MasqueHtmlMatch		= '#<(?:/([a-zA-Z1-6]+)|([a-zA-Z1-6]+)(?: +[a-zA-Z]+="[^"]*")*( ?/)?)>#';
            $texte					.= ' ';
            $BoutsTexte				= preg_split($MasqueHtmlSplit, $texte, -1,  PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_NO_EMPTY);
            $NombreBouts			= count($BoutsTexte);
            if( $NombreBouts == 1 ){
                $texte				.= ' ';
                $LongueurAvant		= strlen($texte);
                $texte 				= substr($texte, 0, strpos($texte, ' ', $LongueurAvant > $nbreCar ? $nbreCar : $LongueurAvant));
                if ($PointSuspension!='' && $LongueurAvant > $nbreCar) {
                    $texte			.= $PointSuspension;
                }
            } else {
                $longueur				= 0;
                $indexDernierBout		= $NombreBouts - 1;
                $position				= $BoutsTexte[$indexDernierBout][1] + strlen($BoutsTexte[$indexDernierBout][0]) - 1;
                $indexBout				= $indexDernierBout;
                $rechercheEspace		= true;
                foreach( $BoutsTexte as $index => $bout )
                {
                    $longueur += strlen($bout[0]);
                    if( $longueur >= $nbreCar )
                    {
                        $position_fin_bout = $bout[1] + strlen($bout[0]) - 1;
                        $position = $position_fin_bout - ($longueur - $nbreCar);
                        if( ($positionEspace = strpos($bout[0], ' ', $position - $bout[1])) !== false  )
                        {
                            $position	= $bout[1] + $positionEspace;
                            $rechercheEspace = false;
                        }
                        if( $index != $indexDernierBout )
                            $indexBout	= $index + 1;
                        break;
                    }
                }
                if( $rechercheEspace === true ){
                    for( $i=$indexBout; $i<=$indexDernierBout; $i++ ){
                        $position = $BoutsTexte[$i][1];
                        if( ($positionEspace = strpos($BoutsTexte[$i][0], ' ')) !== false ){
                            $position += $positionEspace;
                            break;
                        }
                    }
                }
                $texte					= substr($texte, 0, $position);
                preg_match_all($MasqueHtmlMatch, $texte, $retour, PREG_OFFSET_CAPTURE);
                $BoutsTag				= array();
                foreach( $retour[0] as $index => $tag ){
                    if( isset($retour[3][$index][0]) ){
                        continue;
                    }
                    if( $retour[0][$index][0][1] != '/' ){
                        array_unshift($BoutsTag, $retour[2][$index][0]);
                    } else {
                        array_shift($BoutsTag);
                    }
                }
                if( !empty($BoutsTag) ){
                    foreach( $BoutsTag as $tag ){
                        $texte		.= '</'.$tag.'>';
                    }
                }
                if ($PointSuspension!= '' && $LongueurAvantSansHtml > $nbreCar) {
                    $texte				.= 'ReplacePointSuspension';
                    $pattern			= '#((</[^>]*>[\n\t\r ]*)?(</[^>]*>[\n\t\r ]*)?((</[^>]*>)[\n\t\r ]*)?(</[^>]*>)[\n\t\r ]*ReplacePointSuspension)#i';
                    $texte				= preg_replace($pattern, $PointSuspension.'${2}${3}${5}', $texte);
                }
            }
        }
        return $texte;
    }


    public static function implodePhoneNumber($indicatifTel, $phoneNumber){
        if(empty($indicatifTel) || empty($phoneNumber))
            return '';

        //Si il y a un '0' en début de chaine, on le supprime
        if($phoneNumber[0] === '0')
            $phoneNumber = substr($phoneNumber, 1);

        return $indicatifTel.$phoneNumber;
    }
}