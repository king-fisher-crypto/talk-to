<?php
    echo $this->Html->script('/theme/default/js/nx_login_modal');
    echo $this->Form->create('User', array('action' => 'subscribe', 'nobootstrap' => 1,'class' => 'con-subscribe', 'default' => 1, 'id' => 'ins_modal',
                                           'inputDefaults' => array(
                                               'class' => 'form-control'
                                           )));

  
?>
   				  <div class="row2" style="display:inline-block;width:100%"><div class="form-group" data-wow-delay="0.4s">
				   <!-- <label for="" class="col-sm-12 col-md-4 control-label" style="padding-left: 10px;"><?php echo __('Prénom ou pseudo') ?> <span class="star-condition">*</span></label>-->
				    <div class="col-sm-12">
				      <input type="text" class="form-control" id="UserFirstname" name="data[User][firstname]" placeholder="<?php echo __('Prénom ou pseudo *') ?>" required value="<?php echo $firstname; ?>">
				    </div>
					  </div></div>

				  <div class="row2" style="display:inline-block;width:100%"><div class="form-group" data-wow-delay="0.4s">
				   <!-- <label for="" class="col-sm-12 col-md-4 control-label"><?php echo __('Email') ?> <span class="star-condition">*</span></label>-->
				    <div class="col-sm-12">
				      <input type="email" class="form-control" id="UserEmailSubscribe"  name="data[User][email_subscribe]" placeholder="<?php echo __('Email *') ?>" required value="<?php echo $email; ?>">
				    </div>
				  </div></div>

				  <div class="row2" style="display:inline-block;width:100%"><div class="form-group" data-wow-delay="0.4s">
				   <!-- <label for="" class="col-sm-12 col-md-4 control-label"><?php echo __('Confirmez votre Email') ?> <span class="star-condition">*</span></label>-->
				    <div class="col-sm-12">
				      <input type="text" class="form-control" id="UserEmail2" name="data[User][email2]" placeholder="<?php echo __('Confirmez votre Email *') ?>" required value="<?php echo $email2; ?>">
				    </div>
				  </div></div> 

				  <div class="row2" style="display:inline-block;width:100%"><div class="form-group" data-wow-delay="0.4s">
				   <!-- <label for="" class="col-sm-12 col-md-4 control-label"><?php echo __('Mot de passe') ?> <span class="star-condition">*</span></label>-->
				    <div class="col-sm-12">
				      <input type="password" class="form-control" id="UserPasswdSubscribe" name="data[User][passwd_subscribe]" placeholder="<?php echo __('Mot de passe *') ?>" required>
				      <span class="help"><?php echo __('(8 caractères min)') ?></span>
				    </div>
				  </div></div>



				  <div class="row2" style="display:inline-block;width:100%"><div class="form-group " data-wow-delay="0.4s">
				    <!--<label for="" class="col-sm-12 col-md-4 control-label"> <span class="star-condition">*</span></label>-->
				    <div class="col-sm-12">
				      <select class="form-control" id="UserCountryId" name="data[User][country_id]" required>
				      	<option value=""><?php echo __('Pays *') ?></option>
						  <?php 
						  /*	foreach( $select_countries as $key => $opt){
								$selected = '';
								if($key == $country)$selected = 'selected';
								echo '<option value="'.$key.'" '.$selected.'>'.$opt.'</option>';
								
							}*/
	
	$domain = $_SERVER['SERVER_NAME'];
	$params = $this->request->params;
	$dbb_r = new DATABASE_CONFIG();
	$dbb_head = $dbb_r->default;
	$mysqli_head = new mysqli($dbb_head['host'], $dbb_head['login'], $dbb_head['password'], $dbb_head['database']);
		
	$result_head = $mysqli_head->query("SELECT id from domains where domain= '{$domain}'");
	$row_head = $result_head->fetch_array(MYSQLI_ASSOC);
	$current_id_domain = $row_head['id'];
	
	$select_1 = '';	if($current_id_domain == 19 )$select_1 = ' selected '; 
	$select_2 = '';if($current_id_domain == 11 )$select_1 = ' selected ';
	$select_3 = '';if($current_id_domain == 13 )$select_1 = ' selected ';
	$select_4 = '';if($current_id_domain == 22 )$select_1 = ' selected ';
	$select_5 = '';if($current_id_domain == 29 )$select_1 = ' selected ';
					  
	echo '<option value="1" '.$select_1.'>France</option>';
	echo '<option value="2" '.$select_2.'>Belgique</option>';
	echo '<option value="3" '.$select_3.'>Suisse</option>';
	echo '<option value="4" '.$select_4.'>Luxembourg</option>';
	echo '<option value="5" '.$select_5.'>Canada</option>';
	
	
	echo '<option value="6" >Allemagne</option><option value="7" >Andorra</option><option value="8" >Angola</option><option value="9" >Anguilla</option><option value="10" >Antarctica</option><option value="11" >Antigua and Barbuda</option><option value="12" >Argentina</option><option value="13" >Armenia</option><option value="14" >Aruba</option><option value="15" >Australia</option><option value="16" >Austria</option><option value="17" >Azerbaijan</option><option value="18" >Bahamas</option><option value="19" >Bahrain</option><option value="20" >Bangladesh</option><option value="21" >Barbados</option><option value="22" >Belarus</option><option value="23" >Belize</option><option value="24" >Benin</option><option value="25" >Bermuda</option><option value="26" >Bhutan</option><option value="27" >Bolivia</option><option value="28" >Bosnia and Herzegowina</option><option value="29" >Bouvet Island</option><option value="30" >Brazil</option><option value="31" >Bulgaria</option><option value="32" >Burkina Faso</option><option value="33" >Burundi</option><option value="34" >Cambodia</option><option value="35" >Cameroon</option><option value="36" >Cape Verde</option><option value="37" >Cayman Islands</option><option value="38" >Chad</option><option value="39" >Chile</option><option value="40" >China</option><option value="41" >Colombia</option><option value="42" >Comoros</option><option value="43" >Congo</option><option value="44" >Costa Rica</option><option value="45" >Cote D\'Ivoire</option><option value="46" >Croatia</option><option value="47" >Cuba</option><option value="48" >Cyprus</option><option value="49" >Czech Republic</option><option value="50" >Denmark</option><option value="51" >Djibouti</option><option value="52" >Dominica</option><option value="53" >Dominican Republic</option><option value="54" >East Timor</option><option value="55" >Ecuador</option><option value="56" >Egypt</option><option value="57" >El Salvador</option><option value="58" >Equatorial Guinea</option><option value="59" >Eritrea</option><option value="60" >Espagne</option><option value="61" >Estonia</option><option value="62" >Ethiopia</option><option value="63" >Fiji</option><option value="64" >Finland</option><option value="65" >French Guiana</option><option value="66" >French Polynesia</option><option value="67" >French Southern Territories</option><option value="68" >Gabon</option><option value="69" >Gambia</option><option value="70" >Georgia</option><option value="71" >Ghana</option><option value="72" >Gibraltar</option><option value="73" >Greece</option><option value="74" >Greenland</option><option value="75" >Grenada</option><option value="76" >Guadeloupe</option><option value="77" >Guam</option><option value="78" >Guatemala</option><option value="79" >Guinea</option><option value="80" >Guinea-bissau</option><option value="81" >Guyana</option><option value="82" >Haiti</option><option value="83" >Honduras</option><option value="84" >Hong Kong</option><option value="85" >Hungary</option><option value="86" >Iceland</option><option value="87" >India</option><option value="88" >Indonesia</option><option value="89" >Iraq</option><option value="90" >Ireland</option><option value="91" >Israel</option><option value="92" >Italie</option><option value="93" >Jamaica</option><option value="94" >Japan</option><option value="95" >Jordan</option><option value="96" >Kazakhstan</option><option value="97" >Kenya</option><option value="98" >Kiribati</option><option value="99" >Lebanon</option><option value="100" >Lesotho</option><option value="101" >Liberia</option><option value="102" >Liechtenstein</option><option value="103" >Lithuania</option><option value="104" >Macau</option><option value="105" >Madagascar</option><option value="106" >Malawi</option><option value="107" >Malaysia</option><option value="108" >Maldives</option><option value="109" >Mali</option><option value="110" >Malta</option><option value="111" >Marshall Islands</option><option value="112" >Martinique</option><option value="113" >Mauritania</option><option value="114" >Mauritius</option><option value="115" >Mayotte</option><option value="116" >Mexico</option><option value="117" >Monaco</option><option value="118" >Mongolia</option><option value="119" >Montserrat</option><option value="120" >Morocco</option><option value="121" >Mozambique</option><option value="122" >Myanmar</option><option value="123" >Namibia</option><option value="124" >Nauru</option><option value="125" >Nepal</option><option value="126" >Netherlands</option><option value="127" >Netherlands Antilles</option><option value="128" >New Caledonia</option><option value="129" >New Zealand</option><option value="130" >Nicaragua</option><option value="131" >Niger</option><option value="132" >Nigeria</option><option value="133" >Niue</option><option value="134" >Norfolk Island</option><option value="135" >Norway</option><option value="136" >Oman</option><option value="137" >Pakistan</option><option value="138" >Palau</option><option value="139" >Panama</option><option value="140" >Paraguay</option><option value="141" >Peru</option><option value="142" >Philippines</option><option value="143" >Pitcairn</option><option value="144" >Poland</option><option value="145" >Portugal</option><option value="146" >Puerto Rico</option><option value="147" >Qatar</option><option value="148" >Reunion</option><option value="149" >Romania</option><option value="150" >Russian Federation</option><option value="151" >Rwanda</option><option value="152" >Saint Lucia</option><option value="153" >Saint-Vincent-et-les-Grenadines</option><option value="154" >Samoa</option><option value="155" >San Marino</option><option value="156" >Saudi Arabia</option><option value="157" >Senegal</option><option value="158" >Seychelles</option><option value="159" >Sierra Leone</option><option value="160" >Singapore</option><option value="161" >Slovakia (Slovak Republic)</option><option value="162" >Slovenia</option><option value="163" >Solomon Islands</option><option value="164" >Somalia</option><option value="165" >South Africa</option><option value="166" >Sri Lanka</option><option value="167" >St. Helena</option><option value="168" >Sudan</option><option value="169" >Suriname</option><option value="170" >Swaziland</option><option value="171" >Sweden</option><option value="172" >Taiwan</option><option value="173" >Tajikistan</option><option value="174" >Tanzania United Republic of</option><option value="175" >Thailand</option><option value="176" >Togo</option><option value="177" >Tokelau</option><option value="178" >Tonga</option><option value="179" >Trinidad and Tobago</option><option value="180" >Tunisia</option><option value="181" >Turkey</option><option value="182" >Turkmenistan</option><option value="183" >Uganda</option><option value="184" >Ukraine</option><option value="185" >United Arab Emirates</option><option value="186" >United Kingdom</option><option value="187" >United States</option><option value="188" >Uruguay</option><option value="189" >Uzbekistan</option><option value="190" >Vanuatu</option><option value="191" >Venezuela</option><option value="192" >Viet Nam</option><option value="193" >Western Sahara</option><option value="194" >Yemen</option><option value="195" >Yugoslavia</option><option value="196" >Zaire</option><option value="197" >Zambia</option><option value="198" >Zimbabwe</option><option value="199" >Europe </option>';
						  
						  ?>
						</select>
				    </div>
				  </div></div>
					

				  <div class="row2"><div class="form-group" data-wow-delay="0.4s">
				    <div class="col-sm-12"><!-- col-md-offset-4 col-md-7 -->
				      <div class="checkbox">
				        <label>
				          <input type="checkbox" id="UserOptin" value="1" name="data[User][optin]">  <span></span><?=__('Je souhaite recevoir les offres exclusives de Spiriteo') ?>
				        </label>
				      </div>

				      <div class="checkbox">
				        <label>
				          <input type="checkbox" id="UserCgu" value="1" name="data[User][cgu]">  <span></span><?=__('J\'ai lu et j\'approuve sans réserve') ?> <?php echo $this->FrontBlock->getPageLink(1, array('target' => '_blank', 'class' => 'nx_openinlightbox2', 'style' => 'text-decoration:underline'), __('les conditions générales d\'utilisation')) ?>
				        </label>
				      </div>

				    </div>
				  </div></div>

    <div class="connect-footer text-center">
        <ul class="list-inline">
            <?php
                echo '<li class="login-button-li">'.$this->Form->button(__('S\'inscrire'),array('type' => 'submit', 'class' => 'btn btn-pink btn-connect-popup')).'</li>';

               /* echo '<li class="">'.__(' ou ').'</li>';

                echo '<li class="subscribe-button-li">'.$this->Html->link(
                    __('S\'inscrire'),
                    array('controller' => 'users', 'action' => 'subscribe'),
                    array('class' => 'btn btn-pink btn-connect-popup')
                ).'</li>';*/
            ?>
        </ul>
        <div class="text-bottom-form text-center" style="margin-bottom:20px;">
            <!--<a class="pas-links" title="Nouveau?" data-placement="top" data-toggle="tooltip" href="/users/subscribe" data-original-title="Nouveau?">Vous n'êtes pas inscrit ? </a>-->
            <a class="ins-links" title="Creez un compte" data-placement="top" data-toggle="tooltip" href="/users/login" data-original-title="Creez un compte"><?=__('Déjà inscrit ? Connectez-vous !') ?></a>
            </div>
    </div>
<?php
    echo $this->Form->end();