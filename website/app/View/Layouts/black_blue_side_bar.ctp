<?php 
$this->extend('black_blue_root');
?>
<?php 


$this->set('userRole', $userRole);
	
if ( $params['controller'] != "home" ) { ?>	    
<aside class="left-column">
 <?php	

 echo $this->element('vignette'); ?>

	<menu id="side-menu" class="navbar-myaccount">

<?php	


echo $this->element('vignette'); 		    

    if ($userRole == 'agent') echo $this->FrontBlock->getAgentAlertes();
    //echo $this->FrontBlock->getAccountAlertes(); 
    //echo $this->FrontBlock->getHeaderUserBlock();

    if ($userRole == 'agent')
    echo $this->element('side_menu_agent'); 
    else 
    echo $this->element('side_menu'); 
?> 
    <div id="menu_closer"></div>       
	</menu> 

</aside>

	     <section class="content">

		<?php //echo $this->Session->flash(); ?>

			<?php 
				if(!$this->Session->read('Auth.User')){// && $this->request->isMobile()
					switch ( $params['controller'] ) {
						case 'home':
							echo"<br>home";
							echo $this->FrontBlock->getSliderMobile();
						break;
					}
				}
		?>

		<?php 

//			$this->set('css_user_statu', $css_user_statu);
//			$this->set('userRole', $userRole);
		$this->assign('userRole', $userRole);

		//echo $this->render('content');
		/*
		$this->start('content');
		render your 'nav_view' stuff here
		echo $this->element('content');
		$this->end();
		 */


		echo $this->fetch('content'); 


		?>

	    </section>
	    <?php }
	    else echo $this->fetch('content'); ?>


