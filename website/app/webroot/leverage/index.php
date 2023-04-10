
<?php
    if (isset($_POST["submit"])) {
        $name = $_POST['name'];
        $lname = $_POST['lname'];
        $email = $_POST['email'];
        $message = $_POST['message'];
        $from = $_POST['email'];
        $to = 'contact@leverage.com'; 
        $subject = $_POST['subject'];
		 $headers = 'From: '.$from . "\r\n" .
		 'Reply-To: '.$from . "\r\n" .
		 'X-Mailer: PHP/' . phpversion();
        
        $body ="From: $name $lname\n Subject: $subject \n E-Mail: $email\n Message:\n $message";

        // Check if name has been entered
        if (!$_POST['name']) {
            $errName = 'Please enter your first name';
        }

        // Check if name has been entered
        if (!$_POST['lname']) {
            $errName = 'Please enter your last name';
        }
        
        // Check if email has been entered and is valid
        if (!$_POST['email'] || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errEmail = 'Please enter a valid email address';
        }
        
        //Check if message has been entered
        if (!$_POST['message']) {
            $errMessage = 'Please enter your message';
        }


// If there are no errors, send the email
if (!$errName && !$errEmail && !$errMessage) {
    if (mail ($to, $subject, $body, $headers)) {
        $result='<div class="alert alert-success">Thank You! We will be in touch</div>';
    } else {
        $result='<div class="alert alert-danger">Sorry there was an error sending your message. Please try again later.</div>';
    }
}
    }
?>



<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="LVRG">
    <meta name="author" content="LVRG">

    <title>LVRG</title>

    <!-- Bootstrap Core CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css?<?=date('Y-m-d H:i') ?>" type="text/css">
    <link rel="stylesheet" href="font-awesome/css/font-awesome.css" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">


</head>



<body id="page-top">

    <nav id="mainNav" class="navbar navbar-default navbar-fixed-top">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand page-scroll" href="#page-top"><img src="img/logo.jpg" alt="LVRG" class="imglogo" /></a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a class="page-scroll" href="#about">About</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#services">Prodcuts & Services</a>
                    </li>
                    <li>
                        <a class="page-scroll" href="#contact">Contact</a>
                    </li>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container-fluid -->
    </nav>

 <header>
    <div id="main-carousel" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <!-- <ol class="carousel-indicators">
    <li data-target="#main-carousel" data-slide-to="0" class="active"></li>
    <li data-target="#main-carousel" data-slide-to="1"></li>
    <li data-target="#main-carousel" data-slide-to="2"></li>
    <li data-target="#main-carousel" data-slide-to="3"></li>
  </ol> -->

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    <div class="item slider slider-1 active">
        <div class="grey-bg"></div>
      <!-- <img src="img/slider.jpg" alt="..."> -->
      <div class="carousel-caption">
        <h2>LEVERAGE</h2>
        <p class="hidden-xs">LVRG is a leading provider of IT, telecommunications services and multimedia solutions. </p>
      </div>
    </div>
  </div>
</div>
</header>

    <section class="bg-white pb0" id="about">
        <div class="container about-container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading">About us</h2>
                   
                    <p>Today, corporates and individuals recognize that we operate in a connected world where media, providers and customers have ceased to make a distinction between on and offline. </p>

<p>This has led to opportunities in building digital capabilities to help people communicate with the right contacts and audiences, on the right channels, at the right time, while underpinning every personal objectives. All of this by redefining how we engage with media, technologies and entertainment.</p>

<p>LVGR Limited. builds human-first technology that puts our consumer and business customers first.</p>

<p>We deliver, and continue to invent new ways to deliver, captivating solutions that affect how we connect and interact with one another and as a collective, where newer generations are leading the evolution in order to build something bigger than platforms and infrastructures.</p>

                </div>
            </div>
        </div>


<div id="carousel" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <!-- <ol class="carousel-indicators">
    <li data-target="#carousel" data-slide-to="0" class="active"></li>
    <li data-target="#carousel" data-slide-to="1"></li>
    <li data-target="#carousel" data-slide-to="2"></li>
    <li data-target="#carousel" data-slide-to="3"></li>
  </ol> -->

  <!-- Wrapper for slides -->
  <div class="carousel-inner" role="listbox">
    <div class="item slider slider-1 active">
        <div class="grey-bg"></div>
      <!-- <img src="img/slider.jpg" alt="..."> -->
      <div class="carousel-caption">
       
        <p>LVRG has created and developed a large number of highly diversified companies, all gathered under a unique umbrella. All businesses experience ongoing growth in the fields of technology, media, entertainment, personal mentorship, workshops and personal development.</p>
      </div>
    </div>

     <div class="item slider slider-2">
        <div class="grey-bg"></div>
     
      <div class="carousel-caption">
        
        <p>LVRG has created and developed a large number of highly diversified companies, all gathered under a unique umbrella. All businesses experience ongoing growth in the fields of technology, media, entertainment, personal mentorship, workshops and personal development.</p>
      </div>
    </div>

    <div class="item slider slider-3">
        <div class="grey-bg"></div>
     
      <div class="carousel-caption">
        
        <p>LVRG has created and developed a large number of highly diversified companies, all gathered under a unique umbrella. All businesses experience ongoing growth in the fields of technology, media, entertainment, personal mentorship, workshops and personal development.</p>
      </div>
    </div>

    
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#carousel" role="button" data-slide="prev">
    <i class="fa fa-angle-left glyphicon-chevron-left fa-2x"></i>
  </a>
  <a class="right carousel-control" href="#carousel" role="button" data-slide="next">
    <i class="fa fa-angle-right glyphicon-chevron-right fa-2x"></i>
  </a>
</div>

    </section>

    <section class="bg-white" id="services">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                   <!-- <h2 class="section-heading">Our Services</h2>-->
                    
                    <p style="text-align: justify">At LVRG, interaction is at the heart of our mission to connect people with their world from virtually anywhere. We combine premium telecommunication, entertainment and multimedia services to bring our customers greater value.</p>

                    <hr/>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row service-box-row ">
                <div class="col-lg-2 col-xs-2 col-lg-offset-1">
                    <img src="img/evaluation.jpg" class="img-responsive"  alt="evaluation" />
                </div>
                <div class="col-lg-8 col-xs-10">
                    <div class="service-box">
                        <h2>Products Strategy</h2>
                        <p style="text-align: justify">LVRG offers tailored products and platforms through our highly secure business solutions. All products use an integrated approach to serve business and consumers needs.<br />With LVRG solutions, all users will have access to fast, highly secure and reliable communications whenever they need them.</p>
                   </div>
                </div>
            </div><!--row End-->

            <div class="row service-box-row ">
                <div class="col-lg-2 col-xs-2 col-lg-offset-1">
                    <img src="img/sponsorshipsales.jpg" class="img-responsive" alt="sponsorship sales" />
                </div>
                <div class="col-lg-8 col-xs-10">
                    <div class="service-box">
                        <h2>Digital & Interactive Strategy</h2>
                        <p style="text-align: justify">LVRG supplies services in the field of international e-business, including the provision of on-line services platforms such as voice, email, chat services, text messaging, mobile apps etc.<br />This enables LVRG to cover all areas of interaction with an omnichannel view of all digital interactions.</p>
                   </div>
                </div>
            </div><!--row End-->

            <div class="row service-box-row ">
                <div class="col-lg-2 col-xs-2 col-lg-offset-1">
                    <img src="img/marketresearch.jpg" class="img-responsive" alt="market research" />
                </div>
                <div class="col-lg-8 col-xs-10">
                    <div class="service-box">
                        <h2>Content & Distribution Strategy</h2>
                        <p style="text-align: justify">Our operations are focused on the development and integration of advanced information and telecommunication technologies, with an emphasis on solutions for customer engagement and interaction across multiple communication channels and for transmitting multimedia content through the internet.<br />Reaching right contacts and audiences is about more than just targeting influencers. LVRG has compelling content for its various businesses to engage audiences in the manner its audiences prefer to communicate. <br />Through our proprietary network we have in-house engineering and development capabilities to help businesses manage their digital presence quickly.</p>
                   </div>
                </div>
            </div><!--row End-->

           <!--row End-->
        </div>

    <div class="section-gap">
        <div class="container">
            <hr/>
        </div>
    </div><!--section-gap END-->


    </section>

  <!--  <div class="section-gap">
        <div class="container">
            <hr/>
        </div>
    </div><!--section-gap END-->

    <section id="contact">
         <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <h2 class="section-heading">Contact Us</h2>
                </div>
            </div>
        </div>

         <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12">
                    <div class="text-center">
                        <h4>LVRG</h4>
                        <p>Email: <a href="mailto:contact@leverage.com">contact@leverage.com</a></p>
                    </div>
                </div>
                
            </div><!--row End-->
        </div>

        <div class="container">
            <form class="contact-form" role="form" method="post" action="">
                        
                            <div class="row">
                            <div class="col-sm-12">
                                <?php echo $result; ?>  
                            </div>
                        </div>

                          <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <label>First Name</label>
                                    <input type="text" class="form-control" id="name" name="name" placeholder="First Name" value="<?php echo htmlspecialchars($_POST['name']); ?>" required>
                            <?php echo "<p class='text-danger'>$errName</p>";?>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6">
                                    <label>Last Name</label>
                                    <input type="text" class="form-control" id="lname" name="lname" placeholder="Last Name" value="<?php echo htmlspecialchars($_POST['lname']); ?>" required>
                            <?php echo "<p class='text-danger'>$errlName</p>";?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <label>Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="example@domain.com" value="<?php echo htmlspecialchars($_POST['email']); ?>" required>
                            <?php echo "<p class='text-danger'>$errEmail</p>";?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <label>Subject</label>
                                    <input class="form-control" name="subject" placeholder="Subject" type="text" required="" value="<?php echo htmlspecialchars($_POST['subject']); ?>">
                                    <?php echo "<p class='text-danger'>$errSubject</p>";?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <label>Messages</label>
                                   
                                    <textarea style="resize:vertical;" class="form-control" rows="4" name="message"><?php echo htmlspecialchars($_POST['message']);?></textarea>
                            <?php echo "<p class='text-danger'>$errMessage</p>";?>
                                </div>
                            </div>

                            <div class="row">
                            <div class="col-xs-12">
                                <div class="g-recaptcha" data-sitekey="6LeVPKIUAAAAAKILkobFI0Ba9_-NxG1-49sUz6VQ"></div>
                                <!-- <a href="#" class="btn btn-primary">Submit</a> -->
                                <input id="submit" name="submit" type="submit" value="Submit" class="btn btn-default">
                            </div>
                            </div>
            </form>


        </div>

    </section>


    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="js/bootstrap.min.js"></script>

    <!-- Plugin JavaScript -->
    <script src="js/jquery.easing.min.js"></script>
    <script src="js/jquery.fittext.js"></script>
    <script src="js/wow.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="js/theme.js"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>

</body>

</html>
