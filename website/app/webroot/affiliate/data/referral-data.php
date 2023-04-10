<?php include('../auth/db-connect.php');
include_once '../auth/access-functions.php';
session_start();
/* ===========================================
		Date Period Filter 
   	========================================= */
if(isset($_SESSION['start_date'])){$start_date = $_SESSION['start_date'];}
if(isset($_SESSION['end_date'])){$end_date = $_SESSION['end_date'];}
if(empty($start_date)){$start_date = date('Y-m-d', strtotime('today - 120 days'));;}
if(empty($end_date)){$end_date= date('Y-m-d');}

//CHECK IF CPC ENABLED
$get_cpc_on = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT cpc_on FROM ap_other_commissions WHERE id=1"));
$cpc_on = $get_cpc_on['cpc_on'];
if($cpc_on=='1'){
	$aColumns = array( 'id', 'affiliate_id', 'ip', 'browser', 'os', 'country', 'device_type', 'landing_page', 'cpc_earnings', 'datetime', ' ');
}else{
	$aColumns = array( 'id', 'affiliate_id', 'ip', 'browser', 'os', 'country', 'device_type', 'landing_page', 'datetime', ' ');
}

$sIndexColumn = 'id';
$sTable = 'ap_referral_traffic';
$input =& $_GET;
$sOrder = " ORDER BY datetime DESC";
$sLimit = "";
if(isset($input['iDisplayStart']) && $input['iDisplayLength'] != '-1'){
    $sLimit = " LIMIT ".intval($input['iDisplayStart'] ).", ".intval($input['iDisplayLength']);
}
   
$iColumnCount = count($aColumns);
 
if (isset($input['sSearch']) && $input['sSearch'] != ""){
    $aFilteringRules = array();
    for ($i=0 ; $i<$iColumnCount ; $i++ ){
        if(isset($input['bSearchable_'.$i]) && $input['bSearchable_'.$i] == 'true') {
            $aFilteringRules[] = "`".$aColumns[$i]."` LIKE '%".$mysqli->real_escape_string( $input['sSearch'] )."%'";
        }
    }
    if(!empty($aFilteringRules)){
        $aFilteringRules = array('('.implode(" OR ", $aFilteringRules).')');
    }
}
  
// Individual column filtering
for ($i=0 ; $i<$iColumnCount ; $i++ ){
    if ( isset($input['bSearchable_'.$i]) && $input['bSearchable_'.$i] == 'true' && $input['sSearch_'.$i] != '' ){
        $aFilteringRules[] = "`".$aColumns[$i]."` LIKE '%".$mysqli->real_escape_string($input['sSearch_'.$i])."%'";
    }
}
 
if(!empty($aFilteringRules)){
    $sWhere = " WHERE ".implode(" AND ", $aFilteringRules);
}else{
    $sWhere = " WHERE datetime > '".$start_date."' AND datetime < '".$end_date."'";
}
  
$aQueryColumns = array();
foreach($aColumns as $col){
    if($col != ' '){
        $aQueryColumns[] = $col;
    }
}
 
$sQuery = "
    SELECT SQL_CALC_FOUND_ROWS `".implode("`, `", $aQueryColumns)."`
    FROM `".$sTable."`".$sWhere.$sOrder.$sLimit;
 
$rResult = $mysqli->query($sQuery) or die($mysqli->error);
  
// Data set length after filtering
$sQuery = "SELECT FOUND_ROWS()";
$rResultFilterTotal = $mysqli->query( $sQuery) or die($mysqli->error);
list($iFilteredTotal) = $rResultFilterTotal->fetch_row();
 
// Total data set length
$sQuery = "SELECT COUNT(`".$sIndexColumn."`) FROM `".$sTable."`";
$rResultTotal = $mysqli->query( $sQuery ) or die($mysqli->error);
list($iTotal) = $rResultTotal->fetch_row();
  
$output = array(
    "sEcho"                => intval($input['sEcho']),
    "iTotalRecords"        => $iTotal,
    "iTotalDisplayRecords" => $iFilteredTotal,
    "aaData"               => array(),
);



while($aRow = $rResult->fetch_assoc()){
    $row = array();
    for($i=0 ; $i<$iColumnCount ; $i++ ){
      if($aColumns[$i] == 'id'){}
      elseif($aColumns[$i] == 'affiliate_id'){
        //OUTPUT AFFILIATE NAME
        $get_data = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT fullname FROM ap_members WHERE id=$aRow[$sIndexColumn]"));
	      $fullname = $get_data['fullname'];
          if($fullname==''){$row[] = 'No Affiliate';}else{$row[] = '<a href="affiliate-stats?a='.$aRow[$sIndexColumn].'">'.$fullname.'</a>';}
        }
				//OUPUT DEVICE TYPE
				elseif($aColumns[$i] == 'device_type'){
					if($aRow[ $aColumns[$i] ]=='1'){$row[] = 'Desktop';}
					if($aRow[ $aColumns[$i] ]=='2'){$row[] = 'Phone';}
					if($aRow[ $aColumns[$i] ]=='3'){$row[] = 'Tablet';}
					if($aRow[ $aColumns[$i] ]=='' || $aRow[ $aColumns[$i] ]=='0' ){$row[] = 'Unknown';}
				}
				//OUTPUT COUNTRY
				elseif($aColumns[$i] == 'country'){
					$row[] = '<img src="" class="flag flag-'.strtolower($aRow[ $aColumns[$i] ]).'"/> '.$aRow[ $aColumns[$i] ].'';
				}
        elseif($aColumns[$i] == ' '){
            // TABLE ACTIONS
           $row[] = '
							<form method="post" action="data/delete-referral" class="pull-left">
								<input type="hidden" name="m" value="'.$aRow[$sIndexColumn].'">
								<input type="hidden" name="a" value="'.$aRow['affiliate_id'].'">
								<input type="hidden" name="r" value="'.pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME).'">
								<input type="submit" class="btn btn-xs btn-danger" value="Delete">
							</form>';
        }elseif($aColumns[$i] != ' '){
            // General output
            $row[] = $aRow[ $aColumns[$i] ];
        }
    }
    $output['aaData'][] = $row;
}
echo json_encode( $output );
?>