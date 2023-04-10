<?php

//Spiriteo EXPERTS
ini_set("memory_limit",-1);
set_time_limit ( 0 );
ini_set('display_errors', 1);
error_reporting(E_ALL);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$csvname='export-tva_'.date('d').'-'.date('m').'-'.date('Y').'_'.date('H').'h'.date('i').'.csv';
$monfichier = fopen($csvname, 'a+');

$line = array(

                'firstname'    => '',
                'lastname'     => '',
				'pseudo'       => '',
				'email'       => '',
				'societe_pays'       => '',
				'vat_num'       => '',
				'result'       => '',
				'bo_admin'       => '',
				  
            );
fputcsv($monfichier, array_keys($line), ';', '"');

$resultsql = $mysqli->query("SELECT * from users where role = 'agent' and active = 1 and deleted = 0 order by id");
while($row = $resultsql->fetch_array(MYSQLI_ASSOC)){	
	
	$result = '';
	if($row['vat_num']){
		$tva = str_replace(' ','',$row['vat_num']);
		$iso_code = substr($tva,0,2);
		$vat = substr($tva,2);
        $url = 'http://ec.europa.eu/taxation_customs/vies/vatResponse.html?locale=FR&memberStateCode='.
        strtoupper($iso_code).'&number='.$vat.'&traderName=';
        ini_set('default_socket_timeout', 3);
        for ($i = 0; $i < 3; $i++) {
            if ($line = file_get_contents($url)) {
                if (strstr($line, 'TVA valide')) {
                    ini_restore('default_socket_timeout');
                    $result = 'Numero valide';
                }
                if (strstr($line, 'TVA invalide')) {
                    ini_restore('default_socket_timeout');
                    $result = 'Numero non valide';
                }
                if (strstr($line, 'demandes trop nombreuses')) {
                    ini_restore('default_socket_timeout');
                    $result = 'Numero non verifie';
                }
				if( !$result ) $result = 'Numero incorrect';
            }
        }
        ini_restore('default_socket_timeout');
	}else{
		$result = 'Numero manquant';
	}
	
	$pays = $row['societe_pays'];
	
	if(!$pays){
		$result1 = $mysqli->query("SELECT * from  user_country_langs where user_countries_id = '{$row['country_id']}' and lang_id = 1");
		$row1 = $result1->fetch_array(MYSQLI_ASSOC);
		$pays = $row1['name'];
	}
	
	$line = array(

                'firstname'    => $row['firstname'],
                'lastname'     => $row['lastname'],
				'pseudo'       => $row['pseudo'],
				'email'       => $row['email'],
				'societe_pays'       => $pays,
				'vat_num'       => $row['vat_num'],
				'result'       => $result,
				'bo_admin'  => 'https://fr.spiriteo.com/admin/agents/view-'.$row['id']
				  
            );
	fputcsv($monfichier, array_values($line), ';', '"');
}



 
//ici, chargement des lignes avec fputs()
         
fclose($monfichier);
         
if(file_exists($csvname))
   {
   header('Content-Description: File Transfer');
   header('Content-Type: application/octet-stream');
   header('Content-Disposition: attachment; filename='.basename($csvname));
   header('Content-Transfer-Encoding: binary');
   header('Expires: 0');
   header('Cache-Control: must-revalidate');
   header('Pragma: public');
   header('Content-Length: ' . filesize($csvname));
   ob_clean();
   flush();
   readfile($csvname);
   }
else
   {
   echo 'Le fichier cvs n\'a pas pu être généré pour une raison inconnue !';
   }


?>