 <?php
    $userRole = $this->Session->read('Auth.User.role');
   //  $userRole = "agent";
   // $userRole = "client";
      
 //echo"<br>userRole = ".$userRole;
    ?>
<div class="profile-page page <?=$userRole ?>">

    <div class="btns <?= $userRole ?>">
<!--	<a class="btn p28 spe1 blue view_profile" role="presentation" title="<?= __('Infos Générales') ?>"><?= __('Infos Générales') ?></a>-->


	<div class="btn lh24-28l h85 view_profile blue" role="presentation" title="<?= __('Infos Générales') ?> "><?= __('Infos Générales') ?> </div>

	<div class="btn  lh24-28l h85 edit_profile white" role="presentation" title="<?= __('Modifier') ?> <?= __('Mon Profil') ?>"><?= __('Modifier') ?> <br/><?= __('Mon Profil') ?></div>


	<?php if ($userRole == 'agent')
	    { ?>

    	<div class="btn  lh24-28l h85 edit_options white" role="presentation" title="<?= __('Modifier') ?> <?= __('mes options') ?>"><?= __('Modifier') ?> <br/><?= __('mes options') ?></div>

    	<div class="btn  lh24-28l h85 edit_presentation agent flex white" role="presentation" title="<?= __('Modifier') ?> <?= __('Mes photos profil') ?>  <?= __('et ma Présentation') ?>"><?= __('Mes photos profil') ?> <br/><?= __('et ma Présentation') ?></div>

<?php } ?>

    </div>




    <div class="content">

	<?php
	//echo $this->Session->flash();
	if ($userRole == 'agent')
	    {
	    $fields = ["lastname" => "nom", "firstname" => "prénom", "pseudo" => "pseudo",
	    "email" => "email",
	    "activity" => "mon activité",
	    "activity2" => "Info Activité suite",
	    "lang_id" => "langue principale de mon compte",
	    "indicatif_phone" => "indicatif téléphone", 
	    "phone_mobile" => "Numéro De Tel Mobile 1",
	    "phone_operator" => "Opérateur téléphonique",
	    "indicatif_phone2" => "indicatif téléphone 2", "phone_mobile2" => "Numéro De Tel Mobile 2",
	    "phone_operator4" => "Opérateur téléphonique",
	    "phone_number" => "Numéro de téléphone fixe 1",
	    "phone_operator3" => "Opérateur téléphonique",
	    "phone_number2" => "Numéro de téléphone fixe 2",
	    "phone_operator2" => "Opérateur téléphonique",
	    "birthdate" => "Date naissance",
	    "sexe" => "sexe",
	    "address" => "adresse", "postalcode" => "code postal", "city" => "ville", "country_id" => "Pays résidence",
	    "societe" => "Nom société",
	    "societe_number" => "Numéro registre société (si existant)",
	    "langs" => "Langues parlées",

	];

	$fields_mandatory = [
	    "lastname", "firstname", "pseudo", "email", "activity", "activity2", "lang",
	    "phone_mobile", "phone_operator", "lastname", "birthdate", "sexe", "address",
	    "postalcode", "city", "country_id"
	];

	$values = ["lastname" => "Test", "firstname" => "DG", "pseudo" => "James",
	    "email" => "degrefinance.enternaiment@protonmail.com", "indicatif_phone" => "971",
	    "phone_number" => "971 529740379",
	    "phone_operator" => "Equant France", "birthdate" => "23/04/10980", "sexe" => "1",
	    "address" => "460 chemin preysssac, Immeuble le baobab", "postalcode" => "82000",
	    "city" => "Montauban", "country_id" => "Tunisia",
	    
	    "activity" => "Photographe",
	    "activity2" => "Formation photo",
	    "lang_id" => "1",
	    "langs" => "1,2",
	    "indicatif_phone" => "+33", "phone_mobile" => "184146608",
	    "phone_operator3" => "Orange",
	    "indicatif_phone2" => "+33", "phone_mobile2" => "5664838333",
	    "phone_operator4" => "Orange",

	    "phone_number2" => "2165184282",
	    "phone_operator2" => "Orange",
	    "societe" => "Lorem Ipsum",
	    "societe_number" => "21700/R"
	    
	    ];
	    }
	else
	    {
	    $fields_mandatory = $fields = ["lastname" => "lastname", "firstname" => "firstname", "pseudo" => "pseudo",
	    "email" => "email", "indicatif_phone" => "indicatif phone", "phone_mobile" => "Numéro de téléphone mobile 1",
	    "phone_operator" => "phone operator", "birthdate" => "Date de naissance", "sexe" => "sexe",
	    "address" => "address", "postalcode" => "postal code", "city" => "city", "country_id" => "Pays de résidence"];

	    $fields_mandatory =  $fields = ["lastname" => "nom", "firstname" => "prénom", "pseudo" => "pseudo",
	    "email" => "email", "indicatif_phone" => "indicatif téléphone", "phone_mobile" => "Numéro De Tel Mobile 1",
	    "phone_operator" => "Opérateur téléphonique", "birthdate" => "Date de naissance",
	    "sexe" => "sexe",
	    "address" => "adresse", "postalcode" => "code postal", "city" => "ville", "country_id" => "Pays de résidence"];

	$values = ["lastname" => "Test", "firstname" => "DG", "pseudo" => "Majax",
	    "email" => "degrefinance.enternaiment@protonmail.com", "indicatif_phone" => "971",
	    "phone_number" => "971 529740379",
	    "phone_operator" => "Orange", "birthdate" => "23/04/10980", "sexe" => "1",
	    "address" => "460 chemin preysssac, Immeuble le baobab", "postalcode" => "82000",
	    "city" => "Montauban", "country_id" => "France"];
	    }
	

	

	$sexe = [0 => "N/A", 1 => __("Homme"), 2 => __("Femme")];
	?>

	<section class="view view_profile screen">

<?php

$this->set('userRole', $userRole);
$this->set('class_user_statu', $class_user_statu); 
$this->set('fields', $fields);
$this->set('fields_mandatory', $fields_mandatory);
$this->set('values', $values);

echo $this->element('Profil/view');

//echo $this->render('Profil/view');
?>

	</section>


<section class="edit  screen edit_profile">
<?php
$this->set('class_user_statu', $class_user_statu);
$this->set('userRole', $userRole);
$this->set('fields', $fields);
$this->set('values', $values);
echo $this->element('Profil/edit');

//echo $this->render('/Elements/Profil/view');
?>

	</section>

	<section class="edit_options screen">
<?php
$this->set('userRole', $userRole);
$this->set('class_user_statu', $class_user_statu);
$this->set('fields', $fields);
$this->set('values', $values);
echo $this->element('Profil/edit_options');

//echo $this->render('/Elements/Profil/view');
?>

	</section>

	<section class="edit_presentation screen">
<?php
$this->set('userRole', $userRole);
$this->set('class_user_statu', $class_user_statu);
$this->set('fields', $fields);
$this->set('values', $values);
echo $this->element('Profil/edit_presentation');

//echo $this->render('/Elements/Profil/view');
?>

	</section>

    </div>
</div>


<script>
    /*—————————————————————————————————
     PROFILE PAGE
    
    4 TOP BOUTONS + SCREENS
     —————————————————————————————————*/
    window.onload = function ()
    {

        var list_btn = ["view_profile", "edit_profile", "edit_options",
            "edit_presentation"]
        //var list_btn = $(".profile-page .btns .btn ");

        $(".profile-page .btns .btn,  .profile-page .screen.view .edit_profile").
                        on('click touchstart',function ()
                        {
                            //console.log("click",this);
                            for (x in list_btn)
                            {
                                cur_btn_class = list_btn[x]
                               // console.log("cur_btn_class",cur_btn_class);
                                if ($(this).hasClass(cur_btn_class))
                                {
                                    // if(!$(this).hasClass("underline") ) // pour le btn du bas en view profile screen
                                    $(".profile-page .btn." + cur_btn_class).
                                            addClass("blue").removeClass("white")
                                    $(".profile-page .screen."
                                            + cur_btn_class).fadeIn('fast')
                                } else
                                {
                                    $(".profile-page .btn." + cur_btn_class).
                                            removeClass(
                                            "blue").addClass("white")
                                    $(".profile-page .screen."
                                            + cur_btn_class).fadeOut('fast')
                                }
                            }





                        });




	let current_url = window.location;
	
	console.log("current_url",current_url);
	current_url = new String(current_url)
	
	let url_arr = current_url.split("/");
	console.log("url_arr",url_arr);
	let last = url_arr.length-1;
	let last_segment  = url_arr[last];
	
	
	console.log("last_segment",last_segment);
	
	if(last_segment=="view") $(".view_profile").click();
	if(last_segment=="edit") $(".edit_profile").click();
	if(last_segment=="options") $(".edit_options").click();
	if(last_segment=="presentation") 
	{
	    $(".edit_presentation").click();

	}


    };
</script>