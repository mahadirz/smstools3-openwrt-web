<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SMS Tools 3 - Login</title>


    <link rel="stylesheet" href="{$publicRootPath}assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="{$publicRootPath}assets/css/custom.css">

</head>

<body>

<div class="container">



    <div class="row" style="margin-top: 5%">
        <div class="col-md-4 col-md-offset-4">
            <div class="row">
                {if $flashMessages->hasMessages('danger')}
                    {$flashMessages->display('danger',true,true)}
                {/if}
            </div>
        </div>
    </div>

        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default" style="margin-top: 5px">

                    <div class="panel-heading">
                        <h3 class="panel-title">Login</h3>
                    </div>
                    <div class="panel-body" >
                        <form method="post" role="form">
                            <fieldset>
                                <div class="form-group">
                                    <input name="username" class="form-control" placeholder="Username"  type="text" autofocus>
                                </div>
                                <div class="form-group">
                                    <input name="password" class="form-control" placeholder="Password" type="password" value="">
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input name="remember" type="checkbox" value="Remember Me">Remember Me
                                    </label>
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <input type="submit" value="Login" class="btn btn-lg btn-success btn-block">
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-md-offset-4">
            <div class="row">
                <p style="padding-left: 20%">
                    SMS Tools 3 Web by <a href="http://madet.my">Mahadir Ahmad<a/>
                </p>
            </div>
        </div>
    </div>

<script src="{$publicRootPath}assets/bootstrap/js/jquery.min.js"></script>
<script src="{$publicRootPath}assets/bootstrap/js/bootstrap.min.js"></script>

</body>

</html>
