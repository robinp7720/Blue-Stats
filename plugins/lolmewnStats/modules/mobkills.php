<table class="table" id="mob-kills">
    <thead>
    <th>Victim</th>
    <th>Weapon</th>
    <th>World</th>
    <th>Count</th>
    </thead>
    <tbody>
    <?php
    foreach ($plugin->getStat('kill', $player->uuid, False) as $stat):?>
        <tr>
			<td><?=$stat['entityType']?></td>
            <td><?=$stat['weapon']?></td>
            <td><?=$stat['world']?></td>
			<td><?=$stat['value']?></td>
            
        </tr>
    <?php endforeach; ?>
    </tbody>
</table><script>
    $(document).ready(function () {
        $('#mob-kills').DataTable();
    });
</script>
