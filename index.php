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
</ul>
<div id="my-tab-content" class="tab-content">

<?php
//echo "<pre>";
//print_r($_POST);
//echo "</pre>";
if (isset($_POST['compose'])) {
    //call compose function
    $return_call = $sms->compose_sms($_POST['type'], $_POST['msgtonumber'], $_POST['msgtext']);
    if ($return_call == 1) {
        //success
        echo '<br><div class="alert alert-success" role="alert">
             The sms file has been written to outgoing folder, It will appear on sent tab after the SMS Server tools successfully send it, Please refresh!
             </div>';
    } else if ($return_call == 0) {
        //write to outgoing folder failed
        echo '<br><div class="alert alert-danger" role="alert">
             Attempt to write sms in outgoing folder failed!
             </div>';
    } else {
        //text length exceeded
        echo '<br><div class="alert alert-danger" role="alert">
             The text length exceed 160 characters!
             </div>';
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
    <td><strong>Type: </strong></td>
    <td> <select name="type">
  <option value="1">Normal Number</option>
  <option value="2">Short Number</option>
</select> </td>
    </tr>
    
      <tr>
        <td  ><strong>To Number: </strong></td>
        <td > <input type="text" id="msgtonumber" name="msgtonumber" value="+6" name="msgLen" onfocus="setbg('#d9ffd9',this.id);" onblur="setbg('#f0f5e6',this.id)"  size="50" maxlength="10"  /></td>
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
        <td colspan="2" align="center"><input class="btn btn-primary" type="submit" id="submit" value="Send"  /></td>
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
        <th width="5%"></th>
        <th width="15%">Date & Time</th>
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
        <th width="5%"></th>
        <th width="15%">Date & Time</th>
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