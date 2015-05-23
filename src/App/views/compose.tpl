{extends file="master.tpl"}
{block name=title}Compose New SMS{/block}

{block name=panelBody}

    {if $flashMessages->hasMessages('success')}
        {$flashMessages->display('success')}
    {/if}

    <form id="smsform" method="post">
        <input type="hidden" name="compose"/>
        <table style="width:100%;">

            <tr>
                <td><strong>Note:</strong></td>
                <td><p>Default is (+60) Malaysia number</p></td>
            </tr>

            <tr>
                <td><strong>To Number: </strong></td>
                <td><input type="text" id="msgtonumber" name="msgtonumber" placeholder="+60176784332" name="msgLen"
                           onfocus="setbg('#d9ffd9',this.id);" onblur="setbg('#f0f5e6',this.id)" size="30"
                           maxlength="15"/></td>
            </tr>

            <tr>
                <td><strong>Message:</strong></td>
                <td><textarea id="message" rows="4" cols="70" name="msgtext" maxlength="160"
                              onfocus="setbg('#d9ffd9',this.id);" onblur="setbg('#f0f5e6',this.id)"
                              onKeyDown="textCounter(this.form.message,this.form.msgLen,160)"
                              onKeyUp="textCounter(this.form.message,this.form.msgLen,160)"></textarea><br/>
                    <input type="text" id="msgLen" name="msgLen" size="2" maxlength="3" value="160" readonly/>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input class="btn btn-primary" type="submit" id="send" value="Send"/>
                    <input class="btn btn-default" name="save_template" type="submit" id="save_template"
                           value="Save as template"/>
                </td>
            </tr>
        </table>
    </form>
{/block}



{block name=footer}
    <script type="text/javascript">

        // hook onsubmit
        $('#smsform').on('submit', function(e){

            e.preventDefault();

            var buttonId = $(this).find("input[type=submit]:focus").attr('id');

            if($('#msgtonumber').val() == "")
            {
                window.alert('Receiver number is compulsory');
                return;
            }

            if(buttonId == "save_template")
            {
                //skip check for send message
                $('<input>').attr({
                    type: 'hidden',
                    value: 'true',
                    name: 'save_template'
                }).appendTo('#smsform');
                this.submit();
                return;
            }

            var r = confirm("Are you sure to send the message?");
            if (r == true) {
                $('<input>').attr({
                    type: 'hidden',
                    value: 'true',
                    name: 'send'
                }).appendTo('#smsform');
                this.submit();
            }
        });

        function textCounter(field,countfield,maxlimit)
        {
            if(field.value.length>maxlimit)
            {
                field.value=field.value.substring(0,maxlimit)
            }
            else
            {
                countfield.value=maxlimit-field.value.length
            }
        }

        function setbg(color,id)
        {
            document.getElementById(id).style.background=color
        }
    </script>
{/block}