{extends file="master.tpl"}

{block name=header}
    <link rel="stylesheet" href="{$publicRootPath}assets/DataTables/media/css/jquery.dataTables.min.css">
{/block}

{block name=title}SMS Templates{/block}

{block name=panelBody}

    {if $flashMessages->hasMessages('success')}
        {$flashMessages->display('success')}
    {/if}

    <form id="templatesForm" method="POST" action="">
        <table id="templatesTable" class="table">
            <thead>
            <th width="2%"></th>
            <th width="10%">Receiver</th>
            <th width="70%">Text</th>
            </thead>
            <tbody>

            {foreach $simpleMessagingArray as $simpleMessaging}
                <tr>
                    <td><input name="filename[]" value="{$simpleMessaging->getFileName()}" type="checkbox"></td>
                    <td>{$simpleMessaging->getToNumber()}</td>
                    <td>{$simpleMessaging->getText()}</td>
                </tr>
            {/foreach}

            </tbody>


        </table>
        <input class="btn btn-danger" type="submit" id="btnDelete" value="Delete"  />
        <input class="btn btn-warning" type="submit" id="btnSend" value="Send"  />
        <input class="btn btn-primary" id="select-all" type="button"  value="Select All"  />
    </form>
{/block}



{block name=footer}
    <script type="text/javascript" src="{$publicRootPath}assets/DataTables/media/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){    $('#templatesTable').DataTable(); } );

        // Listen for click on select all checkbox
        $('#select-all').click(function(event) {

            // Iterate each checkbox
            $(':checkbox').each(function() {
                this.checked = true;
            });
        });

        // hook onsubmit
        $('#templatesForm').on('submit', function(e){
            e.preventDefault();

            var buttonId = $(this).find("input[type=submit]:focus").attr('id');
            var r = false;

            if(buttonId == 'btnDelete')
            {
                $('<input>').attr({
                    type: 'hidden',
                    value: 'true',
                    name: 'delete'
                }).appendTo('#templatesForm');

                r = confirm("Are you sure to delete the messages?");

            }
            else if(buttonId == 'btnSend')
            {
                $('<input>').attr({
                    type: 'hidden',
                    value: 'true',
                    name: 'send'
                }).appendTo('#templatesForm');

                r = confirm("Are you sure want to send the message?");
            }

            if (r == true) {
                this.submit();
            }

        });

    </script>

{/block}