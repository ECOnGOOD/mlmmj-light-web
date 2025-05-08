<html>
    <head>
        <title>Edit list | {$headline}</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <script>
            //{literal} Do not use Smarty here
            function validate_form()
            {
                var prefix = document.getElementById('prefix').value;
                var subscribers = document.getElementById('subscribers').value;
                var moderators = document.getElementById('moderators').value;

                // Regex for a valid e-mail
                var re_email = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

                // Transform subscribers and moderators into array
                subscribers = subscribers.split("\n");
                moderators = moderators.split("\n");

                if (prefix.length > 128)
                {
                    alert("A prefix length can not be longer than 128 characters.");
                    return false;
                }

                for(var i in subscribers)
                {   
                    if ( subscribers[i] != "" && !re_email.test(subscribers[i]) )
                    {
                        alert('Subscriber "' + subscribers[i] + '" (see line #' + (parseFloat(i)+1) + ') has an incorrect email address.');
                        return false;
                    }
                }

                for(var i in moderators)
                {   
                    if ( moderators[i] != "" && !re_email.test(moderators[i]) )
                    {
                        alert('Moderator "' + moderators[i] + '" (see line #' + (parseFloat(i)+1) + ') has an incorrect email address.');
                        return false;
                    }
                }
            }
            //{/literal}
        </script>
    </head>
    <body onload="document.getElementById('subscribers').value = document.getElementById('subscribers').value.split('\n').sort().join('\n'); document.getElementById('moderators').value = document.getElementById('moderators').value.split('\n').sort().join('\n');">
        <div id="header">
            <div id="header_left">
                <a href="{$web_url}">{$headline}</a>
            </div>
            <div id="header_right">
                <a href="logout.php">Logout ({$username})</a>
            </div>
        </div>
        <div id="breadcrumbs">
            <a href="index.php">Home</a>&nbsp;/&nbsp;{$list_name}
        </div>
        <div style="width: 75%; border: 2px solid #000; margin: 0 auto 30px; text-align: center; padding: 20px; border-radius: 10px; background-color: #FFF7A4; border-color: #C1AE00;">
            Please be aware that you need the user's consent to receive mails from the list <strong>before</strong> you add him to the list of subscribers.<br />This tool <strong>won't send a double opt-in message</strong> to new subscribers automatically.
        </div>
        {if $success eq true}<p class="success">List was successfully updated.</p>{/if}
        <form method="post" action="save_list.php" id="save_list" onsubmit="return validate_form()">
            <div id="edit_page">
                <input type="hidden" name="list_name" value="{$list_name}">
                <div id="column_left">
                    <div id="subscribers_header">
                        Subscribers:&nbsp;
                        <div class="tooltip">
                            <img src="help.svg" width=15 height=15>
                            <span class="help_sub">
                                Please provide one email address per line.<br /><br />Please be aware that you need the user's consent to receive mails from the list <strong>before</strong> you add him to the list of subscribers. This tool won't send a double opt-in message to new subscribers automatically.
                            </span>
                        </div>
                        &nbsp;|&nbsp;<a href="#" onclick="document.getElementById('subscribers').value = document.getElementById('subscribers').value.split('\n').sort().join('\n'); alert('Subscribers list has been sorted alphabetically.');">A-Z</a>&nbsp;|&nbsp;<a href="#" onclick="alert('Current subscribers count: ' + document.getElementById('subscribers').value.trim().split('\n').length);">Count</a>
                    </div>
                    <div id="subscribers_body">
                        <textarea name="subscribers" id="subscribers">{$subscribers}</textarea>
                    </div>
                </div>
                <div id="column_middle">
                    <div id="column_middle_inner">
                        <div id="table_div">
                            <table id="table_middle" class="table_middle">
                                <tr>
                                    <td>
                                        <div id="prefix_header">
                                            <div class="tooltip">
                                                <img src="help.svg" width=15 height=15>
                                                <span class="help_prefix">
                                                    The prefix will be added to the subject field of each message.<br /><br />Can be left blank.
                                                </span>
                                            </div>
                                            &nbsp;Prefix:
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="text" name="prefix" value="{$prefix|escape:'htmlall'}" id="prefix">
                                    </td>
                                </tr>
                            </table>
                            <table class="table_middle">
                                <tr>
                                    <td >
                                        <div id="listdescription_header">
                                            <div class="tooltip">
                                                <img src="help.svg" width=15 height=15>
                                                <span class="help_prefix">
                                                    This is the list description which is displayed in the overview.<br /><br />Can be left blank.
                                                </span>
                                            </div>
                                            &nbsp;List description:
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <textarea name="listdescription" id="listdescription" style="height: 100%; width: 100%;">{$listdescription|escape:'htmlall'}</textarea>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div id="save_btn">
                            <input type="submit" name="submit" value="Save">
                        </div>
                    </div>
                </div>
                <div id="column_right">
                    {if $moderators ne NULL}
                        <div id="moderators_header">
                            Moderators:&nbsp;
                            <div class="tooltip">
                                <img src="help.svg" width=15 height=15>
                                <span class="help_mod">
                                    Please provide one email address per line.
                                    In case of a moderated list the messages will be send to these recipients before they get published to the list.<br /><br />Please be aware that you need the user's consent to receive mails from the list <strong>before</strong> you add him to the list of moderators. This tool won't send a double opt-in message to new moderators.
                                </span>
                            </div>
                            &nbsp;|&nbsp;<a href="#" onclick="document.getElementById('moderators').value = document.getElementById('moderators').value.split('\n').sort().join('\n'); alert('Moderators list has been sorted alphabetically.');">A-Z</a>&nbsp;|&nbsp;<a href="#" onclick="alert('Current moderators count: ' + document.getElementById('moderators').value.trim().split('\n').length);">Count</a>
                        </div>
                        <div id="moderators_body">
                            <textarea name="moderators" id="moderators">{$moderators}</textarea>
                        </div>
                    {else}
                        <div id="moderators_header">
                            List is not moderated.
                        </div>
                    {/if}
                </div>
            </div>
        </form>
    </body>
</html>
