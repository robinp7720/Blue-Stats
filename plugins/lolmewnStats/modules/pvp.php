<table class="table table-sorted" id="pvp-stats">
    <thead>
    <th>Victim</th>
    <th>Weapon</th>
    <th>World</th>
    <th>Time</th>
    </thead>
    <tbody>
    <?php

    foreach ($plugin->getStat('pvp', $player->uuid, False) as $stat):?>
        <tr>
            <td><?=$plugin->getUserName($stat['victim'])?></td>
            <td><?=$stat['weapon']?></td>
            <td><?=$stat['world']?></td>
            <td><?=$stat['time']?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<script>
    $(document).ready(function () {
        $('#pvp-stats').DataTable();
    });
</script>
