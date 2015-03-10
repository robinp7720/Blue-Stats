<article>
	<h2>Kills</h2>
	<table class="table table-striped table-bordered" id="kills">
		<thead>
			<tr>
				<th>Killer</th>
				<?php if($config[$serverId]["server"]["query_enabled"]){echo"<th>Status</th>";}?>
				<th>World</th>
				<th>Killed</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th>Killer</th>
				<?php if($config[$serverId]["server"]["query_enabled"]){echo"<th>Status</th>";}?>
				<th>World</th>
				<th>Killed</th>
				<th>Amount</th>
			</tr>
		</tfoot>
	</table>
</article>
<script type="text/javascript">
	$(document).ready(function() {
	    $('#kills').dataTable( {
	    	<?php /* If url rewrites have been disabled */ if ($config[$serverId]["url"]["rewrite"]==false) :?>
	        "ajax": './ajax/call.php?func=kills',
	        <?php else: ?>
	        "ajax": '<?=$config[$serverId]["url"]["base"]?>/ajax/?func=kills',
	        <?php endif; ?>
	         responsive: true
	    } );
	} );		
</script>