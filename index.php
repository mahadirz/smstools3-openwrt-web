<?php
date_default_timezone_set("Asia/Kuala_Lumpur");
include_once "smsclass.php";
$sms = new SMS();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<!-- Le styles -->
<link href="bootstrap/css/bootstrap.css" rel="stylesheet">
<script type="text/javascript" src="bootstrap/js/jquery-1.10.2.min.js"></script>
<style>

th, td { padding: 5px; }


table { border-collapse: separate; border-spacing: 5px; } 
table { border-collapse: collapse; border-spacing: 5px; } 


th, td { vertical-align: top; }


table { margin: 0 auto; }


</style>
</head>
 
<body>

<div class="container">

<h3><span style="color: green;">SMS Tools 3 for OpenWrt</span></h3>
 
<!-------->
<div id="content">
<ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
<li class="active"><a href="#compose" data-toggle="tab">Compose</a></li>
<li><a href="#inbox" data-toggle="tab"><span id="inboxtotal"  class="badge pull-left"></span><span style="padding-left: 5px;">Inbox</span></a></li>
<li><a href="#sent" data-toggle="tab"><span id="senttotal" class="badge pull-left"></span><span style="padding-left: 5px;">Sent</span></a></li>
<li><a href="#templates" data-toggle="tab">SMS Templates</a></li>
</ul>
<div id="my-tab-content" class="tab-content">

<?php
//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
if (isset($_POST['compose'])) {
    
    //save to template
    if($_POST['save_template']){
        $return_call = $sms->compose_sms($_POST['msgtonumber'], $_POST['msgtext'],"templates");
        if ($return_call) {
        //success
        echo '<br><div class="alert alert-success" role="alert">
             The sms has been saved as template
             </div>';
        } 
        else  {
            //write to outgoing folder failed
            echo '<br><div class="alert alert-danger" role="alert">
             '.SMS::$last_error.'
             </div>';
        }
    }
    else{
        //call compose function
        $return_call = $sms->compose_sms($_POST['msgtonumber'], $_POST['msgtext']);
        if ($return_call) {
            //success
            echo '<br><div class="alert alert-success" role="alert">
             The sms file has been written to outgoing folder, It will appear on sent tab after the SMS Server tools successfully send it, Please refresh!
             </div>';
        } else  {
            //write to outgoing folder failed
            echo '<br><div class="alert alert-danger" role="alert">
             '.SMS::$last_error.'
             </div>';
        }
    }
    
    
} else if (isset($_POST["inbox_delete"])) {
    //delete sms in inbox
    foreach ($_POST["filename"] as $file) {
        $sms->delete_sms(1, $file);
    }
    
} else if (isset($_POST["sent_delete"])) {
    //delete sms in inbox
    foreach ($_POST["filename"] as $file) {
        $sms->delete_sms(2, $file);
    }
    
}
else if(isset($_POST['sms_templates'])){
    if(isset($_POST['delete'])){
        //delete sms in template
        foreach ($_POST["filename"] as $file) {
            $sms->delete_sms(0, $file);
        }
    }
    else{
        //echo "<pre>";
//            print_r($_POST);
//            echo "</pre>";
//            exit;
        
        foreach ($_POST["filename"] as $file) {
            
            
            $return_call = $sms->compose_sms($_POST['msg'][$file]['to'], $_POST['msg'][$file]['text']);
            if ($return_call) {
                //success
                echo '<br><div class="alert alert-success" role="alert">
                SMS saved to outgoing, once it is sent it will appear on sent box
                </div>';
            } 
            else  {
                //write to outgoing folder failed
                echo '<br><div class="alert alert-danger" role="alert">
                '.SMS::$last_error.'
                 </div>';
            }
        }
    }
}


$inbox = $sms->get_inbox();
$sent  = $sms->get_sent();

echo '<script> 
document.getElementById("senttotal").innerHTML = ' . count($sent) . ';
document.getElementById("inboxtotal").innerHTML = ' . count($inbox) . ';
var hash = window.location.hash;
$(".nav-tabs a[href=#sent]").tab("show") ;
</script>';
?>



<div class="tab-pane active" id="compose">
<h1>Compose New SMS</h1>

      <form id="smsform"  method="post" >
      <input type="hidden" name="compose" />
    <table  style="width:100%;">  
    
    <tr>
    <td><strong>Note:</strong></td>
    <td> <p>Default is for Malaysia number</p> </td>
    </tr>
    
      <tr>
        <td  ><strong>To Number: </strong></td>
        <td > <input type="text" id="msgtonumber" name="msgtonumber" placeholder="+60176784332" name="msgLen" onfocus="setbg('#d9ffd9',this.id);" onblur="setbg('#f0f5e6',this.id)"  size="30" maxlength="15"  /></td>
      </tr>    

      <tr>
        <td  ><strong>Message:</strong></td>
        <td ><textarea id="message" rows="4" cols="70"  name="msgtext" maxlength="160" 
        onfocus="setbg('#d9ffd9',this.id);" onblur="setbg('#f0f5e6',this.id)" 
        onKeyDown="textCounter(this.form.message,this.form.msgLen,160)" 
        onKeyUp="textCounter(this.form.message,this.form.msgLen,160)"></textarea><br />
        <input type="text" id="msgLen" name="msgLen"  size="2" maxlength="3" value="160" readonly />
        </td>
      </tr>          
      <tr>
        <td colspan="2" align="center">
        <input class="btn btn-primary" type="submit" id="submit" value="Send"  />
        <input class="btn btn-default" name="save_template" type="submit" id="submit" value="Save as template"  />
        </td>
      </tr>    </table></form>
</div>

<div class="tab-pane" id="inbox">
<h1>Inbox</h1>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading"></div>
  <div class="panel-body">
    <!-- Table -->
  <form method="POST" action="">
  <input type="hidden" name="inbox_delete" />
  <table class="table">
    <thead>
        <th width="2%"></th>
        <th width="18%">Date & Time</th>
        <th width="10%">From</th>
        <th width="70%">Text</th>
    </thead>
    <tbody>
        <?php
foreach ($inbox as $in) {
    echo '<tr>';
    echo '<td><input name="filename[]" value="' . $in["filename"] . '" type="checkbox"></td>';
    echo '<td>' . $in["datetime"] . '</td>';
    echo '<td>' . $in["from"] . '</td>';
    echo '<td>' . $in["text"] . '</td>';
    echo '</tr>';
}
?>
    </tbody>
        

  </table>
  <input class="btn btn-danger" type="submit" id="delete" value="Delete"  />
  </form>
  </div>

  
</div>
</div>

<div class="tab-pane" id="sent">
<h1>Sent</h1>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading"></div>
  <div class="panel-body">
    <!-- Table -->
  <form method="POST" action="">
  <input type="hidden" name="sent_delete" />
  <table class="table">
    <thead>
        <th width="2%"></th>
        <th width="18%">Date & Time</th>
        <th width="10%">To</th>
        <th width="70%">Text</th>
    </thead>
    <tbody>
        <?php
foreach ($sent as $se) {
    echo '<tr>';
    echo '<td><input name="filename[]" value="' . $se["filename"] . '" type="checkbox"></td>';
    echo '<td>' . $se["datetime"] . '</td>';
    echo '<td>' . $se["to"] . '</td>';
    echo '<td>' . $se["text"] . '</td>';
    echo '</tr>';
}
?>
    </tbody>
        

  </table>
  <input class="btn btn-danger" type="submit" id="delete" value="Delete"  />
  </form>
  </div>

  
</div>
</div>


<div class="tab-pane" id="templates">
<h1>SMS Templates</h1>
<div class="panel panel-default">
  <!-- Default panel contents -->
  <div class="panel-heading"></div>
  <div class="panel-body">
    <!-- Table -->
  <form method="POST" action="">
  <input type="hidden" name="sms_templates" />
  <table class="table">
    <thead>
        <th width="2%"></th>
        <th width="10%">To</th>
        <th width="88%">Text</th>
    </thead>
    <tbody>
        <?php
        $templates = $sms->get_templates();
        foreach ($templates as $tpl) {
            echo '<tr>';
            echo '<td><input name="filename[]" value="' . $tpl["filename"] . '" type="checkbox"></td>';
            echo '<input name="msg['.$tpl["filename"].'][to]" value="' . $tpl["to"] . '" type="hidden">';
            echo '<input name="msg['.$tpl["filename"].'][text]" value="' . $tpl["text"] . '" type="hidden">';
            echo '<td>' . $tpl["to"] . '</td>';
            echo '<td>' . $tpl["text"] . '</td>';
            echo '</tr>';
        }
        ?>
    </tbody>
        

  </table>
  <input class="btn btn-danger" type="submit" name="delete" value="Delete"  />
  <input class="btn btn-primary" type="submit" name="send" value="Send"  />
  </form>
  </div>

  
</div>
</div>


</div>
</div>
 
 
<p style="text-align:center; padding-top:20px"><span style="color: black;">&copy; 2014 <a href="http://madet.my">Mahadir Ahmad</a></span></p>
</div> <!-- container -->

<script>
jQuery(document).ready(function ($) {
$('#tabs').tab();
});
</script>
 
 
<script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>

<script type="text/javascript">
function textCounter(field,countfield,maxlimit){if(field.value.length>maxlimit){field.value=field.value.substring(0,maxlimit)}else{countfield.value=maxlimit-field.value.length}}
function setbg(color,id){document.getElementById(id).style.background=color}
</script>
 
</body>
</html>