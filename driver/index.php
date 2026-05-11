<?php 
require_once '../_config.php'; 
require_once '../_include.php';
 
 
if (isset ($_SESSION[$class->loginAdminSession]))
	session_unset($_SESSION[$class->loginAdminSession]);
	
$loginId = '';
$loginPassword = '';
$token = '';

if (isset($_GET)){
    
    if (!empty($_GET['username']))
        $loginId = $_GET['username'];
        
    if (!empty($_GET['token'])){
        $loginPassword = $class->generateStrongPassword();
        $token = $_GET['token'];
    }    
}   
    
$_POST['loginID'] = $loginId;
$_POST['loginPassword'] = $loginPassword;
$_POST['token'] = $token;

//$security->updateAvailableSecurityObject($security->oDbCon,3);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php echo  $class->loadSetting('companyName');  ?></title>  

<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-font-awesome.min.css">  
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>jquery-ui.min.css" />    
<link rel="stylesheet" href="<?php echo $class->adminCssPath; ?>bootstrap.css"/>
<link rel="stylesheet" href="<?php echo $class->adminCssPath; ?>bootstrapValidator.css"/>
<link rel="stylesheet" type="text/css" href="<?php echo $class->adminCssPath; ?>fe-1.1.css">  

<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-3.3.1.min.js"></script>   
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>jquery-ui.min.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>bootstrapValidator.js"></script>
<script type="text/javascript" src="<?php echo $class->defaultJsPath; ?>php-variables-1.2.min.js"></script>
 
	 
<script type="text/javascript"> 
	
	jQuery(document).ready(function(){ 
		      
		 $('#defaultForm')
			.bootstrapValidator({
				
				feedbackIcons: {
					valid: 'glyphicon glyphicon-ok',
					invalid: 'glyphicon glyphicon-remove',
					validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
                loginID: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.username['1'] 
                        },
                        stringLength: {
                            min: 5,
                            max: 30,
                            message: phpErrorMsg.username['3'] 
                        }, 
                        regexp: {
                            regexp: /^[a-zA-Z0-9_\.]+$/,
                            message:  phpErrorMsg.username['4'] 
                        }
                    }
                }, 
                loginPassword: {
                    validators: {
                        notEmpty: {
                            message: phpErrorMsg.password['1'] 
                        }
                    }
                }
            }
        })
        .on('success.form.bv', function(e) {
            // Prevent form submission
            e.preventDefault(); 
            var $form = $(e.target); 
            var bv = $form.data('bootstrapValidator');
             
             var btnLogin = $form.find("[name=btnLogin]");  
             btnLogin.prop('disabled', true);
             btnLogin.find(".loading-icon").show();


            // Use Ajax to submit form data
            $.post($form.attr('action'), $form.serialize(), function(result) {
				 
				$(".notification-msg").hide().fadeToggle("fast"); 
                
				if (!result.valid){
					$(".notification-msg").removeClass("bg-green-avocado").addClass("bg-red-cardinal"); 
                	$(".notification-msg").html(result.message);
					
                    if (result.useOTP)  
                        $(".login-slide-panel").animate({left: $(".login-slide-panel").width() / -2},500, function(){$("[name=authcode]").focus();});
                           
				}else{
                    
                    $(".notification-msg").hide();
					$(".notification-msg").removeClass("bg-red-cardinal");
                    $(".notification-msg").html("");
					if (result.message){ 
                        $(".notification-msg").addClass("bg-green-avocado"); 
                        $(".notification-msg").html(result.message);
                        $(".notification-msg").show();
                    }
                    
                    // kalo login gk pake OTP
                    if (result.useOTP) {
                        $(".login-slide-panel").animate({left: $(".login-slide-panel").width() / -2},500, function(){$("[name=authcode]").focus();});
                    }else{ 
                        $form[0].action = "jobList";
                        $form[0].submit();  
                    }
				}

                 btnLogin.prop('disabled', false);
                 btnLogin.find(".loading-icon").hide();
				
            }, 'json');
        });
        
        <?php if (!empty($loginId) && !empty($loginPassword)){ ?>
           $("[name=btnLogin]").click();
        <?php } ?> 
         
         
        $( ".icon-back" ).on('click', function() { 
            $(".login-slide-panel").animate({left: '0'});
        });
        
        
        
	});
			
</script>
</head>  
       
<?php
$profileImg = $class->loadSetting('companyLogo');  
$avatarStyle = (!empty($profileImg)) ? 'style="background-image:url(\'../phpthumb/phpThumb.php?src='.$class->phpThumbURLSrc .'setting/companyLogo/'.$profileImg.'&far=C&f=png&hash='.getPHPThumbHash($profileImg).'\')"' : '' ;
 
?>
    
<body style="background-color:#333; background-size:cover; background-repeat: no-repeat;  background-position: center;"> 
<div id="body-login"> 
    <div class="login-panel-background">
        <div class="avatar" <?php echo $avatarStyle ?>></div>  
        <div style="text-align:center; line-height:2em; margin-bottom:1em"><?php echo strtoupper(DOMAIN_NAME); ?></div> 
        <form id="defaultForm" method="post" class="form-horizontal" style="overflow:hidden" action="/admin/ajax-login.php">
            <?php echo $class->inputHidden('action'); ?>   
            <div class="notification-msg" style="text-align:center; margin:auto; margin-top:0.5em; margin-bottom:0.5em"></div>  
            <div class="login-slide-panel div-table" style="width:640px;  margin-top:15px; position:relative"> 
                <div class="div-table-row">
                    <div class="div-table-col" style="width:50%;"> 
                       <div class="div-table"  style="width:100%;">
                             <div class="div-table-row form-group"> 
                                <div class="div-table-col-5" style="padding:0" >
                                    <div class="col-lg-12" style="padding:0"> 
                                          <?php echo $class->inputText('loginID', array('etc'=> 'placeholder="'.$class->lang['username'].'"') );   ?> 
                                    </div>
                                </div> 
                            </div> 
                             <div class="div-table-row form-group"> 
                                <div class="div-table-col-5" style="padding:0; padding-top:10px" >
                                    <div class="col-lg-12" style="padding:0" > 
                                          <?php echo $class->inputPassword('loginPassword', array('etc'=> 'placeholder="'.$class->lang['password'].'"') );   ?>  
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                    <div class="div-table-col">
                        <div class="div-table" style="width:100%;">
                            <div class="div-table-row form-group"> 
                                <div class="div-table-col-5" >
                                    <div class="col-lg-12" style="padding:0"> 
                                        <?php echo $class->inputText('authcode', array('etc'=>'style="text-align:center"  tabindex="-1" placeholder="'.ucwords($class->lang['authenticationCode']).'"')); ?>
                                        <div class="icon-back"><i class="fas fa-chevron-left"></i></div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div> 
            </div> 
            <div style="padding-top:10px; width: 320px"><?php echo $class->inputSubmit('btnLogin', $class->lang['login'], array('etc' => 'style="width:100%"') ); ?></div>
        </form>  
        <div style="clear:both;"></div>
    </div>
</div> 
  
</body>
</html>