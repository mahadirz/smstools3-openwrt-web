{extends file="master.tpl"}
{block name=title}Send USSD{/block}

{block name=panelBody}

    <div id="loading"></div>

    <form id="ussd" method="post">

        <table style="width:100%;">




            <tr>
                <td >
                    <textarea readonly id="message" rows="10" cols="80" name="result">

                    </textarea>
                </td>

            </tr>

            <tr>
                <td style="padding-top: 10px">
                    <strong>USSD Code: </strong>
                    <input type="text" name="command" id="command" placeholder="*124#">
                    <input class="btn btn-primary" type="button" id="send" value="Send"/>
                </td>

            </tr>

        </table>
    </form>
{/block}



{block name=footer}
    <script type="text/javascript">

        $('#send').on('click', function(e){
            var command = encodeURIComponent($('#command').val());

            if(command == "")
            {
                alert('Please enter USSD code');
                return;
            }

            $.ajax({
                method: "GET",
                url: "index.php?q=api/ussd&command="+command,
                beforeSend: function( xhr ) {
                    $('#ussd').hide();
                    $('#loading').html('<img width="128px" height="128px" src="{$publicRootPath}assets/images/loading.gif">')
                }

            })
                    .done(function( msg ) {
                        $('#ussd').show();
                        $('#loading').html('');
                        //console.log(msg );

                        if(msg.success)
                        {
                            $('#message').val(msg.payload.message);
                        }
                        else
                        {

                        }

                    });

        });


    </script>
{/block}