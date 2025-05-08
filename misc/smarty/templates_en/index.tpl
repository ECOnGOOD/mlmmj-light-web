<html>
    <head>
        <title>Home | {$headline}</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <script>
            function validate_form()
            {
                var name = document.getElementById('add_list_input').value;
                var name = name.toLowerCase();

                if (name == "")
                {
                    return false;
                }

                if (name.length > 30)
                {
                    alert("Mailing list name must not be longer than 30 characters.");
                    return false;
                }

                if ( name.match(/[^a-z0-9_]/) )
                {
                    alert("Mailing list name must contain only english letters, digits and undercores.");
                    return false;
                }
            }
        </script>
    </head>
    <body>
        <div id="header">
            <div id="header_left">
                <a href="{$web_url}">{$headline}</a>
            </div>
            <div id="header_right">
                <a href="logout.php">Logout ({$username})</a>
            </div>
        </div>
        <div id="breadcrumbs">Home</div>
        <div id="index">
            <div id="lists_header">
                <b>All available mailing lists</b>
            </div>
            <table id="lists">
                <tr>
                    <td>
                        &starf;
                    </td>
                    <td style="font-weight: bold;">
                        Lists you own (editable)&nbsp;
                        <div class="tooltip">
                            <img src="help.svg" width=15 height=15>
                            <span class="help_add_list">
                                You can edit the following mailing lists as you own them. Just click on its name.
                            </span>
                        </div>
                    </td>
                </tr>

                {foreach $lists as $list}
                    {if $list.iamowner == 1}
                        <tr>
                            <td>
                                &check;
                            </td>
                            <td>
                                <a href="edit_list.php?list_name={$list@key}">{$list@key}</a>
                                <div class="tooltip">
                                    <img src="info.svg" width=15 height=15>
                                    <span class="help_add_list">
                                        <strong>Description</strong><br />{$list.description}<br /><br /><strong>List owner(s)</strong><br />{foreach $list.owners as $owner}{$owner}<br />{/foreach}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    {/if}
                {/foreach}

                <tr>
                    <td>
                        &nbsp;
                    </td>
                    <td>
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td>
                        &starf;
                    </td>
                    <td style="font-weight: bold;">
                        All other lists (not editable)&nbsp;
                        <div class="tooltip">
                            <img src="help.svg" width=15 height=15>
                            <span class="help_add_list">
                                You can not edit the following mailing lists as you don't own them.
                            </span>
                        </div>
                    </td>
                </tr>

                {foreach $lists as $list}
                    {if $list.iamowner == 0}
                        <tr>
                            <td>
                                &cross;
                            </td>
                            <td>
                                {$list@key}
                                <div class="tooltip">
                                    <img src="info.svg" width=15 height=15>
                                    <span class="help_add_list">
                                        <strong>Description</strong><br />{$list.description}<br /><br /><strong>List owner(s)</strong><br />{foreach $list.owners as $owner}{$owner}<br />{/foreach}
                                    </span>
                                </div>
                            </td>
                        </tr>
                    {/if}
                {/foreach}
            </table>
            <br />
            <span>Loading time: {$loadingtime} seconds</span>
        </div>
    </body>
</html>
