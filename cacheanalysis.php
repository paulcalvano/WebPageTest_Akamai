<?php
include 'common.inc';
include 'cacheanalysis.inc';

$page_keywords = array('Domains','Webpagetest','Website Speed Test');
$page_description = "Website cache analysis $testLabel";
?>
<!DOCTYPE html>
<html>
    <head>
        <title>WebPagetest Domain Breakdown<?php echo $testLabel; ?></title>
        <?php $gaTemplate = 'Cache Analysis'; include ('head.inc'); ?>
        <style type="text/css">
        div.bar {
            height:12px;
            margin-top:auto;
            margin-bottom:auto;
        }

        .left {text-align:left;}
        .center {text-align:center;}

        .indented1 {padding-left: 40pt;}
        .indented2 {padding-left: 80pt;}

        td {
            white-space:nowrap;
            text-align:left;
            vertical-align:middle;
        }

        td.center {
            text-align:center;
        }

        table.details {
          margin-left:auto; margin-right:auto;
          background: whitesmoke;
          border-collapse: collapse;
        }
        table.details th, table.details td {
          border: 1px silver solid;
          padding: 0.2em;
          text-align: center;
          font-size: smaller;
        }
        table.details th {
          background: gainsboro;
        }
        table.details caption {
          margin-left: inherit;
          margin-right: inherit;
          background: whitesmoke;
        }
        table.details th.reqUrl, table.details td.reqUrl {
          text-align: left;
          width: 30em;
          word-wrap: break-word;
        }
        table.details td.even {
          background: gainsboro;
        }
        table.details td.odd {
          background: whitesmoke;
        }
        table.details td.evenRender {
          background: #dfffdf;
        }
        table.details td.oddRender {
          background: #ecffec;
        }
        table.details td.evenDoc {
          background: #dfdfff;
        }
        table.details td.oddDoc {
          background: #ececff;
        }
        table.details td.warning {
          background: #ffff88;
        }
        table.details td.error {
          background: #ff8888;
        }
        .header_details {
            display: none;
        }
        .a_request {
            cursor: pointer;
        }
        </style>
    </head>
    <body>
        <div class="page">
            <?php
            $tab = 'Test Result';
            $subtab = 'Cache Analysis';
            include 'header.inc';
            ?>
			<div class="center">
			<table id="tableDetails" class="details center">
				<caption>Request Details</caption>
			    <thead>
				<tr>
					<th class="reqNum">#</th>
					<th class="reqUrl">Resource</th>
          <th class="reqCPCODE">CPCODE</th>
          <th class="reqRespCode">Resp Code</th>
					<th class="reqLastModified">Last Modified</th>
					<th class="reqTimeSinceMod">Time Since Modified</th>
					<th class="reqCacheable">Cacheable</th>
					<th class="reqTTL">Akamai Cache Time</th>
					<th class="reqExpiresIn">Expires In?</th>
					<th class="reqExpires">Expires</th>
					<th class="reqCache-Control">Cache-Control</th>
                                        <th class="reqCache-Control">REDbot</th>
				</tr>
			    </thead>
            </tbody>

<?php
// loop through all of the requests and spit out a data table
$requests = getRequests($id, $testPath, $run, @$_GET['cached'], $secure, $haveLocations, true, true);
foreach($requests as $reqNum => $request)
{
	if($request)
	{
		echo '<tr>';

        $requestNum = $reqNum + 1;

		$highlight = '';
		$result = (int)$request['responseCode'];
		if( $result != 401 && $result >= 400)
			$highlight = 'error ';
		elseif ( $result >= 300)
			$highlight = 'warning ';

		if( (int)$requestNum % 2 == 1)
			$highlight .= 'odd';
		else
			$highlight .= 'even';

		if( $request['load_start'] < $data['render'])
			$highlight .= 'Render';
		elseif ( $request['load_start'] < $data['docTime'])
			$highlight .= 'Doc';

    echo '<td class="reqNum ' . $highlight . '">' . $requestNum . '</td>';
       
		if( $request['host'] || $request['url'] )
		{
			$protocol = 'http://';
			if( $request['is_secure'] && $request['is_secure'] == 1)
				$protocol = 'https://';
			$url = $protocol . $request['host'] . $request['url'];
            $displayurl = ShortenUrl($url);
            if ($settings['nolinks']) {
                echo "<td class=\"reqUrl $highlight\"><a title=\"$url\" href=\"#request$requestNum\">$displayurl</a></td>";
            } else {
			    echo '<td class="reqUrl ' . $highlight . '"><a rel="nofollow" href="' . $url .  '">' . $displayurl . '</a></td>';
            }
		}
		else
			echo '<td class="reqUrl ' . $highlight . '">-</td>';
echo '<td class="reqCPCODE ' . $highlight . '">' . $request['cpcode'] . '</td>';
   if( array_key_exists('responseCode', $request) && $request['responseCode'])
           echo '<td class="respCode ' . $highlight . '">' . $request['responseCode'] . '</td>';
   else
           echo '<td class="respCode ' . $highlight . '">-</td>';

    echo '<td class="reqLastModified ' . $highlight . '">' . $request['last-modified'] . '</td>';


$dateString = strtotime($request['last-modified']);
$now = strtotime($request['date']);
$timeSinceModified="";

$timeDelta = $now - $dateString;

if($timeDelta>0 && $timeDelta < 60) $timeSinceModified= $timeDelta/60 .'s';
else if($timeDelta >= 60 && $timeDelta < 3600) $timeSinceModified= round($timeDelta/60) .'m';
else if($timeDelta >= 3600 && $timeDelta < 86400) $timeSinceModified= round($timeDelta/3600) .'h';
else if($timeDelta >= 86400) $timeSinceModified= round($timeDelta/86400) .'d';

echo '<td class="reqTimeSinceMod ' . $highlight . '">' . $timeSinceModified . '</td>';
echo '<td class="reqCacheable ' . $highlight . '">' . $request['cacheable'] . '</td>';
echo '<td class="reqTTL ' . $highlight . '">' . $request['ttl'] . '</td>';
}

$dateString = strtotime($request['expires']);
$now = strtotime($request['date']);
$expiresIn="";

$timeDelta = $dateString - $now;
if ($timeDelta<=0) $expiresIn= 'expired';
else if($timeDelta>0 && $timeDelta < 60) $expiresIn= $timeDelta/60 .'s';
else if($timeDelta >= 60 && $timeDelta < 3600) $expiresIn= round($timeDelta/60) .'m';
else if($timeDelta >= 3600 && $timeDelta < 86400) $expiresIn= round($timeDelta/3600) .'h';
else if($timeDelta >= 86400) $expiresIn= round($timeDelta/86400) .'d';

echo '<td class="reqExpiresIn ' . $highlight . '">' . $expiresIn . '</td>';
echo '<td class="reqExpires ' . $highlight . '">' . $request['expires'] . '</td>';
echo '<td class="reqCache-Control ' . $highlight . '">' . $request['cacheControl'] . '</td>';
echo '<td class="reqRedBot ' . $highlight . '"><a href="https://redbot.org/?uri=' . urlencode($protocol . $request['host'] . "/" . $request['url']) . '">REDbot</a></td>';

		echo '</tr>';
}
?>
</table></div> 
            <?php include('footer.inc'); ?>
        </div>


        <script language="JavaScript">
        $(document).ready(function() { $("#tableDetails").tablesorter({
            headers: { 3: { sorter:'currency' } ,
                       4: { sorter:'currency' } ,
                       5: { sorter:'currency' } ,
                       6: { sorter:'currency' } ,
                       7: { sorter:'currency' } ,
                       8: { sorter:'currency' } ,
                       9: { sorter:'currency' }
                     }
        }); } );

        </script>
    </body>
</html>

