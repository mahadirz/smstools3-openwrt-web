<!DOCTYPE html>
<html lang="en">
<head>
    <title>SMS Tools3 Web </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{$publicRootPath}assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="{$publicRootPath}assets/css/custom.css">
    {block name=header}{/block}



</head>
<body>

<nav class="navbar navbar-inverse navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <a class="navbar-brand" href="#">SMS Tools3</a>
        </div>
        <div>
            <ul class="nav navbar-nav">
                <li {if $menu == "inbox"}class="active"{/if}>
                    <a href="index.php?q=sms/inbox"><span class="badge pull-left" id="inboxtotal">{$totalInbox}</span><span style="padding-left: 5px;">Inbox</span></a>
                </li>

                <li {if $menu == "sent"}class="active"{/if}>
                    <a href="index.php?q=sms/sent"><span class="badge pull-left" id="senttotal">{$totalSent}</span><span style="padding-left: 5px;">Sent</span></a>
                </li>

                <li {if $menu == "outgoing"}class="active"{/if}>
                    <a href="index.php?q=sms/outgoing">
                        {if $totalOutgoing > 0}
                            <span class="outgoing-badge pull-left" id="outgoingtotal">{$totalOutgoing}</span><span style="padding-left: 5px;">Outgoing</span>
                        {else}
                            Outgoing
                        {/if}
                    </a>
                </li>

                <li {if $menu == "compose"}class="active"{/if}>
                    <a href="index.php?q=sms/compose">Compose</a>
                </li>

                <li {if $menu == "ussd"}class="active"{/if}>
                    <a href="index.php?q=sms/ussd">USSD</a>
                </li>

                <li {if $menu == "templates"}class="active"{/if}>
                    <a href="index.php?q=sms/templates">Templates</a>
                </li>

                <li>
                    <a href="index.php?q=logout">Logout</a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<div style="padding:5% 2% " class="container-fluid">


    <div class="row">
        <div class="col-sm-12" >

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{block name=title}default title{/block}</h3>
                </div>
                <div class="panel-body">
                   {block name=panelBody}{/block}
                </div>
            </div>

        </div>
    </div>
</div>

<div style="text-align:center; padding-top:20px;padding-bottom: 10px">
    <p>
        <span style="color: black;">&copy; 2014 - {$smarty.now|date_format:"%Y"} <a href="http://madet.my">Mahadir Ahmad</a></span>
    </p>
    <p>
        <span style="font-size: smaller">Page generated in {$performance->getExecutionTime()}s with {$performance->getMemoryUsage()} memory usages</span>
    </p>
</div>

<script src="{$publicRootPath}assets/bootstrap/js/jquery.min.js"></script>
<script src="{$publicRootPath}assets/bootstrap/js/bootstrap.min.js"></script>

{block name=footer}{/block}

</body>
</html>
