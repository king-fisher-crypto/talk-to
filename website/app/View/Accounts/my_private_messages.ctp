<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));
echo $this->Session->flash();
?>


<section class=" my_private_messages-page  page ">

    <?php
   // $this->Session->setFlash('Thanks for your payment.');
    
    
    $url_params =  $this->params['pass'] ;
    
    
	$message =  __("Souhaitez vous valider ce message  ?");
	$this->set('message', $message);
	echo $this->element('Utils/modal-confirmation');
    
    ?>



    <article class="marge">
	<h1 class="">  <?= __('Mes messages privés') ?></h1>
	<?php
	//echo"<br>userRole=".$this->Session->read('Auth.User.role')."<br/>";
	//$userRole="agent";
	if($userRole=="agent")
	    echo __("Les messages privés sont gratuits, et peuvent être désactivés à votre convenance, totalement ou uniquement pour un ou plusieurs clients. Si l'un d'eux utilise ce canal à des fins inappropriées, vous pouvez lui en faire part ou lui bloquer définitivement l'accès aux messages privés. Afin de ne pas vous sur-solliciter par ce mode, les messages privés sont limités à 1 message par jour et par client.");
	else
	   echo 	__("Les messages privés ne sont pas des consultations, les LiviMasters n'ont pas d'obligations de vous répondre par ce biais et ne le feront que s'ils en ont le temps. Afin d'éviter tout abus, les messages privés peuvent être désactivés par les LiviMasters et à leur seule appréciation.");
	
	 ?>
 
	
	
	
	
    </article>

    
    
    <div class=" cadre_table">
	
	<div class="links lh24-28j fw400"> 
	     <?php 
	     
	     $url = Configure::read('Site.baseUrlFull')."/accounts/my_private_messages/";
	     
	     $class="black2";
	     if(empty($url_params))  $class="blue2"; ?>
	    <a class="<?=$class; ?>" href="<?=$url;?>"><?= __('boîte de réception') ?></a>
	    <?php 
	    if(1==1) {?>
	    <div class="nbre_msg"><div class="disk lh24-28k white2">3</div></div>
	    <?php } ?>
	    
	    <?php 
	    $class="black2";
	    if(in_array("unread", $url_params))  $class="blue2";?>
	    <a class="<?=$class; ?>" href="<?=$url;?>unread"><span class="message"><?= __('messages') ?></span> <?= __('non lus') ?></a>
	    
	    <?php if(1==1) {?>
	    <div class="nbre_msg"><div class="disk lh24-28k white2">3</div></div>
	    <?php } ?>
	    
	    <?php
	    $class="black2";
	    if(in_array("rough", $url_params)) $class="blue2"; ?>
	    <a class="<?=$class; ?>" href="<?=$url;?>rough"><?= __('brouillons') ?></a>
	     <?php if(1==2) {?>
	    <div class="nbre_msg"><div class="disk lh24-28k white2">3</div></div>
	    <?php } ?>
	</div>
	
	
	<div class=" div_msg">
	    
	    <div class="left_col">
		
		
<!--		<div style="width:200px;height:50px; box-shadow: 0px 3px 35px 0px #00000030;"></div>-->
		
		
		<div class="div_search_client">
		    <input class="btn_like search_client lh22-33 lgrey2" type="text" placeholder="<?= __('Recherche client') ?>">
		    <img src="/theme/black_blue/img/loupe_bleu.svg" class="loupe" />
		</div>
		
		<div class="list_echanges">
		    
	<?php  
		    
	$msgs=[];
	$msgs[]=["id"=>"1", "name"=>"James Potter","img"=>"https://picsum.photos/200/300", "txt"=>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Scelerisque enim orci potenti felis, viverra scelerisque sollicitudin nulla augue. Fames odio suspendisse augue rhoncus in. Dui eget nullam et ultricies tellus elit eget. Molestie pellentesque bibendum dictum quis lacus euismod. "];
	
	$msgs[]=["id"=>"2", "name"=>"Jessica Black","img"=>"https://picsum.photos/200/400", "txt"=>"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem."];
	
	$msgs[]=["id"=>"3", "name"=>"Henry Butter","img"=>"https://picsum.photos/200/500", "txt"=>"At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio."];
	
	$msgs[]=["id"=>"4", "name"=>"James Franck","img"=>"https://picsum.photos/200/600", "txt"=>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Scelerisque enim orci potenti felis, viverra scelerisque sollicitudin nulla augue. Fames odio suspendisse augue rhoncus in. Dui eget nullam et ultricies tellus elit eget. Molestie pellentesque bibendum dictum quis lacus euismod. "];
	


	$msgs[]=["id"=>"5", "name"=>"James Potter","img"=>"https://picsum.photos/200/300", "txt"=>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Scelerisque enim orci potenti felis, viverra scelerisque sollicitudin nulla augue. Fames odio suspendisse augue rhoncus in. Dui eget nullam et ultricies tellus elit eget. Molestie pellentesque bibendum dictum quis lacus euismod. "];
	
	$msgs[]=["id"=>"6", "name"=>"Jessica Black","img"=>"https://picsum.photos/200/400", "txt"=>"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem."];
	
	$msgs[]=["id"=>"7", "name"=>"Henry Butter","img"=>"https://picsum.photos/200/500", "txt"=>"At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio."];
	
	$msgs[]=["id"=>"8", "name"=>"James Franck","img"=>"https://picsum.photos/200/600", "txt"=>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Scelerisque enim orci potenti felis, viverra scelerisque sollicitudin nulla augue. Fames odio suspendisse augue rhoncus in. Dui eget nullam et ultricies tellus elit eget. Molestie pellentesque bibendum dictum quis lacus euismod. "];

	$msgs[]=["id"=>"9", "name"=>"James Potter","img"=>"https://picsum.photos/200/300", "txt"=>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Scelerisque enim orci potenti felis, viverra scelerisque sollicitudin nulla augue. Fames odio suspendisse augue rhoncus in. Dui eget nullam et ultricies tellus elit eget. Molestie pellentesque bibendum dictum quis lacus euismod. "];
	
	$msgs[]=["id"=>"10", "name"=>"Jessica Black","img"=>"https://picsum.photos/200/400", "txt"=>"Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem."];
	
	$msgs[]=["id"=>"11", "name"=>"Henry Butter","img"=>"https://picsum.photos/200/500", "txt"=>"At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio."];
	
	$msgs[]=["id"=>"12", "name"=>"James Franck","img"=>"https://picsum.photos/200/600", "txt"=>"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Scelerisque enim orci potenti felis, viverra scelerisque sollicitudin nulla augue. Fames odio suspendisse augue rhoncus in. Dui eget nullam et ultricies tellus elit eget. Molestie pellentesque bibendum dictum quis lacus euismod. "];
	
foreach($msgs as $msg)
		    {
    $i++;
	    ?>
		    
<div class="echange_bloc <?php if($i==1) echo "active" ?>" data-id="<?=$msg["id"]?>" >
					
			
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><img src="<?=$msg["img"]?>" class="rounded"></td>
    <td class="title lh20-30c  fw500"><?=$msg["name"]?></td>
    <td class="date lh14-21 fw500">aujourd'hui 15:30 AM</td>
  </tr>
  <tr >
    <td>&nbsp;</td>
    <td colspan="2" ><div class="txt fw400 lh18-27b lgrey2"><?=$msg["txt"]; ?></div></td>
    
  </tr>
</table>
			
			
			
		    </div>
		    
		    
		    
		    <?php } ?>
		    
		    
		</div>
	    </div>
	    
	    
	    <div class="right_col">
		
		<div class="div_search_client">
		    <img src="/theme/black_blue/img/menu/chevron.svg" class="chevron" />
		    <input class="btn search_client lh22-33 lgrey2" type="text" placeholder="<?= __('Recherche client') ?>">
		    <img src="/theme/black_blue/img/loupe_bleu.svg" class="loupe" />
		</div>
		
		
		<div class="voir_tout">
		    <div class='img_txt'>
		    <img src="/theme/black_blue/img/big_left_arrow.svg" class="big_arrow" class="big_arrow">
		    <div class="lh22-26 lgrey2 txt"><?= __("Voir tout l'historique de") ?> 
			<span class="name blue2">James Potter</span> </div>
		    </div>
		    <img src="/theme/black_blue/img/croix.svg"  class="close">
		</div>
		
		
		
			    <div class="vignette">
			    <img src="https://picsum.photos/200/600" class="rounded">
			    <div class="lh28-42b fw500 name">James Potter</div>
			    </div>
		
			
		
		<div class="messages_bloc">
		    
		<!--  message_bloc --> 
		     
		
		
		</div> <!--  messages_bloc --> 
		
		<div class="send_msg btn_like ">
<!--		<input class=" lh20-30a lgrey2" type="text" placeholder="<?= __('taper un message') ?>">	-->	<textarea placeholder="<?= __('taper un message') ?>"  class="lh20-30a lgrey2 "      ></textarea>
		<img src="/theme/black_blue/img/big_right_arrow.svg" />
		</div>
		
		
		
	    </div><!--  right_col -->
	    
	</div><!--  div_msg -->
	
	
   </div><!--  fin cadre -->
    
  
   
    
    
    </section>
<div style="display:none;">
    
    <div class="message_bloc" id="bloc_reception">
		    <div class="lus lgrey2 fw300 lh14-21">  
		    <img src="/theme/black_blue/img/courrier_ouvert.svg" class=""/>
		    <?= __('mettre en non lu') ?>
		    </div>
		    
		    <div class="bloc_txt lh14-21 lgrey">
			
			<div class="vignette_date">
			    <div class="vignette">
			    <img src="https://picsum.photos/200/600" class="rounded">
			    <div class="lh28-42b fw500 name">James Potter</div>
			    </div>
			<div class="date fw500 ">aujourd'hui 15:30 AM +05:00, Paris</div>
			</div>
			
			<div class="txt lh22-33c">
			    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Scelerisque enim orci potenti felis, viverra scelerisque sollicitudin nulla augue. Fames odio suspendisse augue rhoncus in. Dui eget nullam et ultricies tellus elit eget. Molestie pellentesque bibendum dictum quis lacus euismod. 
			</div>
			
			<div class="files">
			<img src="/theme/black_blue/img/fichier.svg" /></div>
		    </div>
		</div>
    
    
    
    
		<div class="message_bloc envoi" id="bloc_envoi" >
		<div class="blue bloc_txt lh14-21 white2">
		<div class="date fw500 ">aujourd'hui 15:30 AM +05:00, Paris</div>
		<div class="txt lh22-33c">
		      Lorem ipsum dolor sit amet, consectetur adipiscing elit. Scelerisque enim orci potenti felis, viverra scelerisque sollicitudin nulla augue. Fames odio suspendisse augue rhoncus in. Dui eget nullam et ultricies tellus elit eg
		</div>
		</div>
		</div> 
		</div>


<script>

document.addEventListener("DOMContentLoaded", function() {
	
	
	function clear_smg()
	{
	    $(".echange_bloc").removeClass("active")
	    $(".right_col .voir_tout .name").text("")
	    $(".right_col .bloc_txt .name").text("")
	    $(".right_col .vignette img.rounded").attr("src","")
	    $(".right_col .date ").text("")
	    $(".right_col .bloc_txt  .txt ").text("")
	    $(".right_col .messages_bloc ").html("")
	    $( ".send_msg " ).removeClass("extend_send_msg");
	    $( ".send_msg " ).addClass("contract_send_msg");
	}
	
	
	
	$( ".echange_bloc" ).click(function() {
	    
	    clear_smg()
	  
	    $(this).addClass("active")
//	    console.log("click",$(this));
	    let mobile = false;
	    if( $( ".right_col" ).css("display") =="none" ) mobile = true;
	    
	    if(mobile){
		$( ".right_col" ).toggle();
		$( ".left_col" ).toggle();
	    }
	    
	    let bloc_reception = $("#bloc_reception").clone()
	    bloc_reception.appendTo(".messages_bloc");
	    
	    
	    let id_msg = $(this).data("id");
	    console.log("id",id_msg);
	    
	    let date_str = $(this).find(".date").text()
	    let txt = $(this).find(".txt").text()
	    let name = $(this).find(".title ").text()
	    let img = $(this).find(" img.rounded").attr("src");   
	    
	    $(".right_col .voir_tout .name").text(name)
	    $(".right_col .vignette img.rounded").attr("src",img)
	    $(bloc_reception).find(" .bloc_txt .name").text(name)
	    $(bloc_reception).find("  .date ").text(date_str)
	    $(bloc_reception).find("  .bloc_txt  .txt ").text(txt)
	    
	  });

	   $( ".chevron" ).click(function() {
		$( ".right_col" ).toggle();
		$( ".left_col" ).toggle();
	    });


	 $( ".send_msg textarea" ).click(function() {
		$( ".send_msg " ).removeClass("contract_send_msg").addClass("extend_send_msg");
	 });   

	
	    let mobile = false;
	    if( $( ".right_col" ).css("display") =="none" ) mobile = true;
	    if(!mobile)
	    $( '.echange_bloc:first-child ' ).click ();
	
	

	
	
  $( ".send_msg img" ).click(function() {     $("#modal-confirmation").modal(); });
		
addEventListener('confirm', send_msg, false);
function send_msg()     {
	let txt =  $( ".send_msg textarea" ).val();
	      
	      $( ".send_msg " ).removeClass("extend_send_msg").addClass("contract_send_msg")
	      
	      let bloc_envoi = $("#bloc_envoi").clone()
	    
	    bloc_envoi.appendTo(".messages_bloc");
		
	    let date = "aujourd'hui "+new Date().toLocaleString();;  
	    let nbre = $(".message_bloc.envoi").length
		
	    $(bloc_envoi).find(".bloc_txt  .date").text(date)
	    $(bloc_envoi).find(".bloc_txt  .txt ").text(txt)
	      
	    $(bloc_envoi).attr("id","bloc_"+nbre)    
	}


	
	
   }); // fin  DOMContentLoaded
</script>