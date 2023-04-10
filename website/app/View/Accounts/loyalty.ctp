<style>
    
    

.loyalty-page .grid{
  display: grid;
  grid-template-columns: calc(210px*var(--coef)) repeat(3, 1 );
  grid-template-rows:repeat(7,calc(48px*var(--coef)) );
  grid-gap: calc(10px*var(--coef));
}

.loyalty-page .grid .label_h {
    visibility: hidden;
}

.loyalty-page .grid1 .label_h {
    visibility: visible;
}

 .g1_1{grid-row: 1 / 2;   grid-column: 1 / 2;}
 .g1_2{grid-row: 1 / 2;   grid-column: 2 / 3;}
 .g1_3{grid-row: 1 / 2;   grid-column: 3 / 4;}
 .g1_4{grid-row: 1 / 2;   grid-column: 4 / 5;}
 .g1_5{grid-row: 1 / 2;   grid-column: 5 / 6;}

 .g2_1{grid-row: 2 / 3;   grid-column: 1 / 2;}
 .g2_2{grid-row: 2 / 3;   grid-column: 2 / 3;}
 .g2_3{grid-row: 2 / 3;   grid-column: 3 / 4;}
 .g2_4{grid-row: 2 / 3;   grid-column: 4 / 5;}
 .g2_5{grid-row: 2 / 3;   grid-column: 5 / 6;}

 
.g3_1{grid-row: 3 / 4;   grid-column: 1 / 2;}
.g3_2{grid-row: 3 / 4;   grid-column: 2 / 3;}
.g3_3{grid-row: 3 / 4;   grid-column: 3 / 4;}
.g3_4{grid-row: 3 / 4;   grid-column: 4 / 5;}
.g3_5{grid-row: 3 / 4;   grid-column: 5 / 6;}


 .g4_1{grid-row: 4 / 5;   grid-column: 1 / 2;}
.g4_2{grid-row: 4 / 5;   grid-column: 2 / 3;}
.g4_3{grid-row: 4 / 5;   grid-column: 3 / 4;}
.g4_4{grid-row: 4 / 5;   grid-column: 4 / 5;}
.g4_5{grid-row: 4 / 5;   grid-column: 5 / 6;}

.g5_1{grid-row: 5 / 6;   grid-column: 1 / 2;}
.g5_2{grid-row: 5 / 6;   grid-column: 2 / 3;}
.g5_3{grid-row: 5 / 6;   grid-column: 3 / 4;}
.g5_4{grid-row: 5 / 6;   grid-column: 4 / 5;}
.g5_5{grid-row: 5 / 6;   grid-column: 5 / 6;}

 
 
  .g1_9{grid-row: 1 / 2 ;   grid-column: 2 / 6;}
  .g2_9{grid-row: 2 / 3;   grid-column: 2 / 6;}
  .g3_9{grid-row: 3 / 4;   grid-column: 2 / 6;}
  .g4_9{grid-row: 4 / 5;   grid-column: 2 / 6;}
  .g5_9{grid-row: 5 / 6;   grid-column: 2 / 6;}
  
  
  
.loyalty-page .grid  .cell{
    margin-left:calc(70px*var(--coef));
    align-self: center;
    
}

.loyalty-page  .grid .label_h,
.loyalty-page  .grid .label_l
{
    font-weight: 500;
    align-self: center;
   
}

.loyalty-page .grid .mois{
   align-self: center;
}


.loyalty-page .grid .total{    grid-row: 6 / 7  ;   grid-column: 1 / 6; }
.loyalty-page .grid .total_let{ grid-row: 6 / 7  ;   grid-column: 1 / 2; margin-left: calc(20px*var(--coef))}
.loyalty-page .grid .total_num{ grid-row: 6 / 7  ;   grid-column: 4 / 5;}



.loyalty-page .grid .total2{    grid-row: 7 / 8  ;   grid-column: 1 / 6; }
.loyalty-page .grid .total_let2{ grid-row: 7 / 8  ;   grid-column: 1 / 2; margin-left: calc(20px*var(--coef))}
.loyalty-page .grid .total_num2{ grid-row: 7 / 8  ;   grid-column: 4 / 5;}



/* ONLY iPads (portrait and landscape) ----------- */
@media only screen and (min-width : 768px)  and (max-width : 1024px)  {
    /* Styles */
    
.loyalty-page .grid{
  grid-template-columns: calc(230px*var(--coef)) repeat(3, 1 );
  margin-top: 0;
  margin-bottom: 0;
}

.loyalty-page .grid:not(:first-child) {
    margin-top: calc(10px*var(--coef));
}

.loyalty-page .grid .label_h {
    visibility: visible;
}


.loyalty-page .grid  .cell{
    margin-left:calc(20px*var(--coef));    
}

.loyalty-page .grid .total_let, .loyalty-page .grid .total_num{
    color:var( --second-color);
     align-self: center;
     margin-top: calc(40px*var(--coef));
}


.loyalty-page .grid .total{
    margin-top: calc(20px*var(--coef));
}
    
    
}




/* MOBILE */
@media only screen   and (max-width : 767px)
{
    .loyalty-page .grid  .cell{
    margin-left:0; 
    justify-self: center;
}




.loyalty-page .grid{
  grid-template-columns:repeat(4,1fr );
  grid-template-rows:repeat(7,calc(30px*var(--coef)) );
  margin-top: 0;
  margin-bottom: 0;
  grid-row-gap:calc(26px*var(--coef));
  grid-column-gap:5px;
}

.loyalty-page .grid:not(:first-child) {
    margin-top: 0;
}

.loyalty-page .grid .label_h {
    visibility: visible;
}




.loyalty-page .grid .total_let, .loyalty-page .grid .total_num{
    color:var( --second-color);
     align-self: center;
     margin-top: 0;
     font-weight: 600;
}


.loyalty-page .grid .total{
    margin-top: 0;
    border-radius: calc(35px*var(--coef));
}

.voir_gains{
    margin-top:  calc(30px*var(--coef));
    text-align: center;
}

.loyalty-page .grid .total_let2, .loyalty-page .grid .total_num2{
    font-weight: 600;
    margin-top: 0;
}

}




</style>

<?php 
//echo $this->Session->flash();
  $loyalty_credit = [];
echo $this->element('Utils/year-picker');

$class="no_gain";
 if(count($loyalty_credit)==0) {$class="gain";}

?>


<section class="loyalty-page  page  <?=$class;?>">


    <article>
	<h1 class="">  <?= __('Mes gains / Affiliation') ?></h1>
    </article> 

    
    
    <?php 
    
    $labels_col=["Recrutement LiviMasters", "Recrutement Ambassadeurs" ,"Affiliation vidéos formation" , "Affiliation MasterClass"];
    $labels_row=["Inscrits", "Actifs" ,"Montant gains"];
    
    $mois_ar = ["1"=> "janvier","2"=> "février","3"=>"Mars", "4"=>"Avril", "5"=>"Mai", "6"=>"Juin", "7"=>"Juillet", "8"=>"Août", "9"=>"Septembre", "10"=>"octobre", "11"=>"Novembre", "12"=>"Décembre"];
    $loyalty_credit = [];
    $loyalty_credit[5]= [];
    $loyalty_credit[4]= [];
    $loyalty_credit[3]= [];
    $loyalty_credit[2]= [];
    $loyalty_credit[1]= [];
    
    $loyalty_credit[5]["Recrutement LiviMasters"]=[];
    $loyalty_credit[5]["Recrutement LiviMasters"]["Inscrits"]=5;
    $loyalty_credit[5]["Recrutement LiviMasters"]["Actifs"]=2;
    $loyalty_credit[5]["Recrutement LiviMasters"]["Montant gains"]="25,25$";
    
    $loyalty_credit[5]["Recrutement Ambassadeurs"]=[];
    $loyalty_credit[5]["Recrutement Ambassadeurs"]["Inscrits"]=120;
    $loyalty_credit[5]["Recrutement Ambassadeurs"]["Actifs"]=112;
    $loyalty_credit[5]["Recrutement Ambassadeurs"]["Montant gains"]="125,25$";
    
    $loyalty_credit[5]["Affiliation vidéos formation"]=[];
    $loyalty_credit[5]["Affiliation vidéos formation"]["Inscrits"]=5;
    $loyalty_credit[5]["Affiliation vidéos formation"]["Actifs"]=2;
    $loyalty_credit[5]["Affiliation vidéos formation"]["Montant gains"]="25,25$";
    
    $loyalty_credit[5]["Affiliation MasterClass"]=[];
    $loyalty_credit[5]["Affiliation MasterClass"]["Inscrits"]=120;
    $loyalty_credit[5]["Affiliation MasterClass"]["Actifs"]=112;
    $loyalty_credit[5]["Affiliation MasterClass"]["Montant gains"]="125,25$";
    
    $loyalty_credit[4]= $loyalty_credit[5];
    $loyalty_credit[3]= $loyalty_credit[5];
    $loyalty_credit[2]= $loyalty_credit[5];
    $loyalty_credit[1]= $loyalty_credit[5];
//    $loyalty_credit[12]= $loyalty_credit[5];
//     $loyalty_credit[11]= $loyalty_credit[5];
    
   $loyalty_credit = [];
   
 
   
    if(count($loyalty_credit)==0){ ?>
    <div class="no_gain">
    <div class="txt1 orange2 lh30-35">
	<?= __('Vous n’avez pas encore de gains parrainage / Affiliation') ?>
    </div>
    
    <div class="txt2 lh24-28d ">
	<?= __("Gagnez des revenus supplémentaires en invitant et en parrainant de nouveaux LiviMasters, de nouveaux Ambassadeurs, mais également en faisant la promotion des Videos Formation et des MasterClass de vos LiviMasters préférés&nbsp;!") ?>
    </div>
    
    <div class="div_btn">
	<div class="btn lh25-29c h85b recruter up_case blue2">
	<?= __('recruter des affiliés ') ?>
	</div>
	    
    </div>
    
    </div>
    <?php 
    
   return;
    } ?>
    
    
    <div class="gain">
    
	<div class="txt1  lh30-35b lgrey2">
	<?= __('Mon code affilié') ?>
    </div>
    
    <div class="btn_like blue txt2 lh24-28d h60b">
	<?= __("Lorem Ipsum") ?>
    </div>
	
	

	<div class="cadre_table ">
	
	
	<div class="btns"> 
	    <a rel="modal:open" href="#year-picker" id="btn_datepicker" class="btn spe1  date transparent daterange b blue2"    title="<?= __('dates') ?>"><img src="/theme/black_blue/img/calendrier.svg"> 2022</a> 
	   
	</div> 
	
	    <div class="overflow_y">
	
	 <?php 
	 $g=0;
	 
	 foreach ($loyalty_credit as $mois => $loyalty_mois)
	{
	     $g++;
	     
      ?>
	
	
	<div class="grid grid<?=$g?>">
	    
	    <div class="g1_1 lh26-30b mois blue2 "><?= $mois_ar[$mois]?></div>
	    <div class="g1_2 cell label_h lh26-30b "><?= __('Inscrits') ?></div>
	    <div class="g1_3 cell label_h lh26-30b "><?= __('Actifs') ?></div>
	    <div class="g1_4 cell label_h lh26-30b "><?= __('Montant gains') ?></div>
	    
	    <div class="g2_1 label_l lh24-28g "><?= __('Recrutement LiviMasters') ?></div>
	    <div class="g3_1 label_l lh24-28g "><?= __('Recrutement Ambassadeurs') ?></div>
	    <div class="g4_1 label_l lh24-28g "><?= __('Affiliation vidéos formation') ?></div>
	    <div class="g5_1 label_l lh24-28g "><?= __('Affiliation MasterClass') ?></div>
	    
	    <?php 
		$c=0;
		$d=0;
	    
	       foreach ($labels_col as $key_c=>$label_col)
		    {
	    
	    foreach ($labels_row as $key_r=>$label_row)
		 {
		 echo "<div class='cell  cell2 lh22-33 g".(intval($key_c)+2)."_".(intval($key_r)+2)."'>".$loyalty_mois[$label_col][$label_row]."</div>";
		    }
		    
		   
		    if($c==2) $c=0;
	    echo "<div class='field_bar  l$d field_bar$c g".(intval($key_c)+2)."_9'></div>";
		    $c++;
		    $d++;
		    
		 }
	    
	    ?>
	    
	
	
	<div class="btn_like total blue h48">
	
	</div>
	  <div class="total_let lh24-28b "><?= __('Total') ?> / <?= $mois_ar[$mois]?></div>
	 <div class="total_num cell lh24-28b ">2 545,00 $</div>   
	    
	   
	 <?php if($mois==1) { ?>
	 
	 <div class=" total2  ">
	
	</div>
	  <div class="total_let2 lh24-28b blue2"><?= __('Total') ?> / 2022</div>
	 <div class="total_num2 cell lh24-28b blue2">2 545,00 $</div>     

	 <?php } ?>
	 
	</div> <!--grid -->
	 <?php
	 /*
	 if($mois==1) { ?>
	<div class="year blue2 b lh30-45">2021</div>
	
	 <?php }
	  */ ?>
	  
	
	
    <?php } ?>
	 </div><!--  overflow_y -->
    </div><!-- cadre_table-->
	
    <div class="voir_gains">
    <a class="blue2 lh24-28g underline up_case" href="/accounts/affiliate_payment"><?= __('Voir Mes Gains et Reversements') ?></a>
    </div>
    
    </div><!-- class="gain"-->
    <?php if ($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator); ?>



<?php
//echo $this->Frontblock->getRightSidebar();
?>

   <input type="hidden" id="daterange" class="form-control"  >

</section>

