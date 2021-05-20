<?php

/**
 * Event Rankings
 * https://webenginecms.org/
 * 
 * @version 1.1.0
 * @author Lautaro Angelico <http://lautaroangelico.com/>
 * @copyright (c) 2013-2019 Lautaro Angelico, All Rights Reserved
 * @build 78769f14e4ba7592617243b8d4529015
 */

function saveChanges()
{
    global $_POST;

    $xmlPath = __PATH_EVENTRANKINGS_ROOT__ . 'config.xml';
    $xml = simplexml_load_file($xmlPath);

    if (!is_writable($xmlPath)) throw new Exception('The configuration file is not writable.');

    if (!Validator::UnsignedNumber($_POST['setting_1'])) throw new Exception('Submitted setting is not valid (results_limit)');
    $xml->results_limit = $_POST['setting_1'];

    if (!Validator::UnsignedNumber($_POST['setting_4'])) throw new Exception('Submitted setting is not valid (show_level)');
    if (!in_array($_POST['setting_4'], array(1, 0))) throw new Exception('Submitted setting is not valid (show_level)');
    $xml->show_level = $_POST['setting_4'];

    if (!Validator::UnsignedNumber($_POST['setting_5'])) throw new Exception('Submitted setting is not valid (show_class)');
    if (!in_array($_POST['setting_5'], array(1, 0))) throw new Exception('Submitted setting is not valid (show_class)');
    $xml->show_class = $_POST['setting_5'];

    if (!Validator::UnsignedNumber($_POST['setting_6'])) throw new Exception('Submitted setting is not valid (live_ranking)');
    if (!in_array($_POST['setting_6'], array(1, 0))) throw new Exception('Submitted setting is not valid (live_ranking)');
    $xml->live_ranking = $_POST['setting_6'];

    $xml->excluded_players = $_POST['setting_7'];

    $save = @$xml->asXML($xmlPath);
    if (!$save) throw new Exception('There has been an error while saving changes.');
}

if (check_value($_POST['submit_changes'])) {
    try {

        saveChanges();
        message('success', 'Settings successfully saved.');
    } catch (Exception $ex) {
        message('error', $ex->getMessage());
    }
}


// load configs
$pluginConfig = simplexml_load_file(__PATH_EVENTRANKINGS_ROOT__ . 'config.xml');
if (!$pluginConfig) throw new Exception('Error loading config file.');
?>
<h2>Event Rankings Settings</h2>

<h4>Rankings Home</h4>
<p><?php echo '<a href="' . __PATH_MODULES_RANKINGS__ . 'bloodcastle/" target="_blank">' . __PATH_MODULES_RANKINGS__ . 'bloodcastle/</a>'; ?></p>
<p><?php echo '<a href="' . __PATH_MODULES_RANKINGS__ . 'devilsquare/" target="_blank">' . __PATH_MODULES_RANKINGS__ . 'devilsquare/</a>'; ?></p>
<p><?php echo '<a href="' . __PATH_MODULES_RANKINGS__ . 'chaoscastle/" target="_blank">' . __PATH_MODULES_RANKINGS__ . 'chaoscastle/</a>'; ?></p>
<form action="" method="post">

    <table class="table table-striped table-bordered table-hover module_config_tables">
        <tr>
            <th>Results Limit<br /><span>Number of players to show in the ranking.</span></th>
            <td>
                <input class="form-control" type="text" name="setting_1" value="<?php echo $pluginConfig->results_limit; ?>" />
            </td>
        </tr>
        <tr>
            <th>Show Player Level<br /><span></span></th>
            <td>
                <?php enabledisableCheckboxes('setting_4', $pluginConfig->show_level, 'Enabled', 'Disabled'); ?>
            </td>
        </tr>
        <tr>
            <th>Show Player Class<br /><span></span></th>
            <td>
                <?php enabledisableCheckboxes('setting_5', $pluginConfig->show_class, 'Enabled', 'Disabled'); ?>
            </td>
        </tr>
        <tr>
            <th>Enable Live Ranking Data<br /><span>If enabled, cache will not be used and ranking data will be loaded directly from the database on each request.</span></th>
            <td>
                <?php enabledisableCheckboxes('setting_6', $pluginConfig->live_ranking, 'Enabled', 'Disabled'); ?>
            </td>
        </tr>
        <tr>
            <th>Excluded Players<br /><span>Separated by comma.<br /><br />Example:<br />player1,player2,player3</span></th>
            <td>
                <input class="form-control" type="text" name="setting_7" value="<?php echo $pluginConfig->excluded_players; ?>" />
            </td>
        </tr>

        <tr>
            <td colspan="2"><input type="submit" name="submit_changes" value="Save Changes" class="btn btn-success" /></td>
        </tr>
    </table>
</form>