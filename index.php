<?php
/**
 * Copyright 2005-2011 MERETHIS
 * Centreon is developped by : Julien Mathis and Romain Le Merlus under
 * GPL Licence 2.0.
 *
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation ; either version 2 of the License.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
 * PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see <http://www.gnu.org/licenses>.
 *
 * Linking this program statically or dynamically with other modules is making a
 * combined work based on this program. Thus, the terms and conditions of the GNU
 * General Public License cover the whole combination.
 *
 * As a special exception, the copyright holders of this program give MERETHIS
 * permission to link this program with independent modules to produce an executable,
 * regardless of the license terms of these independent modules, and to copy and
 * distribute the resulting executable under terms of MERETHIS choice, provided that
 * MERETHIS also meet, for each linked independent module, the terms  and conditions
 * of the license of that module. An independent module is a module which is not
 * derived from this program. If you modify this program, you may extend this
 * exception to your version of the program, but you are not obliged to do so. If you
 * do not wish to do so, delete this exception statement from your version.
 *
 * For more information : contact@centreon.com
 *
 */

ini_set("display_errors", "Off");

require_once "../require.php";
require_once $centreon_path . 'www/class/centreon.class.php';
require_once $centreon_path . 'www/class/centreonSession.class.php';
require_once $centreon_path . 'www/class/centreonDB.class.php';
require_once $centreon_path . 'www/class/centreonWidget.class.php';

session_start();

if (!isset($_SESSION['centreon']) || !isset($_REQUEST['widgetId'])) {
    exit;
}
$centreon = $_SESSION['centreon'];
$widgetId = $_REQUEST['widgetId'];

 try {
    global $pearDB;

    $db = new CentreonDB();
    $db2 = new CentreonDB("centstorage");
    $pearDB = $db;

    if ($centreon->user->admin == 0) {
        $access = new CentreonACL($centreon->user->get_id());
        $grouplist = $access->getAccessGroups();
        $grouplistStr = $access->getAccessGroupsString();
    }

    $widgetObj = new CentreonWidget($centreon, $db);
    $preferences = $widgetObj->getWidgetPreferences($widgetId);
    $autoRefresh = 0;
    if (isset($preferences['refresh_interval'])) {
        $autoRefresh = $preferences['refresh_interval'];
    }
    
    /*
     * Check ACL
     */
    $acl = 1;
    if (isset($tab[0]) && isset($tab[1]) && $centreon->user->admin == 0) {
        $query = "SELECT host_id FROM centreon_acl WHERE host_id = ".$db->escape($tab[0])." AND service_id = ".$db->escape($tab[1])." AND group_id IN (".$grouplistStr.")";
        $res = $db2->query($query);
        if (!$res->numRows()) {
            $acl = 0;
        }
    }
} catch (Exception $e) {
    echo $e->getMessage() . "<br/>";
    exit;
}
?>
<html>
<style type="text/css">
         body{ margin:0; padding:0 0 0 0; }
         div#actionBar { position:absolute; top:0; left:0; width:100%; height:25px; background-color: #FFFFFF; }
         @media screen { body>div#actionBar { position: fixed; } }
         * html body { overflow:hidden ; text-align:center;}

    </style>
    <head>
		<link href="resources/c3.css" rel="stylesheet" type="text/css">
		<link href="resources/test.css" rel="stylesheet" type="text/css">

		<!-- Load d3.js and c3.js -->
		<script src="resources/d3.v3.min.js" charset="utf-8"></script>
		<script src="resources/c3.min.js"></script>    

       <link href="../../Themes/Centreon-2/style.css" rel="stylesheet" type="text/css"/>
        <link href="../../Themes/Centreon-2/jquery-ui/jquery-ui.css" rel="stylesheet" type="text/css"/>
        <link href="../../Themes/Centreon-2/jquery-ui/jquery-ui-centreon.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="../../include/common/javascript/jquery/jquery.js"></script>
        <script type="text/javascript" src="../../include/common/javascript/jquery/jquery-ui.js"></script>
        <script type="text/javascript" src="../../include/common/javascript/widgetUtils.js"></script>

	</head>
     <body>
<div id="chart4" ></div>
<script type="text/javascript">

var chart = c3.generate({
    bindto: '#chart4',
					   size: {
				height: 300,
				width: 600
			},
    data: {
		x: 'x',
      columns: [
			['x', '2015-04-01', '2015-05-01', '2015-06-01', '2015-07-01', '2015-08-01', '2015-09-01'],
			['Availability', 99.56, 99.56, 100, 100, 50, 86.32],
			['Alerts', 10, 20, 0, 0, 50, 3]
      ],
      axes: {
        Alerts: 'y2'
      },
      types: {
        Alerts: 'bar' // ADD
      }
    },
    axis: {
	 x:{
            type: 'timeseries',
            tick: {
                format: '%Y-%m'
            }
      }


    }
});
</script>

<script type="text/javascript">
jQuery(function() {
                parent.iResize(window.name, 300);
                jQuery(window).resize(function() {
                //     reload();
                });
           });
</script>

	</body>
</html>
